import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import ast

# ==========================
# Load processed CSVs
# ==========================
jobs_it = pd.read_csv("upwork_jobs_with_skills.csv", encoding="utf-8")       # IT jobs
jobs_informal = pd.read_csv("informal_jobs_with_skills.csv", encoding="utf-8")   # Informal jobs

# Merge jobs together
all_jobs = pd.concat([jobs_it, jobs_informal], ignore_index=True)

# Workers (only one file, do NOT duplicate)
all_workers = pd.read_csv("workers_with_skills.csv", encoding="utf-8")

print(f"✅ Jobs loaded: {len(all_jobs)}")
print(f"✅ Workers loaded: {len(all_workers)}")

# ==========================
# Convert extracted_skills into clean text
# ==========================
def skills_to_text(skills):
    if pd.isna(skills):
        return ""
    try:
        if isinstance(skills, str):
            parsed = ast.literal_eval(skills) if skills.startswith("[") else skills.split(",")
        else:
            parsed = list(skills)
        return " ".join([s.strip() for s in parsed if s.strip()])
    except Exception:
        return str(skills)

all_jobs["skills_text"] = all_jobs["extracted_skills"].apply(skills_to_text)
all_workers["skills_text"] = all_workers["extracted_skills"].apply(skills_to_text)

# Drop jobs with no extracted skills (to avoid useless matches)
all_jobs = all_jobs[all_jobs["skills_text"].str.strip() != ""].reset_index(drop=True)

print(f"✅ Jobs after cleaning: {len(all_jobs)}")

# ==========================
# Vectorize skills (TF-IDF)
# ==========================
vectorizer = TfidfVectorizer()
all_skills = pd.concat([all_jobs["skills_text"], all_workers["skills_text"]])

X = vectorizer.fit_transform(all_skills)

job_vecs = X[:len(all_jobs)]
worker_vecs = X[len(all_jobs):]

# ==========================
# Matching functions
# ==========================
def match_workers_to_job(job_index, top_n=5):
    """Return top N workers for a given job index."""
    similarities = cosine_similarity(job_vecs[job_index], worker_vecs)[0]
    top_workers = similarities.argsort()[::-1][:top_n]
    results = all_workers.iloc[top_workers][["clean_text", "extracted_skills"]].copy()
    results["similarity"] = similarities[top_workers]
    return results

def match_jobs_to_worker(worker_index, top_n=5):
    """Return top N jobs for a given worker index."""
    similarities = cosine_similarity(worker_vecs[worker_index], job_vecs)[0]
    top_jobs = similarities.argsort()[::-1][:top_n]
    results = all_jobs.iloc[top_jobs][["title", "description", "extracted_skills"]].copy()
    results["similarity"] = similarities[top_jobs]
    return results

# ==========================
# Interactive Test Mode
# ==========================
if __name__ == "__main__":
    while True:
        choice = input("\nTest (1 = Job → Workers, 2 = Worker → Jobs, q = quit): ").strip()

        if choice == "1":
            job_index = int(input(f"Enter job index (0 to {len(all_jobs)-1}): "))
            print("\n=== Job Details ===")
            print("TITLE:", all_jobs.loc[job_index, "title"])
            print("DESCRIPTION:", str(all_jobs.loc[job_index, "description"])[:200], "...")
            print("REQUIRED SKILLS:", all_jobs.loc[job_index, "extracted_skills"])
            print("\n=== Top Matching Workers ===")
            print(match_workers_to_job(job_index, top_n=5))

        elif choice == "2":
            worker_index = int(input(f"Enter worker index (0 to {len(all_workers)-1}): "))
            print("\n=== Worker Profile ===")
            print("PROFILE:", str(all_workers.loc[worker_index, "clean_text"])[:200], "...")
            print("WORKER SKILLS:", all_workers.loc[worker_index, "extracted_skills"])
            print("\n=== Top Matching Jobs ===")
            print(match_jobs_to_worker(worker_index, top_n=5))

        elif choice.lower() == "q":
            print("Exiting test mode.")
            break
        else:
            print("Invalid choice. Try again.")
