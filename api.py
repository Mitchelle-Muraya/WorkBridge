# api.py
from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib, numpy as np, pandas as pd, os, mysql.connector
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.feature_extraction.text import TfidfVectorizer
from dotenv import load_dotenv

# --------------------------
# Load environment variables
# --------------------------
load_dotenv()  # reads .env file
DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = int(os.getenv("DB_PORT", 3306))
DB_USER = os.getenv("FLASK_DB_USER", "flaskuser")
DB_PASS = os.getenv("FLASK_DB_PASS", "12345")
DB_NAME = os.getenv("DB_DATABASE", "workbridge")

print(f"🔌 Connecting to MySQL at {DB_HOST}:{DB_PORT} using user {DB_USER}")

# --------------------------
# Load trained ML model + vectorizer
# --------------------------
model = joblib.load("random_forest_model.pkl")
vectorizer = joblib.load("tfidf_vectorizer.pkl")

# --------------------------
# Domain-specific keyword expansions
# --------------------------
custom_replacements = {
    "plumber": "plumber plumbing pipes taps sinks toilets repair bathroom leak",
    "electrician": "electrician wiring sockets lights switches fuse electricity power",
    "mechanic": "mechanic car engine repair garage vehicle motorbike service",
    "tailor": "tailor sewing clothes uniforms dresses fabric fashion",
    "welder": "welder welding metal steel iron fabrication",
    "carpenter": "carpenter woodworking furniture chairs tables bed construction",
    "farmer": "farmer farming crops maize beans agriculture garden",
    "painter": "painter painting house walls brush roller color",
    "gardener": "gardener gardening plants flowers lawn trimming hedge",
    "maid": "maid househelp cleaning washing laundry cooking home chores",
    "nanny": "nanny babysitter childcare kids children infant care",
    "driver": "driver driving vehicle car matatu boda taxi transport",
    "security": "security guard watchman protection patrol safety",
}

def expand_text(text: str) -> str:
    text = text.lower().strip()
    expanded = text
    for kw, replacement in custom_replacements.items():
        if kw in text:
            expanded += " " + replacement
    return expanded


# --------------------------
# Flask setup
# --------------------------
app = Flask(__name__)
CORS(app)

@app.route("/", methods=["GET"])
def home():
    return jsonify({
        "message": "✅ WorkBridge ML API (Dynamic Worker Matching) is running successfully!",
        "usage": "POST to /predict_job or /recommend_workers"
    })


# -------------------------------------------------
# 🧠 Predict job category
# -------------------------------------------------
@app.route("/predict_job", methods=["POST"])
def predict_job():
    try:
        data = request.get_json()
        if not data or "description" not in data:
            return jsonify({"error": "Missing 'description' field"}), 400

        desc = data["description"].lower().strip()
        expanded = expand_text(desc)

        X_vec = vectorizer.transform([expanded])
        probs = model.predict_proba(X_vec)[0]
        pred_class = model.classes_[np.argmax(probs)]
        confidence = {label: round(float(p), 2) for label, p in zip(model.classes_, probs)}

        return jsonify({
            "description": desc,
            "predicted_category": pred_class,
            "confidence": confidence
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 500


# -------------------------------------------------
# 🤖 Recommend Workers (live DB)
# -------------------------------------------------
@app.route("/recommend_workers", methods=["POST"])
def recommend_workers():
    data = request.get_json()
    job_skills = data.get("skills", "")

    try:
        # ✅ Connect to MySQL (Laravel DB)
        db = mysql.connector.connect(
            host=DB_HOST,
            port=DB_PORT,
            user=DB_USER,
            password=DB_PASS,
            database=DB_NAME
        )
        print("✅ Connected to MySQL successfully.")
        print(f"📋 Job skills received: {job_skills}")

        cursor = db.cursor(dictionary=True)
        cursor.execute("""
            SELECT u.id, u.name AS worker_name, w.skills, w.location, w.experience
            FROM workers w
            INNER JOIN users u ON u.id = w.user_id
        """)
        workers = cursor.fetchall()
        print(f"✅ Retrieved {len(workers)} workers from DB.")
        cursor.close()
        db.close()

        if not workers:
            return jsonify({"recommended_workers": []})

        df = pd.DataFrame(workers)
        df["skills_cleaned"] = df["skills"].fillna("").apply(lambda s: s.replace(",", " ").lower())
        job_skills_cleaned = job_skills.replace(",", " ").lower()

        tfidf = TfidfVectorizer(stop_words="english")
        tfidf_matrix = tfidf.fit_transform(df["skills_cleaned"])
        job_vec = tfidf.transform([job_skills_cleaned])
        similarity_scores = cosine_similarity(job_vec, tfidf_matrix).flatten()

        df["match_score"] = similarity_scores
        df = df.sort_values(by=["match_score", "experience"], ascending=False)
        df = df[df["match_score"] > 0.0]

        recommended = df.head(5).to_dict(orient="records")
        print(f"✨ Recommended top {len(recommended)} workers.")

        return jsonify({"recommended_workers": recommended})

    except mysql.connector.Error as db_err:
        print(f"❌ Database Error: {db_err}")
        return jsonify({"error": str(db_err)}), 500

    except Exception as e:
        print(f"❌ General Error: {e}")
        return jsonify({"error": str(e)}), 500


# --------------------------
# Run Flask App
# --------------------------
if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    print(f"🚀 Starting Flask on port {port} ...")
    app.run(host="0.0.0.0", port=port)
