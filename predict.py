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
    "plumber": "plumber plumbing pipes taps sinks toilets repair bathroom leak water",
    "electrician": "electrician wiring sockets lights switches fuse electricity power connection",
    "mechanic": "mechanic car engine repair garage vehicle motorbike service auto",
    "tailor": "tailor sewing clothes uniforms dresses fabric stitching fashion repairs",
    "welder": "welder welding metal steel iron fabrication gates windows grills",
    "carpenter": "carpenter woodworking furniture chairs tables bed construction timber repair",
    "farmer": "farmer farming crops maize beans agriculture garden poultry livestock",
    "painter": "painter painting house walls brush roller color interior exterior",
    "gardener": "gardener gardening plants flowers lawn trimming hedge landscaping trees",
    "maid": "maid househelp cleaning washing laundry cooking home chores domestic",
    "nanny": "nanny babysitter childcare kids children infant care daycare",
    "driver": "driver driving vehicle car matatu boda taxi transport delivery",
    "security": "security guard watchman protection patrol safety nightshift",
    "mason": "mason construction bricks cement building walls tiles concrete",
    "chef": "chef cook cooking food catering hotel kitchen meals",
    "barber": "barber shaving haircut grooming hair stylist beard",
    "salonist": "salonist hairdresser braids weaving plaiting beauty stylist salon",
    "shoemaker": "shoemaker cobbler shoe repair polishing sandals fixing",
    "vendor": "vendor hawker market selling fruits vegetables clothes roadside",
    "boda": "boda boda rider motorcycle transport delivery courier",
    "cleaner": "cleaner janitor sweeping mopping office sanitation hygiene",
    "dj": "dj disc jockey music entertainment events parties sound",
    "photographer": "photographer camera photos shooting events wedding studio",
    "teacher": "teacher tutor lessons training home teaching private coaching",
    "technician": "technician repair electronics phone computer fridge tv",
    "bricklayer": "bricklayer construction wall foundation cement blocks",
    "fisherman": "fisherman fishing boat lake fish market tilapia",
    "meat": "butcher meat cutting beef goat chicken pork",
    "water_vendor": "water vendor supplier delivery tank jerrican borehole",
    "shoe_shiner": "shoe shiner polishing shining leather shoes street",
    "laundry": "laundry washer ironing clothes handwash drycleaning services",
    "event_planner": "event planner decoration tents chairs wedding party organizer",
    "construction_worker": "construction worker laborer site digging lifting mixing building",
    "house_painter": "house painter walls ceiling decoration brush roller paint",
    "driver_uber": "uber driver taxi cab ride transport online app",
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
