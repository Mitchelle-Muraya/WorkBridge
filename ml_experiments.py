# ml_experiments.py

import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier
from sklearn.naive_bayes import MultinomialNB
from sklearn.svm import LinearSVC
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import (
    accuracy_score, precision_score, recall_score, f1_score,
    classification_report, confusion_matrix
)
import matplotlib.pyplot as plt
import seaborn as sns
import joblib

# ==========================================================
# STEP 1 — STANDARDIZE DATASETS (makes column names uniform) I cleaned and standardized datasets from diff sources so  that all of them had the same structure
# ==========================================================
def standardize(df, source_name):
    cols = {c.lower().strip(): c for c in df.columns}

    df_std = pd.DataFrame()

    def safe_col(*possible_names):
        """Return the first matching column, else an empty string."""
        for name in possible_names:
            if name.lower() in cols:
                return df[cols[name.lower()]]
        return ""  # return empty string if none found

    df_std["title"] = safe_col("job title", "title")
    df_std["description"] = safe_col("job description", "description")
    df_std["skills"] = safe_col("skills")
    df_std["certifications"] = safe_col("certifications")
    df_std["budget"] = safe_col("budget")
    df_std["hourly_low"] = safe_col("hourly_low")
    df_std["hourly_high"] = safe_col("hourly_high")
    df_std["country"] = safe_col("country")

    df_std["source"] = source_name

    return df_std


# ==========================================================
# STEP 2 — LOAD DATA
# ==========================================================
df_dataset = pd.read_csv("datasets/dataset.csv")
df_informal = pd.read_csv("datasets/kenya_informal_jobs.csv")
df_upwork = pd.read_csv("datasets/upwork-jobs.csv")

df_dataset = standardize(df_dataset, "IT")
df_informal = standardize(df_informal, "Informal")
df_upwork = standardize(df_upwork, "Upwork")

# Merge everything
df_all = pd.concat([df_dataset, df_informal, df_upwork], ignore_index=True)

# ==========================================================
# STEP 2.1 — EDA: JOB CATEGORY DISTRIBUTION
# ==========================================================
plt.figure(figsize=(7, 5))
df_all["source"].value_counts().plot(kind="bar", color=["#00b3ff", "#00c9a7", "#0077cc"])
plt.title("Distribution of Job Categories")
plt.xlabel("Category")
plt.ylabel("Number of Job Descriptions")
plt.tight_layout()
plt.savefig("eda_category_distribution.png", dpi=300)
plt.show()


# ==========================================================
# STEP 3 — PREPROCESSING
# ==========================================================
df_all = df_all.fillna("")

df_all["text"] = (
    df_all["title"].astype(str) + " " +
    df_all["description"].astype(str) + " " +
    df_all["skills"].astype(str)
)

X = df_all["text"]
y = df_all["source"]


# ==========================================================
# STEP 4 — TRAIN/TEST SPLIT I split the data into training and testing  to avoid overfitting.
# ==========================================================
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)


# ==========================================================
# STEP 5 — TF-IDF VECTORIZATION
# ==========================================================
vectorizer = TfidfVectorizer(stop_words="english", max_features=5000)
X_train_tfidf = vectorizer.fit_transform(X_train)
X_test_tfidf = vectorizer.transform(X_test)


# ==========================================================
# STEP 6 — TRAIN MULTIPLE MODELS
# ==========================================================
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

    results.append((
        name,
        accuracy_score(y_test, y_pred),
        precision_score(y_test, y_pred, average="weighted", zero_division=0),
        recall_score(y_test, y_pred, average="weighted", zero_division=0),
        f1_score(y_test, y_pred, average="weighted", zero_division=0)
    ))

    print(f"\n{name} Performance:")
    print(classification_report(y_test, y_pred, zero_division=0))


# ==========================================================
# STEP 7 — SUMMARY OF MODELS
# ==========================================================
df_results = pd.DataFrame(
    results, columns=["Model", "Accuracy", "Precision", "Recall", "F1-score"]
)
print("\n=== SUMMARY OF ALL MODELS ===")
print(df_results)


# ==========================================================
# STEP 8 — SAVE BEST MODEL (Random Forest)
# ==========================================================
best_rf = RandomForestClassifier(n_estimators=200, random_state=42)
best_rf.fit(X_train_tfidf, y_train)

joblib.dump(best_rf, "random_forest_model.pkl")
joblib.dump(vectorizer, "tfidf_vectorizer.pkl")

print("\n✅ Saved Random Forest model & TF-IDF vectorizer!")


# ==========================================================
# STEP 9 — CONFUSION MATRIX
# ==========================================================
y_pred_rf = best_rf.predict(X_test_tfidf)
labels = sorted(y_test.unique())
cm = confusion_matrix(y_test, y_pred_rf, labels=labels)

plt.figure(figsize=(7, 5))
sns.heatmap(
    cm,
    annot=True,
    fmt="d",
    cmap="Blues",
    xticklabels=labels,
    yticklabels=labels
)
plt.title("Confusion Matrix — Random Forest")
plt.xlabel("Predicted")
plt.ylabel("Actual")
plt.tight_layout()
plt.savefig("rf_confusion.png", dpi=300)
plt.show()
