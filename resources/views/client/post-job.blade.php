@extends('layouts.app')

@section('content')
<style>
  :root {
    --bg-color: #f8fafc;
    --card-bg: #ffffff;
    --text-color: #212529;
    --primary-color: #0d6efd;
    --secondary-text: #6c757d;
    --input-bg: #ffffff;
  }

  [data-theme="dark"] {
    --bg-color: #0b0c10;
    --card-bg: #1f2833;
    --text-color: #ffffff;
    --primary-color: #45a29e;
    --secondary-text: #c5c6c7;
    --input-bg: #28313b;
  }

  html, body {
  height: 100%;
  background-color: var(--bg-color);
  color: var(--text-color);
  transition: background-color 0.4s ease, color 0.4s ease;
}


  .job-form-card {
    max-width: 1000px;
    margin: 2.5rem auto;
    background: var(--card-bg);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 2.5rem;
    transition: all 0.4s ease-in-out;
  }

  .form-label {
    font-weight: 600;
    color: var(--primary-color);
  }

  h2 {
    font-weight: 700;
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 1.8rem;
  }

  input, textarea, select {
    background-color: var(--input-bg);
    color: var(--text-color);
    border: 1px solid #ced4da;
    border-radius: 10px;
    padding: 10px;
  }

  .btn-primary {
    background-color: var(--primary-color);
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    font-weight: 600;
    width: 100%;
    color: #fff;
  }

  .btn-primary:hover {
    opacity: 0.9;
  }

  .toggle-mode {
    float: right;
    background: none;
    border: none;
    color: var(--primary-color);
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
  }

  .toggle-mode:hover {
    color: var(--text-color);
  }

  .row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
  }
  nav.navbar {
  background-color: var(--card-bg) !important;
  color: var(--text-color) !important;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  transition: background-color 0.4s ease;
}

.navbar a, .navbar-brand, .navbar-nav .nav-link {
  color: var(--text-color) !important;
  transition: color 0.4s ease;
}
.job-form-card, .card {
  background-color: var(--card-bg);
  color: var(--text-color);
}
[data-theme="dark"] body {
  background-color: #0b0c10; /* dark charcoal */
}


  .col-half {
    flex: 1;
    min-width: 45%;
  }

  small.text-muted {
    font-size: 0.85rem;
    color: var(--secondary-text) !important;
  }
</style>

<div class="container py-5">
  <div class="job-form-card">
    <button id="toggleTheme" class="toggle-mode">🌙 Dark Mode</button>
    <h2>🛠️ Post a New Job</h2>

    @if(session('success'))
      <div class="alert alert-success text-center">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('client.storeJob') }}" method="POST">
      @csrf

      <div class="row">
        <!-- LEFT COLUMN -->
        <div class="col-half">
          <div class="mb-3">
            <label for="title" class="form-label">Job Title</label>
            <input type="text" class="form-control" name="title" placeholder="e.g., House Painter, Electrician Needed" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Job Description</label>
            <textarea class="form-control" name="description" rows="5" placeholder="Describe the job clearly..." required></textarea>
          </div>

          <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" name="location" placeholder="e.g., Nairobi, Westlands" required>
          </div>


        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-half">
          <div class="mb-3">
            <label for="budget" class="form-label">Budget (KSH)</label>
            <input type="number" class="form-control" name="budget" placeholder="e.g., 1500" required>
          </div>

          <div class="mb-3">
            <label for="deadline" class="form-label">Deadline</label>
            <input type="date" class="form-control" name="deadline" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Skills Required (Select up to 5)</label>
            <select class="form-select" name="skills_required[]" id="skills_required" multiple>
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
            <small class="text-muted">Hold CTRL/CMD to select up to 5.</small>

            <div class="mt-3">
              <label for="other_skill" class="form-label">Other Skill (optional)</label>
              <input type="text" class="form-control" name="other_skill" placeholder="e.g., Plastering or Babysitting">
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Post Job</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Dark/Light mode toggle
  const toggleBtn = document.getElementById('toggleTheme');
  const currentTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', currentTheme);
  toggleBtn.textContent = currentTheme === 'light' ? '🌙 Dark Mode' : '☀️ Light Mode';

  toggleBtn.addEventListener('click', () => {
    const theme = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    toggleBtn.textContent = theme === 'light' ? '🌙 Dark Mode' : '☀️ Light Mode';
  });

  // Skill limit
  document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('skills_required');
    select.addEventListener('change', function () {
      if ([...this.selectedOptions].length > 5) {
        alert('You can select up to 5 skills only.');
        this.options[this.selectedIndex].selected = false;
      }
    });
  });
</script>
@endsection
