# simulate_train.py
import pandas as pd
import numpy as np
import ast, random, os
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import classification_report, confusion_matrix, roc_auc_score, accuracy_score
import joblib


# ---------- Utility functions ----------
def safe_read_csv(path):
    if not os.path.exists(path):
        print(f"[WARN] File not found: {path}")
        return None
    try:
        return pd.read_csv(path, encoding='utf-8')
    except Exception:
        return pd.read_csv(path, encoding='latin1')

def parse_skills_field(x):
    """Parse many possible formats of extracted_skills into a list of strings."""
    if pd.isna(x):
        return []
    if isinstance(x, list):
        return [str(s).strip() for s in x if str(s).strip()]
    s = str(x).strip()
    if s.startswith('['):
        try:
            parsed = ast.literal_eval(s)
            return [str(it).strip() for it in parsed if str(it).strip()]
        except Exception:
            pass
    parts = [p.strip() for p in s.split(',') if p.strip()]
    return parts

# ---------- Load data ----------
workers = safe_read_csv("workers_with_skills.csv")
jobs_upwork = safe_read_csv("upwork_jobs_with_skills.csv")
jobs_informal = safe_read_csv("informal_jobs_with_skills.csv")

jobs_list = []
if jobs_upwork is not None:
    jobs_list.append(jobs_upwork)
if jobs_informal is not None:
    jobs_list.append(jobs_informal)
if not jobs_list:
    raise SystemExit("No job CSVs found. Place 'upwork_jobs_with_skills.csv' or 'informal_jobs_with_skills.csv' in this folder.")

jobs = pd.concat(jobs_list, ignore_index=True)

# ---------- Normalize skills ----------
for df in [workers, jobs]:
    if df is None:
        continue
    if 'extracted_skills' not in df.columns:
        possible = [c for c in df.columns if 'skill' in c.lower()]
        if possible:
            df['extracted_skills'] = df[possible[0]]
        else:
            df['extracted_skills'] = [[]]

workers['skills_list'] = workers['extracted_skills'].apply(parse_skills_field)
jobs['skills_list'] = jobs['extracted_skills'].apply(parse_skills_field)

workers['skills_text'] = workers['skills_list'].apply(lambda L: " ".join([s.replace(" ", "_") for s in L if len(s) > 2]))
jobs['skills_text'] = jobs['skills_list'].apply(lambda L: " ".join([s.replace(" ", "_") for s in L if len(s) > 2]))

print(f"Workers: {len(workers)}, Jobs: {len(jobs)}")

# ---------- Vectorize ----------
vectorizer = TfidfVectorizer()
corpus = pd.concat([workers['skills_text'], jobs['skills_text']], ignore_index=True).astype(str)
vectorizer.fit(corpus)
job_vecs = vectorizer.transform(jobs['skills_text'])
worker_vecs = vectorizer.transform(workers['skills_text'])

job_index_to_pos = {idx: pos for pos, idx in enumerate(jobs.index)}
worker_index_to_pos = {idx: pos for pos, idx in enumerate(workers.index)}

# ---------- Build pairs ----------
pairs = []
MAX_JOBS_SAMPLE = min(200, len(jobs))
MAX_WORKERS_PER_JOB = min(500, len(workers))

sampled_job_idxs = list(jobs.sample(n=MAX_JOBS_SAMPLE, random_state=1).index)

for jidx in sampled_job_idxs:
    jpos = job_index_to_pos[jidx]
    sampled_workers = workers.sample(n=MAX_WORKERS_PER_JOB, random_state=jidx).index
    for widx in sampled_workers:
        wpos = worker_index_to_pos[widx]
        w_sk = set(workers.at[widx, 'skills_list'])
        j_sk = set(jobs.at[jidx, 'skills_list'])
        overlap = len(w_sk & j_sk)
        union = len(w_sk | j_sk)
        jaccard = overlap / union if union > 0 else 0.0
        tfidf_sim = float(cosine_similarity(job_vecs[jpos], worker_vecs[wpos])[0,0])
        pairs.append({
            'job_idx': jidx,
            'worker_idx': widx,
            'overlap': overlap,
            'union': union,
            'jaccard': jaccard,
            'tfidf_sim': tfidf_sim,
            'job_skill_count': len(j_sk),
            'worker_skill_count': len(w_sk),
        })

pairs_df = pd.DataFrame(pairs)
print("Built pairs:", len(pairs_df))

# ---------- Simulate labels ----------
def simulate_hire_label(row):
    base = 0.05 + 0.6 * row['jaccard'] + 0.35 * row['tfidf_sim']
    base = max(0.0, min(1.0, base))
    return 1 if random.random() < base else 0

pairs_df['label'] = pairs_df.apply(simulate_hire_label, axis=1)
print("Label distribution:\n", pairs_df['label'].value_counts())

# ---------- Train/test split ----------
X = pairs_df[['jaccard', 'tfidf_sim', 'overlap', 'job_skill_count', 'worker_skill_count']].fillna(0)
y = pairs_df['label']

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, stratify=y, random_state=42)

# ---------- Train Logistic Regression ----------
clf = LogisticRegression(max_iter=1000, class_weight='balanced')
clf.fit(X_train, y_train)

# ---------- Evaluate ----------
y_pred = clf.predict(X_test)
y_proba = clf.predict_proba(X_test)[:,1]

print("\nClassification report (Logistic Regression):\n")
print(classification_report(y_test, y_pred))
print("Confusion matrix:\n", confusion_matrix(y_test, y_pred))
print("Accuracy:", accuracy_score(y_test, y_pred))
print("ROC AUC:", roc_auc_score(y_test, y_proba))

# ---------- Save model ----------
joblib.dump(clf, "workbridge_match_clf.joblib")
joblib.dump(vectorizer, "workbridge_tfidf_vectorizer.joblib")
print("✅ Model and vectorizer saved to disk.")

# ---------- Recommend workers ----------
def recommend_workers_for_job(job_idx, top_n=5):
    jpos = job_index_to_pos[job_idx]
    j_sk = set(jobs.at[job_idx, 'skills_list'])
    j_vec = job_vecs[jpos]
    rows = []
    for widx in workers.index:
        wpos = worker_index_to_pos[widx]
        w_sk = set(workers.at[widx, 'skills_list'])
        overlap = len(w_sk & j_sk)
        union = len(w_sk | j_sk)
        jaccard = overlap / union if union > 0 else 0.0
        tfidf_sim = float(cosine_similarity(j_vec, worker_vecs[wpos])[0,0])
        feat = pd.DataFrame([[jaccard, tfidf_sim, overlap, len(j_sk), len(w_sk)]],
                            columns=['jaccard','tfidf_sim','overlap','job_skill_count','worker_skill_count'])
        prob = clf.predict_proba(feat)[0,1]
        rows.append((widx, prob, feat))
    rows_sorted = sorted(rows, key=lambda r: r[1], reverse=True)[:top_n]
    result = []
    for widx, prob, feat in rows_sorted:
        result.append({
            'worker_idx': int(widx),
            'probability': float(prob),
            'worker_skills': workers.at[widx, 'skills_list']
        })
    return pd.DataFrame(result)

demo_job = sampled_job_idxs[0]
print("\nTop recommended workers for job idx", demo_job)
print(recommend_workers_for_job(demo_job))
