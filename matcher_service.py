# matcher_service.py
from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np

app = Flask(__name__)
CORS(app)

# Paths (update if needed)
JOB_IT = "upwork_jobs_with_skills.csv"
JOB_INFORMAL = "informal_jobs_with_skills.csv"
WORKERS_CSV = "workers_with_skills.csv"

# Load processed CSVs (assume you already generated these)
jobs_it = pd.read_csv(JOB_IT, encoding="utf-8", low_memory=False)
jobs_informal = pd.read_csv(JOB_INFORMAL, encoding="utf-8", low_memory=False)
workers = pd.read_csv(WORKERS_CSV, encoding="utf-8", low_memory=False)

# Combine jobs into a single dataframe
jobs = pd.concat([jobs_it, jobs_informal], ignore_index=True)

# prepare skills_text columns (same format used in your matcher)
def to_skills_text(s):
    if pd.isna(s):
        return ""
    if isinstance(s, str):
        return s
    return " ".join(s) if hasattr(s, "__iter__") else str(s)

jobs["skills_text"] = jobs["extracted_skills"].fillna("[]").astype(str).str.strip("[]").str.replace("'", "")
workers["skills_text"] = workers["extracted_skills"].fillna("[]").astype(str).str.strip("[]").str.replace("'", "")

# Vectorize once at startup (vocab built from current datasets)
vectorizer = TfidfVectorizer()
all_texts = pd.concat([jobs["skills_text"], workers["skills_text"]]).astype(str)
X = vectorizer.fit_transform(all_texts)

job_vecs = X[:len(jobs)]
worker_vecs = X[len(jobs):]

@app.route("/health")
def health():
    return jsonify({"status": "ok", "jobs": len(jobs), "workers": len(workers)})

@app.route("/match_job", methods=["POST"])
def match_job():
    data = request.get_json(force=True)
    top_n = int(data.get("top_n", 5))

    # Allow either job_index (existing) or ad-hoc skills_text
    if "job_index" in data:
        idx = int(data["job_index"])
        if idx < 0 or idx >= len(job_vecs):
            return jsonify({"error": "job_index out of range"}), 400
        qvec = job_vecs[idx]
    else:
        skills_text = data.get("skills_text", "")
        qvec = vectorizer.transform([skills_text])

    sims = cosine_similarity(qvec, worker_vecs)[0]
    top_idx = np.argsort(sims)[::-1][:top_n]
    results = []
    for i in top_idx:
        results.append({
            "worker_index": int(i),
            "similarity": float(sims[i]),
            "clean_text": str(workers.loc[i, "clean_text"]) if "clean_text" in workers.columns else "",
            "skills_text": str(workers.loc[i, "skills_text"])
        })
    return jsonify({"results": results})

@app.route("/match_worker", methods=["POST"])
def match_worker():
    data = request.get_json(force=True)
    top_n = int(data.get("top_n", 5))

    if "worker_index" in data:
        idx = int(data["worker_index"])
        if idx < 0 or idx >= len(worker_vecs):
            return jsonify({"error": "worker_index out of range"}), 400
        qvec = worker_vecs[idx]
    else:
        skills_text = data.get("skills_text", "")
        qvec = vectorizer.transform([skills_text])

    sims = cosine_similarity(qvec, job_vecs)[0]
    top_idx = np.argsort(sims)[::-1][:top_n]
    results = []
    for i in top_idx:
        results.append({
            "job_index": int(i),
            "similarity": float(sims[i]),
            "title": str(jobs.loc[i, "title"]) if "title" in jobs.columns else "",
            "description": str(jobs.loc[i, "description"])[:500]
        })
    return jsonify({"results": results})

if __name__ == "__main__":
    print("Starting matcher service: jobs:", len(jobs), "workers:", len(workers))
    app.run(host="0.0.0.0", port=5000, debug=False)
