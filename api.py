# api.py
from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import numpy as np

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
# Flask App
# --------------------------
app = Flask(__name__)
CORS(app)

@app.route("/", methods=["GET"])
def home():
    return jsonify({
        "message": "✅ WorkBridge ML API is running successfully!",
        "usage": "Send a POST request to /predict_job with a job description."
    })

@app.route("/predict_job", methods=["POST"])
def predict_job():
    data = request.get_json()
    if not data or "description" not in data:
        return jsonify({"error": "Missing 'description' field"}), 400

    desc = data["description"].lower().strip()

    # Keyword override first
    for kw in informal_keywords:
        if kw in desc:
            return jsonify({
                "description": desc,
                "predicted_category": "Informal",
                "confidence": {"Informal": 0.99, "IT": 0.01},
                "method": "keyword_override"
            })

    # Else, use ML model
    expanded_desc = expand_text(desc)
    X_vec = vectorizer.transform([expanded_desc])
    probs = model.predict_proba(X_vec)[0]
    pred_class = model.classes_[np.argmax(probs)]

    confidence = {
        label: round(float(prob), 2)
        for label, prob in zip(model.classes_, probs)
    }

    return jsonify({
        "description": desc,
        "predicted_category": pred_class,
        "confidence": confidence,
        "method": "ml_model"
    })


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)

import os

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    app.run(host="0.0.0.0", port=port)
