import joblib
import numpy as np

# --------------------------
# Load trained model + vectorizer
# --------------------------
print("Loading trained model + vectorizer...")
model = joblib.load("random_forest_model.pkl")
vectorizer = joblib.load("tfidf_vectorizer.pkl")
print("✅ Model and vectorizer loaded\n")

# --------------------------
# Domain-specific expansions
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
    """Expand job description with domain-specific keywords"""
    text = text.lower().strip()
    expanded = text
    for keyword, replacement in custom_replacements.items():
        if keyword in text:
            expanded += " " + replacement
    return expanded

# --------------------------
# Hybrid prediction
# --------------------------
def predict_job_category(text: str):
    # 1️⃣ Check keyword override for informal jobs
    for kw in informal_keywords:
        if kw in text.lower():
            return "Informal", 0.99  # high confidence override

    # 2️⃣ Else use Random Forest
    expanded_text_desc = expand_text(text)
    X_test = vectorizer.transform([expanded_text_desc])

    if hasattr(model, "predict_proba"):
        probs = model.predict_proba(X_test)[0]
        pred_class = model.classes_[np.argmax(probs)]
        confidence = probs[np.argmax(probs)]
    else:
        pred_class = model.predict(X_test)[0]
        confidence = None

    return pred_class, confidence

# --------------------------
# Interactive prediction loop
# --------------------------
while True:
    print("-" * 50)
    job_desc = input("Job Description (type 'exit' to quit): ")
    if job_desc.lower() == "exit":
        break

    pred_class, confidence = predict_job_category(job_desc)
    print(f" → Predicted Category: {pred_class}")
    if confidence is not None:
        print(f" → Confidence: {confidence:.2f}")

    # Show top TF-IDF words
    expanded_desc = expand_text(job_desc)
    X_test = vectorizer.transform([expanded_desc])
    feature_names = np.array(vectorizer.get_feature_names_out())
    topn = X_test.toarray().flatten().argsort()[-5:][::-1]
    print(" → Top contributing words (TF-IDF):")
    for idx in topn:
        if X_test[0, idx] > 0:
            print(f"    {feature_names[idx]} ({X_test[0, idx]:.3f})")
