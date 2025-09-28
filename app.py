from flask import Flask, request, jsonify

app = Flask(__name__)

# ---- WORKERS AND THEIR SKILLS ----
worker_skills = {
    "Alice (Plumber)": ["plumbing", "pipes", "installation"],
    "Brian (Electrician)": ["electrical", "wiring", "lighting"],
    "Cynthia (Carpenter)": ["carpentry", "wood", "furniture"],
    "David (Painter)": ["painting", "decor", "walls"],
    "Eva (Mechanic)": ["mechanic", "engine", "car"]
}

def recommend_workers(job_description):
    job_desc_lower = job_description.lower()
    recommendations = []
    for worker, skills in worker_skills.items():
        if any(skill in job_desc_lower for skill in skills):
            recommendations.append(worker)
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
