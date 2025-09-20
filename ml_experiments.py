# ml_experiments.py
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier
from sklearn.naive_bayes import MultinomialNB
from sklearn.svm import LinearSVC
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score, classification_report, confusion_matrix
import matplotlib.pyplot as plt
import seaborn as sns
import joblib

# --------------------------
# STEP 1: Standardize Function
# --------------------------
def standardize(df, source_name):
    cols = {c.lower().strip(): c for c in df.columns}

    df_std = pd.DataFrame()
    df_std["title"] = df[cols["job title"]] if "job title" in cols else df[cols["title"]] if "title" in cols else ""
    df_std["description"] = df[cols["job description"]] if "job description" in cols else df[cols["description"]] if "description" in cols else ""
    df_std["skills"] = df[cols["skills"]] if "skills" in cols else ""
    df_std["certifications"] = df[cols["certifications"]] if "certifications" in cols else ""
    df_std["budget"] = df[cols["budget"]] if "budget" in cols else ""
    df_std["hourly_low"] = df[cols["hourly_low"]] if "hourly_low" in cols else ""
    df_std["hourly_high"] = df[cols["hourly_high"]] if "hourly_high" in cols else ""
    df_std["country"] = df[cols["country"]] if "country" in cols else ""
    df_std["source"] = source_name
    return df_std

# --------------------------
# STEP 2: Load datasets
# --------------------------
df_dataset = pd.read_csv("datasets/dataset.csv")
df_informal = pd.read_csv("datasets/kenya_informal_jobs.csv")
df_upwork = pd.read_csv("datasets/upwork-jobs.csv")

df_dataset = standardize(df_dataset, "IT")
df_informal = standardize(df_informal, "Informal")
df_upwork = standardize(df_upwork, "Upwork")

# Merge all datasets
df_all = pd.concat([df_dataset, df_informal, df_upwork], ignore_index=True)

# --------------------------
# STEP 3: Preprocessing
# --------------------------
df_all = df_all.fillna("")  # Fill NaN with empty strings

# Create "text" column (features)
df_all["text"] = (
    df_all["title"].astype(str) + " " +
    df_all["description"].astype(str) + " " +
    df_all["skills"].astype(str)
)

# Label = source
X = df_all["text"]
y = df_all["source"]

# --------------------------
# STEP 4: Train/Test Split
# --------------------------
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)

# --------------------------
# STEP 5: TF-IDF Vectorization
# --------------------------
vectorizer = TfidfVectorizer(stop_words="english", max_features=5000)
X_train_tfidf = vectorizer.fit_transform(X_train)
X_test_tfidf = vectorizer.transform(X_test)

# --------------------------
# STEP 6: Train Multiple Models
# --------------------------
models = {
    "Logistic Regression": LogisticRegression(max_iter=2000),
    "Random Forest": RandomForestClassifier(n_estimators=200, random_state=42),
    "Naive Bayes": MultinomialNB(),
    "SVM": LinearSVC(),
    "KNN": KNeighborsClassifier(n_neighbors=5)
}

results = []

for name, model in models.items():
    model.fit(X_train_tfidf, y_train)
    y_pred = model.predict(X_test_tfidf)

    acc = accuracy_score(y_test, y_pred)
    prec = precision_score(y_test, y_pred, average="weighted", zero_division=0)
    rec = recall_score(y_test, y_pred, average="weighted", zero_division=0)
    f1 = f1_score(y_test, y_pred, average="weighted", zero_division=0)

    results.append((name, acc, prec, rec, f1))

    print(f"\n{name} Performance:")
    print(classification_report(y_test, y_pred, zero_division=0))

# --------------------------
# STEP 7: Show Summary
# --------------------------
df_results = pd.DataFrame(results, columns=["Model", "Accuracy", "Precision", "Recall", "F1-score"])
print("\n=== Summary of Models ===")
print(df_results)

# --------------------------
# STEP 8: Save Best Model (Random Forest)
# --------------------------
rf = RandomForestClassifier(n_estimators=200, random_state=42)
rf.fit(X_train_tfidf, y_train)

joblib.dump(rf, "random_forest_model.pkl")
joblib.dump(vectorizer, "tfidf_vectorizer.pkl")

print("\n✅ Random Forest model + TF-IDF saved successfully!")

# --------------------------
# STEP 9: Confusion Matrix for Random Forest
# --------------------------
y_pred_rf = rf.predict(X_test_tfidf)

print("\n=== Final Random Forest Classification Report ===")
print(classification_report(y_test, y_pred_rf, zero_division=0))

labels = sorted(y_test.unique())
cm = confusion_matrix(y_test, y_pred_rf, labels=labels)

plt.figure(figsize=(6, 5))
sns.heatmap(cm, annot=True, fmt="d", cmap="Blues",
            xticklabels=labels,
            yticklabels=labels)
plt.title("Confusion Matrix - Random Forest")
plt.xlabel("Predicted")
plt.ylabel("Actual")
plt.tight_layout()
plt.savefig("rf_confusion.png", dpi=300)
print("✅ Confusion matrix saved as rf_confusion.png")
plt.show()
