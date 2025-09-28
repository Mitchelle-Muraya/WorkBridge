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
# Domain-specific expansions (same as predict.py)
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
    text = text.lower().strip()
    expanded = text
    for keyword, replacement in custom_replacements.items():
        if keyword in text:
            expanded += " " + replacement
    return expanded

# --------------------------
# Initialize Flask
# --------------------------
app = Flask(__name__)
CORS(app)

@app.route("/predict_job", methods=["POST"])
def predict_job():
    data = request.get_json()
    if not data or "description" not in data:
        return jsonify({"error": "Please provide 'description'"}), 400

    desc = data["description"].lower().strip()

    # 1️⃣ Keyword override
    for kw in informal_keywords:
        if kw in desc:
            return jsonify({
                "description": desc,
                "predicted_category": "Informal",
                "confidence": {"Informal": 0.99, "IT": 0.0, },
                "method": "keyword_override"
            })

    # 2️⃣ Else expand + predict with model
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
    app.run(port=5000, debug=True)
