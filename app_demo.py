from flask import Flask, request, jsonify
import difflib

app = Flask(__name__)

# ---- WORKERS AND THEIR SKILLS ----
worker_skills = {
    "Alice (Plumber)": ["plumber", "plumbing", "pipes", "installation"],
    "Brian (Electrician)": ["electrician", "electrical", "wiring", "lighting", "lights"],
    "Cynthia (Carpenter)": ["carpenter", "carpentry", "wood", "furniture"],
    "David (Painter)": ["painter", "painting", "decor", "walls"],
    "Eva (Mechanic)": ["mechanic", "engine", "car", "vehicle", "repair"]
}


def recommend_workers(job_description):
    job_desc_lower = job_description.lower()
    recommendations = []

    for worker, skills in worker_skills.items():
        for skill in skills:
            # Fuzzy match threshold: 0.7 means 70% similarity
            similarity = difflib.SequenceMatcher(None, skill, job_desc_lower).ratio()
            if skill in job_desc_lower or similarity > 0.7:
                recommendations.append(worker)
                break
    return recommendations[:3] if recommendations else ["No matches found"]

# ✅ API endpoint for Laravel
@app.route("/predict_job", methods=["POST"])
def predict_job():
    data = request.get_json()
    description = data.get("description", "")

    # Just a dummy classifier for now (replace with ML model later)
    if any(word in description.lower() for word in ["plumber", "electrician", "carpenter", "painter", "mechanic"]):
        predicted_category = "Informal"
    else:
        predicted_category = "IT"

    recommendations = recommend_workers(description)

    return jsonify({
        "description": description,
        "predicted_category": predicted_category,
        "recommended_workers": recommendations
    })

if __name__ == "__main__":
    app.run(debug=True)
