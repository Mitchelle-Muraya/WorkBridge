from flask import Flask, render_template, request

app = Flask(__name__)

# ---- WORKERS AND THEIR SKILLS ----
worker_skills = {
    "Alice (Plumber)": ["plumbing", "pipes", "installation"],
    "Brian (Electrician)": ["electrical", "electrician", "wiring", "lighting", "electrician"],
    "Cynthia (Carpenter)": ["carpentry", "wood", "furniture","carpenter"],
    "David (Painter)": ["painting", "decor", "walls","paint"],
    "Eva (Mechanic)": ["mechanic", "engine", "car"]
}


# ---- FUNCTION TO MATCH WORKERS BASED ON JOB DESCRIPTION ----
def recommend_workers(job_description):
    job_desc_lower = job_description.lower()
    recommendations = []

    for worker, skills in worker_skills.items():
        # If any skill matches words in job description, include this worker
        if any(skill in job_desc_lower for skill in skills):
            recommendations.append(worker)

    # Return top 3 matches, or a message if none found
    return recommendations[:3] if recommendations else ["No matches found"]

# ---- ROUTES ----
@app.route("/", methods=["GET", "POST"])
def home():
    if request.method == "POST":
        job_desc = request.form["job_description"]
        recommendations = recommend_workers(job_desc)
        return render_template("results.html", job_desc=job_desc, recommendations=recommendations)
    return render_template("index.html")

# ---- RUN SERVER ----
if __name__ == "__main__":
    app.run(debug=True)
