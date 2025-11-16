# api.py
from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import numpy as np
import os

# --------------------------
# Load trained model + vectorizer
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

informal_keywords = list(custom_replacements.keys())

def expand_text(text: str) -> str:
    """Expand text with domain-specific context."""
    text = text.lower().strip()
    expanded = text
    for kw, replacement in custom_replacements.items():
        if kw in text:
            expanded += " " + replacement
    return expanded


# --------------------------
# Flask App Setup
# --------------------------
app = Flask(__name__)
CORS(app)


@app.route("/", methods=["GET"])
def home():
    return jsonify({
        "message": "✅ WorkBridge ML API is running successfully!",
        "usage": "Send a POST request to /predict_job with a job or skill description."
    })


@app.route("/predict_job", methods=["POST"])
def predict_job():
    """Handles both worker-skill and job-description inputs intelligently."""
    try:
        data = request.get_json()
        if not data or "description" not in data:
            return jsonify({"error": "Missing 'description' field"}), 400

        desc = data["description"].lower().strip()
        expanded_desc = expand_text(desc)

        # --- ML Prediction ---
        X_vec = vectorizer.transform([expanded_desc])
        probs = model.predict_proba(X_vec)[0]
        pred_class = model.classes_[np.argmax(probs)]
        confidence = {
            label: round(float(prob), 2)
            for label, prob in zip(model.classes_, probs)
        }

        # --- Decide Response Type ---
        if any(word in desc for word in ["need", "hire", "looking for", "job", "worker", "technician", "fix"]):
            # Client posting a job → Recommend workers
            recommended_workers = []
            for kw in informal_keywords:
                if kw in desc:
                    recommended_workers.append(kw.title() + " (Skilled Worker)")
            if not recommended_workers:
                recommended_workers = ["No matches found"]

            return jsonify({
                "description": desc,
                "predicted_category": pred_class,
                "confidence": confidence,
                "recommended_workers": recommended_workers,
                "source": "client_job"
            })

        else:
            # Worker skills → Recommend jobs
            recommended_jobs = []
            for kw in informal_keywords:
                if kw in desc:
                    recommended_jobs.append({
                        "job_title": f"{kw.title()} Required for Local Project",
                        "category": "Informal"
                    })
            if not recommended_jobs:
                recommended_jobs = [{"job_title": "No matches found", "category": "None"}]

            return jsonify({
                "description": desc,
                "predicted_category": pred_class,
                "confidence": confidence,
                "recommended_jobs": recommended_jobs,
                "source": "worker_profile"
            })

    except Exception as e:
        return jsonify({"error": str(e)}), 500


if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    app.run(host="0.0.0.0", port=port)
