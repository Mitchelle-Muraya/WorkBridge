@extends('layouts.app')

@section('content')

{{-- ======================= MODERN FORM STYLES ======================= --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

<style>
  :root {
    --bg-color: #f4f7fc;
    --card-bg: #ffffff;
    --text-color: #1d1d1d;
    --primary: #007bff;
    --input-bg: #ffffff;
    --radius: 14px;
  }

  [data-theme="dark"] {
    --bg-color: #0c0f14;
    --card-bg: #1a1f25;
    --text-color: #f5f5f5;
    --primary: #4dd0e1;
    --input-bg: #2a323d;
  }

  body {
    background: var(--bg-color);
    color: var(--text-color);
    transition: .3s ease;
  }

  .job-card {
    max-width: 900px;
    margin: 2.5rem auto;
    background: var(--card-bg);
    border-radius: var(--radius);
    padding: 2.5rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  }

  .title-area {
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .title-area h2 {
    font-weight: 700;
    color: var(--primary);
  }

  .form-label {
    font-weight: 600;
  }

  .form-control, .ts-control {
    border-radius: var(--radius) !important;
    background: var(--input-bg) !important;
    color: var(--text-color) !important;
    border: 1px solid #d2d6dc;
    padding: .75rem !important;
  }

  .row-flex {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
  }

  .col-side {
    flex: 1;
    min-width: 45%;
  }

  button[type="submit"] {
    width: 100%;
    padding: 12px;
    font-size: 1.1rem;
    border-radius: var(--radius);
  }
</style>

<div class="container">
  <div class="job-card">

    {{-- DARK MODE BUTTON --}}
    <div class="d-flex justify-content-end">
    <button id="toggleTheme" class="btn btn-sm btn-outline-secondary mb-3">
        🌙 Dark Mode
    </button>
</div>


    <div class="title-area">
      <h2>🛠️ Post a New Job</h2>
      <p class="text-muted">Fill in the job details below</p>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
      <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    {{-- ======================= JOB FORM ======================= --}}
    <form action="{{ route('client.storeJob') }}" method="POST">
      @csrf

      <div class="row-flex">

        {{-- LEFT SIDE --}}
        <div class="col-side">

          <div class="mb-3">
            <label class="form-label">Job Title</label>
            <input type="text" name="title" class="form-control" placeholder="e.g., Hairdresser" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Job Description</label>
            <textarea name="description" rows="5" class="form-control"
             placeholder="Describe the job in detail..." required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control"
             placeholder="e.g., Nairobi" required>
          </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="col-side">

          <div class="mb-3">
            <label class="form-label">Job Category</label>
            <select id="category" name="category" class="form-control" required>
              <option value="">Search job category...</option>
              @foreach($rates as $rate)
                <option value="{{ $rate->skill_name }}"
                  data-min="{{ $rate->min_rate }}"
                  data-max="{{ $rate->max_rate }}"
                  data-avg="{{ $rate->average_rate }}">
                  {{ $rate->skill_name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Suggested Rate (KES)</label>
            <input type="text" id="suggestedRate" class="form-control" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Your Offer (KES)</label>
            <input type="number" name="budget" id="budget" class="form-control" placeholder="Enter your budget" required>
            <small id="budgetHelp" class="text-muted"></small>
          </div>

          <div class="mb-3">
            <label class="form-label">Deadline</label>
            <input type="date" name="deadline" class="form-control" id="deadline" required min="{{ date('Y-m-d') }}">

          </div>

        </div>
      </div>

      {{-- SKILLS SECTION --}}
      <div class="mb-3 mt-3">
        <label class="form-label">Skills Required (Max 5)</label>
        <select name="skills_required[]" id="skills_required" class="form-control" multiple>
          <option value="Plumbing">Plumbing</option>
          <option value="Electrical Repair">Electrical Repair</option>
          <option value="Carpentry">Carpentry</option>
          <option value="Painting">Painting</option>
          <option value="Gardening">Gardening</option>
          <option value="Tailoring">Tailoring</option>
          <option value="Cleaning">Cleaning</option>
          <option value="Cooking">Cooking</option>
          <option value="Driving">Driving</option>
          <option value="Security">Security</option>
        </select>
        <small class="text-muted">Type to search & select (max 5)</small>

        <div class="mt-3">
          <label class="form-label">Other Skill (Optional)</label>
          <input type="text" name="other_skill" class="form-control" placeholder="Add custom skill">
        </div>
      </div>

      <button type="submit" class="btn btn-primary mt-4">Post Job</button>

    </form>

  </div>
</div>

{{-- ======================= JS: SEARCHABLE SELECT ======================= --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Make Category searchable
    new TomSelect("#category", {
        placeholder: "Search categories...",
        allowEmptyOption: false,
        maxOptions: 200
    });

    // Make Skills searchable + limit to 5
    new TomSelect("#skills_required", {
        maxItems: 5,
        plugins: ["remove_button"],
        placeholder: "Select skills…",
        create: false
    });

    // Update rate suggestion
    document.getElementById('category').addEventListener('change', function () {
        let opt = this.selectedOptions[0];
        let min = opt.dataset.min;
        let max = opt.dataset.max;
        let avg = opt.dataset.avg;

        document.getElementById('suggestedRate').value =
          `${min} – ${max} KES (avg ${avg})`;

        document.getElementById('budgetHelp').innerHTML =
          `Budget must be between <b>${min}</b> and <b>${max}</b> KES.`;
    });
    document.getElementById('deadline').addEventListener('change', function () {
    let today = new Date().toISOString().split('T')[0];
    if (this.value < today) {
        alert("You cannot select past dates.");
        this.value = today;
    }
});


});
</script>

@endsection
