# predict_debug.py
import pandas as pd
import joblib

# --------------------------
# Load model + vectorizer
# --------------------------
print("Loading trained Random Forest model + TF-IDF vectorizer...")
rf = joblib.load("random_forest_model.pkl")
vectorizer = joblib.load("tfidf_vectorizer.pkl")
print("Loaded model: random_forest_model.pkl")
print("Loaded vectorizer: tfidf_vectorizer.pkl")

# --------------------------
# Function to standardize datasets
# --------------------------
def standardize(df, source_name):
    cols = {c.lower().strip(): c for c in df.columns}

    title = df[cols["job title"]] if "job title" in cols else df[cols["title"]] if "title" in cols else pd.Series([""] * len(df))
    description = df[cols["job description"]] if "job description" in cols else df[cols["description"]] if "description" in cols else pd.Series([""] * len(df))
    skills = df[cols["skills"]] if "skills" in cols else pd.Series([""] * len(df))

    df_std = pd.DataFrame({
        "title": title.astype(str),
        "description": description.astype(str),
        "skills": skills.astype(str),
    })
    df_std["source"] = source_name
    return df_std

# --------------------------
# Load datasets
# --------------------------
def standardize_and_merge():
    df_dataset = pd.read_csv("datasets/dataset.csv")
    print(f"Loaded {len(df_dataset)} rows from dataset.csv as IT")
    df_dataset = standardize(df_dataset, "IT")

    df_informal = pd.read_csv("datasets/kenya_informal_jobs.csv")
    print(f"Loaded {len(df_informal)} rows from kenya_informal_jobs.csv as Informal")
    df_informal = standardize(df_informal, "Informal")

    df_upwork = pd.read_csv("datasets/upwork-jobs.csv")
    print(f"Loaded {len(df_upwork)} rows from upwork-jobs.csv as Upwork")
    df_upwork = standardize(df_upwork, "Upwork")

    df_all = pd.concat([df_dataset, df_informal, df_upwork], ignore_index=True)
    df_all = df_all.fillna("")
    df_all["text"] = df_all["title"] + " " + df_all["description"] + " " + df_all["skills"]

    return df_all

# --------------------------
# Debug Prediction Function
# --------------------------
def debug_predict(job_text):
    print("\n----------------------------------------")
    print(f"Job Description: {job_text}")

    vec = vectorizer.transform([job_text])
    pred = rf.predict(vec)[0]

    print(f" → Predicted Category: {pred}")

    # Debug: show top TF-IDF features
    feature_array = vectorizer.get_feature_names_out()
    tfidf_scores = vec.toarray()[0]
    topn = tfidf_scores.argsort()[-10:][::-1]  # top 10 words

    print(" → Top contributing words (TF-IDF):")
    for i in topn:
        if tfidf_scores[i] > 0:
            print(f"    {feature_array[i]} ({tfidf_scores[i]:.3f})")

# --------------------------
# Main
# --------------------------
def main():
    df_merged = standardize_and_merge()
    print(f"✅ Final merged dataset shape: {df_merged.shape}")

    # Example jobs to debug
    examples = [
        "Looking for a plumber to fix leaking pipes",
        "Need a Python developer for a Django project",
        "Remote freelance project: build a website in WordPress"
    ]

    for job in examples:
        debug_predict(job)

if __name__ == "__main__":
    main()
