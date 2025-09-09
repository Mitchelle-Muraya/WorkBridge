import pandas as pd

import re

# ==========================
# Paths to your CSVs (absolute paths)
# ==========================
upwork_jobs_path = r"D:\IS 2\upwork\upwork-jobs.csv"
informal_jobs_path = r"D:\IS 2\informal_jobs_full.csv"
it_workers_path = r"D:\IS 2\Resume\UpdatedResumeDataSet.csv"
informal_workers_path = r"D:\IS 2\informal_workers_resumes.csv"
it_skills_path = r"D:\IS 2\dataset.csv"  # IT skills
informal_skills_path = r"D:\IS 2\kenyan_informal_skills.csv"  # Informal skills

# ==========================
# Load datasets (with encoding handling)
# ==========================
def load_csv(path):
    try:
        return pd.read_csv(path, encoding="utf-8-sig")
    except UnicodeDecodeError:
        return pd.read_csv(path, encoding="latin1")

upwork_jobs = load_csv(upwork_jobs_path)
informal_jobs = load_csv(informal_jobs_path)
it_workers = load_csv(it_workers_path)
informal_workers = load_csv(informal_workers_path)
it_skills_df = load_csv(it_skills_path)
informal_skills_df = load_csv(informal_skills_path)

print("✅ Upwork jobs loaded:", len(upwork_jobs))
print("✅ Informal jobs loaded:", len(informal_jobs))
print("✅ IT workers loaded:", len(it_workers))
print("✅ Informal workers loaded:", len(informal_workers))
print("✅ IT skills loaded:", len(it_skills_df))
print("✅ Informal skills loaded:", len(informal_skills_df))

# ==========================
# Normalize job columns
# ==========================
def normalize_jobs(df):
    df = df.copy()
    if "title" not in df.columns:
        title_col = [c for c in df.columns if "title" in c.lower()]
        if title_col:
            df.rename(columns={title_col[0]: "title"}, inplace=True)
        else:
            df["title"] = df.iloc[:, 0]
    if "description" not in df.columns:
        desc_col = [c for c in df.columns if "desc" in c.lower()]
        if desc_col:
            df.rename(columns={desc_col[0]: "description"}, inplace=True)
        else:
            df["description"] = df.iloc[:, 1]
    return df[["title", "description"]]

upwork_jobs = normalize_jobs(upwork_jobs)
informal_jobs = normalize_jobs(informal_jobs)

# ==========================
# Text cleaning + normalization
# ==========================
def clean_text(text):
    text = str(text).lower()
    text = re.sub(r"[^a-z0-9\s]", " ", text)
    text = re.sub(r"[\s\-\/]+", " ", text)
    text = re.sub(r"\s+", " ", text)
    return text.strip()

def normalize_word(word):
    """Lowercase + strip plurals and common suffixes."""
    word = word.lower().strip()
    word = re.sub(r"(ers|er|ing|s)$", "", word)  # plumbers → plumber
    return word



# ==========================
# Prepare skill keywords (cleaner)
# ==========================
def get_skill_keywords(df):
    skill_col = [c for c in df.columns if "skill" in c.lower()]
    if not skill_col:
        raise ValueError("❌ No column with skills found in skills CSV.")
    all_skills = []
    for kw in df[skill_col[0]].dropna():
        split_skills = [k.strip().lower() for k in str(kw).split(",") if k.strip()]
        all_skills.extend(split_skills)

    # ✅ Remove duplicates + very short junk tokens
    return [kw for kw in set(all_skills) if len(kw) > 2]


# ==========================
# Skill extraction with strict matching
# ==========================
def extract_skills(text, keywords):
    if pd.isna(text) or not str(text).strip():
        return []

    text_clean = clean_text(text)
    found = set()

    for kw in keywords:
        kw_norm = normalize_word(kw)
        variants = [kw_norm]

        if kw_norm in synonym_map:
            variants.extend([normalize_word(v) for v in synonym_map[kw_norm]])

        for v in variants:
            if not v or len(v) <= 2:
                continue  # 🚫 skip noisy short skills like "r", "io"

            # ✅ Whole word match instead of substring
            if re.search(rf"\b{re.escape(v)}\b", text_clean):
                found.add(kw_norm)
                break

    return list(found)
