# recommend.py
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, precision_score, recall_score
from sklearn.metrics.pairwise import cosine_similarity
import pickle

# ----------------------------
# 1. Load datasets
# ----------------------------
it_df = pd.read_csv("datasets/dataset.csv")
informal_df = pd.read_csv("datasets/kenya_informal_jobs.csv")
upwork_df = pd.read_csv("datasets/upwork-jobs.csv")

# Standardize columns
it_df.rename(columns={'Job Description': 'job_description', 'Job Title': 'job_title'}, inplace=True)
informal_df.rename(columns={'Job Description': 'job_description', 'Job Title': 'job_title'}, inplace=True)
upwork_df.rename(columns={'Job Description': 'job_description', 'title': 'job_title'}, inplace=True)

# Add category
it_df['category'] = 'IT'
informal_df['category'] = 'Informal'
upwork_df['category'] = 'Upwork'

# Merge datasets
jobs_df = pd.concat([it_df, informal_df, upwork_df], ignore_index=True)
jobs_df.dropna(subset=['job_description'], inplace=True)
print(f"✅ Merged dataset shape: {jobs_df.shape}")

# ----------------------------
# 2. TF-IDF for job recommendation (ML)
# ----------------------------
tfidf_vectorizer = TfidfVectorizer(max_features=5000, stop_words='english')
X_jobs = tfidf_vectorizer.fit_transform(jobs_df['job_description'])
y_jobs = jobs_df['category']

# Save vectorizer
with open('tfidf_vectorizer.pkl', 'wb') as f:
    pickle.dump(tfidf_vectorizer, f)

# ----------------------------
# 3. Train Random Forest model
# ----------------------------
X_train, X_test, y_train, y_test = train_test_split(X_jobs, y_jobs, test_size=0.2, random_state=42)
rf_job_model = RandomForestClassifier(n_estimators=200, random_state=42)
rf_job_model.fit(X_train, y_train)

# Evaluate
y_pred = rf_job_model.predict(X_test)
print("Random Forest Job Recommendation Model:")
print("Accuracy:", accuracy_score(y_test, y_pred))
print("Precision:", precision_score(y_test, y_pred, average='weighted'))
print("Recall:", recall_score(y_test, y_pred, average='weighted'))

# Save model
with open('rf_job_model.pkl', 'wb') as f:
    pickle.dump(rf_job_model, f)

# ----------------------------
# 4. Load real worker dataset
# ----------------------------
workers_df = pd.read_csv("datasets/kenyan_worker_profiles.csv")  # worker_name, skills, location, experience, past_jobs
skills_vectorizer = TfidfVectorizer(stop_words='english')
skills_vectorizer.fit(workers_df['skills'].astype(str))

# ----------------------------
# 5. Recommendation functions
# ----------------------------

# Jobs recommended for a worker (ML-based)
def recommend_jobs(worker_profile, top_n=5):
    with open('tfidf_vectorizer.pkl', 'rb') as f:
        vectorizer = pickle.load(f)
    with open('rf_job_model.pkl', 'rb') as f:
        model = pickle.load(f)

    worker_vec = vectorizer.transform([worker_profile])
    proba = model.predict_proba(worker_vec)[0]
    top_indices = np.argsort(proba)[::-1][:top_n]

    recommendations = []
    for i in top_indices:
        category = model.classes_[i]
        score = proba[i]
        recommended_jobs = jobs_df[jobs_df['category'] == category].head(top_n)
        for _, row in recommended_jobs.iterrows():
            recommendations.append({
                'job_title': row.get('job_title', 'N/A'),
                'description': row['job_description'],
                'category': category,
                'match_score': float(score)
            })
    return recommendations[:top_n]

# Workers recommended for a job (combined ranking)
def recommend_workers(job_skills, job_location=None, top_n=5):
    job_vec = skills_vectorizer.transform([job_skills])
    worker_vecs = skills_vectorizer.transform(workers_df['skills'].astype(str))
    skill_similarity = cosine_similarity(job_vec, worker_vecs).flatten()

    recommendations = []
    for idx, sim_score in enumerate(skill_similarity):
        worker = workers_df.iloc[idx]
        exp_score = min(worker.get('experience', 0), 10) / 10  # normalize experience
        loc_score = 0
        if job_location and 'location' in worker and worker['location']:
            loc_score = 1 if worker['location'].lower() == job_location.lower() else 0

        final_score = sim_score * 0.6 + exp_score * 0.3 + loc_score * 0.1

        recommendations.append({
            'worker_name': worker['worker_name'],
            'skills': worker['skills'],
            'location': worker.get('location', 'N/A'),
            'experience': worker.get('experience', 'N/A'),
            'skill_match': float(sim_score),
            'final_score': float(final_score)
        })

    recommendations.sort(key=lambda x: x['final_score'], reverse=True)
    return recommendations[:top_n]

# ----------------------------
# 6. Example usage
# ----------------------------
if __name__ == "__main__":
    # Recommend jobs for a worker
    worker_profile = "electrician, plumbing"
    print("Jobs recommended for worker:")
    for job in recommend_jobs(worker_profile):
        print(job)

    # Recommend workers for a posted job
    job_post_skills = "plumbing, electrician, leak fixing"
    job_post_location = "Nairobi"
    print("\nWorkers recommended for job:")
    for worker in recommend_workers(job_post_skills, job_post_location, top_n=5):
        print(worker)
