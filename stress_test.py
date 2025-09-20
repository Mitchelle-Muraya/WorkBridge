# stress_test.py
import joblib
import os

# Load trained model + vectorizer
print("Loading trained model + vectorizer...")
model = joblib.load("random_forest_model.pkl")
vectorizer = joblib.load("tfidf_vectorizer.pkl")
print("✅ Model and vectorizer loaded\n")

# Define tricky/ambiguous jobs
test_jobs = [
    "Looking for someone to repair my computer and also paint the walls",
    "Need help with data entry in Excel",
    "Hire a driver for deliveries using company vehicle",
    "Part-time electrician who can also do some IT networking",
    "Looking for a welder to fix a broken metal gate",
    "Remote freelance writer needed for blog posts",
    "Plumber required to install bathroom fittings",
    "Software developer who can also design logos",
    "Gardener needed to trim hedges and maintain lawn",
    "Customer support agent for an online store"
]

# Predict each job
for job in test_jobs:
    X = vectorizer.transform([job])
    pred = model.predict(X)[0]
    probs = model.predict_proba(X)[0]

    # Confidence per class
    class_probs = {cls: round(prob, 2) for cls, prob in zip(model.classes_, probs)}

    print("----------------------------------------")
    print(f"Job Description: {job}")
    print(f" → Predicted Category: {pred}")
    print(" → Confidence levels:")
    for cls, prob in class_probs.items():
        print(f"    {cls}: {prob}")