it_keywords = get_skill_keywords(it_skills_df)
informal_keywords = get_skill_keywords(informal_skills_df)



print("✅ Total IT skills loaded:", len(it_keywords))
print("✅ Total Informal skills loaded:", len(informal_keywords))

# ==========================
# Synonym dictionary (optimized)
# ==========================
synonym_map = {
    # Domestic work
    "cleaner": ["maid", "housekeeper", "domestic worker", "janitor", "mama fua", "laundry lady"],
    "laundry": ["washing clothes", "cloth washing", "mama fua", "dhobi"],
    "nanny": ["childcare", "babysitter", "house help", "au pair", "daycare assistant"],
    "cook": ["chef", "kitchen staff", "food preparer", "caterer", "mama mboga"],

    # Construction
    "construction worker": ["mjengo", "builder", "mason", "labourer", "site worker", "handyman"],
    "plumber": ["plumbing", "pipe fitter", "fundi wa maji"],
    "electrician": ["electrical technician", "wiring specialist", "fundi wa stima"],
    "carpenter": ["woodworker", "joiner", "fundi wa mbao", "cabinet maker"],
    "welder": ["metal worker", "fabricator", "fundi wa chuma"],

    # Transport
    "driver": ["chauffeur", "matatu driver", "truck driver", "taxi driver", "cab driver", "boda rider"],
    "rider": ["boda boda", "delivery rider", "courier", "motorcycle driver"],

    # Mechanics
    "mechanic": ["car repair", "vehicle technician", "auto repair", "fundi wa gari", "garage worker"],
    "technician": ["repairman", "maintenance worker", "service engineer"],

    # Beauty & Personal Care
    "hairdresser": ["barber", "salonist", "stylist", "beautician"],
    "makeup artist": ["beauty artist", "cosmetician", "makeup specialist"],

    # Farming & Manual Labor
    "farmer": ["farm worker", "agricultural worker", "shamba boy", "herdsman"],
    "gardener": ["landscaper", "groundskeeper", "shamba worker"],

    # Security
    "security guard": ["watchman", "askari", "gatekeeper", "night guard"],

    # General informal workers
    "casual laborer": ["day worker", "handyman", "mjengo guy", "kibarua"],
    "vendor": ["hawker", "trader", "mama mboga", "street seller", "market seller"],

    # Swahili / Kenyan informal
    "fundi": ["artisan", "craftsman", "mechanic", "plumber", "technician", "repairman", "carpenter"],
    "fundi umeme": ["electrician", "electrical technician"],
    "fundi maji": ["plumber", "water technician", "pipefitter"],
    "fundi simu": ["phone repairer", "mobile technician", "technologist"],
    "fundi magari": ["mechanic", "auto technician", "vehicle repairer"],
    "fundi viatu": ["cobbler", "shoemaker", "shoe repairer"],
    "mchuuzi": ["vendor", "trader", "hawker", "retailer", "merchant", "shopkeeper"],
    "mkulima": ["farmer", "agricultural worker", "grower", "peasant", "cultivator"],
    "dereva": ["driver", "chauffeur", "motorist"],
    "mpishi": ["cook", "chef", "food preparer"],
    "usafi": ["cleaning", "janitor", "housekeeping", "sanitation"],
    "mchoraji": ["painter", "artist", "illustrator"],
    "fundikazi": ["domestic worker", "maid", "house help", "housekeeper"],
    "mshonaji": ["tailor", "seamstress", "dressmaker", "designer"],
    "boi": ["helper", "househelp", "assistant"],

    # IT equivalents
    "fundi computer": ["computer technician", "IT support", "desktop technician"],
    "fundi IT": ["IT technician", "IT support", "systems technician", "tech support"],
    "fundi mtandao": ["network technician", "network engineer", "internet specialist"],
    "programu": ["software", "application", "program"],
    "mtandao": ["network", "internet", "connectivity"],
    "data": ["database", "information", "records"],
    "developer": ["programmer", "coder", "software engineer"],
    "mhandisi wa kompyuta": ["computer engineer", "IT engineer"]
}

