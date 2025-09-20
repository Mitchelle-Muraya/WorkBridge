# skill_based_recommend_full.py
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# ----------------------------
# 1. Load job datasets
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

# Merge
jobs_df = pd.concat([it_df, informal_df, upwork_df], ignore_index=True)
jobs_df.dropna(subset=['job_description'], inplace=True)
print(f"✅ Merged dataset shape: {jobs_df.shape}")

# ----------------------------
# 2. TF-IDF for jobs
# ----------------------------
tfidf_vectorizer_jobs = TfidfVectorizer(max_features=5000, stop_words='english')
job_tfidf_matrix = tfidf_vectorizer_jobs.fit_transform(jobs_df['job_description'].astype(str))

# ----------------------------
# 3. Load worker dataset
# ----------------------------
workers_df = pd.read_csv("datasets/kenyan_worker_profiles.csv")  # worker_name, skills, location, experience

# TF-IDF for worker skills
tfidf_vectorizer_workers = TfidfVectorizer(stop_words='english')
worker_tfidf_matrix = tfidf_vectorizer_workers.fit_transform(workers_df['skills'].astype(str))

# ----------------------------
# 4. Recommendation functions
# ----------------------------
def recommend_jobs_for_worker(worker_skills, top_n=5):
    worker_vec = tfidf_vectorizer_jobs.transform([worker_skills])
    similarity_scores = cosine_similarity(worker_vec, job_tfidf_matrix).flatten()
    top_indices = similarity_scores.argsort()[::-1][:top_n]

    recommendations = []
    for idx in top_indices:
        job = jobs_df.iloc[idx]
        recommendations.append({
            'job_title': job['job_title'],
            'description': job['job_description'],
            'category': job['category'],
            'similarity_score': float(similarity_scores[idx])
        })
    return recommendations

def recommend_workers_for_job(job_skills, job_location=None, top_n=5):
    job_vec = tfidf_vectorizer_workers.transform([job_skills])
    similarity_scores = cosine_similarity(job_vec, worker_tfidf_matrix).flatten()

    job_skills_set = set(job_skills.lower().split(', '))
    recommendations = []

    for idx, skill_sim in enumerate(similarity_scores):
        worker = workers_df.iloc[idx]
        exp_score = min(worker.get('experience', 0), 10) / 10
        loc_score = 0
        if job_location and 'location' in worker and worker['location']:
            loc_score = 1 if worker['location'].lower() == job_location.lower() else 0

        final_score = skill_sim * 0.6 + exp_score * 0.3 + loc_score * 0.1

        worker_skills_set = set(worker['skills'].lower().split(', '))
        matched_skills = job_skills_set.intersection(worker_skills_set)

        recommendations.append({
            'worker_name': worker['worker_name'],
            'skills': worker['skills'],
            'location': worker.get('location', 'N/A'),
            'experience': worker.get('experience', 'N/A'),
            'skill_match': float(skill_sim),
            'final_score': float(final_score),
            'matched_skills': list(matched_skills)
        })

    recommendations.sort(key=lambda x: x['final_score'], reverse=True)
    return recommendations[:top_n]

# ----------------------------
# 5. Interactive terminal
# ----------------------------
if __name__ == "__main__":
    while True:
        print("\n--- Skill-Based Recommendation ---")
        print("1. Recommend jobs for a worker")
        print("2. Recommend workers for a job")
        print("3. Exit")
        choice = input("Enter choice (1/2/3): ")

        if choice == "1":
            skills = input("Enter worker skills (comma separated): ")
            top_n = int(input("How many jobs to recommend? "))
            results = recommend_jobs_for_worker(skills, top_n=top_n)
            print("\nRecommended Jobs:")
            for r in results:
                print(r)

        elif choice == "2":
            skills = input("Enter job skills required (comma separated): ")
            location = input("Enter job location (optional, press Enter to skip): ")
            top_n = int(input("How many workers to recommend? "))
            results = recommend_workers_for_job(skills, job_location=location, top_n=top_n)
            print("\nRecommended Workers:")
            for r in results:
                print(r)

        elif choice == "3":
            print("Exiting...")
            break
        else:
            print("Invalid choice, try again.")