# ==========================
# Skill extraction with synonyms
# ==========================
def extract_skills(text, keywords):
    if pd.isna(text) or not str(text).strip():
        return []

    text_clean = clean_text(text)
    found = set()

    for kw in keywords:
        kw_norm = normalize_word(kw)
        variants = [kw_norm]
        if kw_norm in synonym_map:
            variants.extend([normalize_word(v) for v in synonym_map[kw_norm]])

        for v in variants:
            if v and v in text_clean:   # ✅ substring match instead of word-boundary regex
                found.add(kw_norm)      # store normalized version
                break                   # no need to keep checking once matched
    return list(found)


# ==========================
# Extract skills for jobs
# ==========================
upwork_jobs["clean_text"] = upwork_jobs["title"].astype(str) + " " + upwork_jobs["description"].astype(str)
upwork_jobs["extracted_skills"] = upwork_jobs["clean_text"].apply(lambda x: extract_skills(x, it_keywords))

informal_jobs["clean_text"] = informal_jobs["title"].astype(str) + " " + informal_jobs["description"].astype(str)
informal_jobs["extracted_skills"] = informal_jobs["clean_text"].apply(lambda x: extract_skills(x, informal_keywords))

all_jobs = pd.concat([upwork_jobs, informal_jobs], ignore_index=True)
print("✅ Total jobs after merge:", len(all_jobs))

# ==========================
# Normalize workers
# ==========================
def normalize_workers(df):
    """Try to pick the most relevant column for worker skills/resume text."""
    possible_cols = [c for c in df.columns if any(k in c.lower() for k in ["resume", "skills", "summary", "experience", "bio"])]
    if possible_cols:
        return df[possible_cols[0]].astype(str)  # take first matching
    else:
        # fallback: combine everything into one string
        return df.astype(str).apply(lambda row: " ".join(row.values), axis=1)

it_workers["clean_text"] = normalize_workers(it_workers)
informal_workers["clean_text"] = normalize_workers(informal_workers)

all_workers = pd.concat([it_workers, informal_workers], ignore_index=True)
print("✅ Total workers after merge:", len(all_workers))

# Expand keywords with synonyms
all_keywords = set(it_keywords + informal_keywords)

for base, synonyms in synonym_map.items():
    all_keywords.add(base)
    for syn in synonyms:
        all_keywords.add(syn)

all_keywords = list(all_keywords)
print("✅ Total expanded keywords (with synonyms):", len(all_keywords))

all_workers["extracted_skills"] = all_workers["clean_text"].apply(lambda x: extract_skills(x, all_keywords))

# ==========================
# Test synonym map functionality
# ==========================
sample_texts = [
    "Looking for a mjengo guy to help with construction work",
    "We need a fundi wa stima for wiring a new building",
    "Hiring a mama fua for laundry services",
    "Seeking an IT fundi to fix computer networks",
]

for txt in sample_texts:
    skills_found = extract_skills(txt, all_keywords)
    print(f"\nTEXT: {txt}")
    print("Extracted Skills:", skills_found)

# ==========================
# Save results
# ==========================
upwork_jobs.to_csv("upwork_jobs_with_skills.csv", index=False)
informal_jobs.to_csv("informal_jobs_with_skills.csv", index=False)
all_workers.to_csv("workers_with_skills.csv", index=False)

print("\n✅ Saved processed CSVs")
print("\n🔍 Debugging Example")
print("Text:", "We need an electrician for wiring a new building")
print("Extracted:", extract_skills("We need an electrician for wiring a new building", all_keywords))

