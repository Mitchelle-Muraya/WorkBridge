<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | Worker Dashboard</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
  :root {
    --primary: #0066ff;
    --accent: #00c9a7;
    --bg: linear-gradient(135deg, #f5f9ff, #e7efff, #f8fbff);
    --text: #1f1f1f;
    --card-bg: rgba(255, 255, 255, 0.85);
    --sidebar-gradient: linear-gradient(180deg, #00b8ff, #0066cc, #003c8f);

    --dropdown-text: #222;
    --text-muted: #6c757d;
  }

  [data-theme="dark"] {
    --primary: #3399ff;
    --accent: #00e6b8;
    --bg: linear-gradient(135deg, #0a1124, #101b3a, #0b132b);
    --text: #f8f9fa;
    --card-bg: rgba(20, 30, 60, 0.9);
    --sidebar-gradient: linear-gradient(180deg, #14213d, #0b132b);
    --dropdown-text: #f1f1f1;
    --text-muted: #a9b7c6;
  }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: "Poppins", sans-serif;
    transition: all 0.5s ease;
  background-size: 400% 400%;
  animation: gradientShift 18s ease infinite;
}

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}



  /* === NAVBAR === */
  /* === NAVBAR === */
.navbar {
  background: linear-gradient(90deg, #00b8ff, #0066cc, #003c8f);
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.15);
  height: 70px;
  transition: all 0.4s ease;
  backdrop-filter: blur(6px);
}

.navbar-brand {
  font-weight: 700;
  color: white !important;
  font-size: 1.5rem;
  letter-spacing: 0.5px;
}

.navbar .bi-bell-fill {
  color: white;
  cursor: pointer;
  transition: 0.3s ease;
}

.navbar .bi-bell-fill:hover {
  color: #00e6b8;
  transform: rotate(10deg);
}

/* Navbar Links & Buttons */
.navbar a,
.navbar .btn-outline-info {
  color: white !important;
}

.btn-outline-info {
  border: 2px solid #00e6b8;
  color: #00e6b8 !important;
  background: transparent;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-outline-info:hover {
  background: #00e6b8;
  color: #003c8f !important;
}

/* === DARK MODE NAVBAR === */
[data-theme="dark"] .navbar {
  background: linear-gradient(90deg, #1e2a48, #0b132b);
  box-shadow: 0 3px 20px rgba(0, 0, 0, 0.6);
}

[data-theme="dark"] .navbar a,
[data-theme="dark"] .navbar .btn-outline-info {
  color: #e8f0ff !important;
}

[data-theme="dark"] .navbar .btn-outline-info:hover {
  background: #00e6b8;
  color: #0b132b !important;
}

/* === Dropdown styling (optional for consistency) === */
.dropdown-menu {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 10px;
  backdrop-filter: blur(8px);
  border: none;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

[data-theme="dark"] .dropdown-menu {
  background: rgba(20, 30, 60, 0.95);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
}

  /* === SIDEBAR === */
  /* === SIDEBAR === */
.sidebar {
  background: linear-gradient(180deg, #00b8ff, #0066cc, #003c8f); /* Soft teal-to-deep-blue gradient */
  width: 250px;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  padding-top: 90px;
  color: white;
  box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
  transition: background 0.4s ease;
}

.sidebar a {
  display: flex;
  align-items: center;
  color: white;
  text-decoration: none;
  padding: 14px 20px;
  font-weight: 500;
  transition: background 0.3s ease, border-left 0.3s ease;
}

.sidebar a:hover {
  background: rgba(255, 255, 255, 0.15);
  border-left: 4px solid #00e6b8; /* accent color when hovered */
}

.sidebar i {
  margin-right: 12px;
  font-size: 1.2rem;
}

/* === DARK MODE SIDEBAR === */
[data-theme="dark"] .sidebar {
  background: linear-gradient(180deg, #1e2a48, #0b132b); /* Deep navy tones */
  box-shadow: 4px 0 12px rgba(0, 0, 0, 0.4);
}

  /* === CONTENT === */
  .content {
    margin-left: 270px;
    padding: 100px 30px 30px;
    transition: all 0.4s ease;
  }

  h2 {
    font-weight: 700;
    color: var(--primary);
  }

  /* === CARDS === */
  .card-box {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 24px rgba(0, 102, 255, 0.08);
    transition: all 0.4s ease;
  }

  .card-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 195, 255, 0.25);
  }

  [data-theme="dark"] .card-box:hover {
    box-shadow: 0 6px 20px rgba(0, 195, 255, 0.15);
  }

  /* === SWITCH MODE BUTTON === */
  .btn-outline-info {
    border-color: var(--accent);
    color: var(--accent);
    font-weight: 500;
    transition: 0.3s ease;
  }

  .btn-outline-info:hover {
    background: var(--accent);
    color: #0b132b;
  }

  /* === NOTIFICATIONS DROPDOWN === */
  .notification-dropdown {
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    min-width: 270px;
    padding: 10px 0;
  }

  .notification-item {
    color: var(--dropdown-text);
    font-size: 0.9rem;
    padding: 10px 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .notification-item:hover {
    background: rgba(0, 102, 255, 0.08);
  }

  /* === DARK MODE SWITCH === */
  .switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 24px;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    border-radius: 24px;
    transition: 0.4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: 0.4s;
  }

  input:checked + .slider {
    background-color: var(--accent);
  }

  input:checked + .slider:before {
    transform: translateX(24px);
  }

  /* === WELCOME SECTION === */
  .welcome {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--primary);
  }

  .welcome span {
    color: var(--accent);
  }

  .text-muted {
    color: var(--text-muted) !important;
  }

  /* === FOOTER === */
  footer {
    text-align: center;
    padding: 25px;
    color: var(--text-muted);
    font-size: 0.9rem;
  }
</style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top px-3">
    <a class="navbar-brand" href="#">WorkBridge</a>
    <div class="d-flex align-items-center ms-auto gap-4">
      <!-- Notifications -->
      <div class="dropdown">
        <i class="bi bi-bell-fill fs-4 dropdown-toggle" data-bs-toggle="dropdown"></i>
        <div class="dropdown-menu dropdown-menu-end notification-dropdown">
          <div class="notification-item">
            <i class="bi bi-briefcase-fill text-primary"></i>
            <span>New job: Electrician needed in Westlands.</span>
          </div>
          <div class="notification-item">
            <i class="bi bi-chat-left-text-fill text-success"></i>
            <span>You have a new message from a client.</span>
          </div>
          <div class="notification-item">
            <i class="bi bi-star-fill text-warning"></i>
            <span>You received a 5★ review!</span>
          </div>
        </div>
      </div>
<!-- Switch Mode Button -->
<a href="{{ route('switch.mode') }}"
   class="btn btn-sm btn-outline-info ms-3">
   {{ Auth::user()->mode === 'worker' ? 'Become Client' : 'Become Worker' }}
</a>

      <!-- Dark Mode Toggle -->
      <label class="switch">
        <input type="checkbox" id="themeToggle" />
        <span class="slider"></span>
      </label>

      <!-- Profile -->
      <div class="dropdown">
        <a class="dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
          <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0066ff&color=fff&size=32" class="rounded-circle me-2" alt="Avatar">
          <span>{{ strtok(Auth::user()->name, ' ') }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <div class="sidebar">
    <a href="#"><i class="bi bi-house"></i> Dashboard</a>
    <a href="#"><i class="bi bi-briefcase"></i> My Jobs</a>
    <a href="#"><i class="bi bi-stars"></i> Ratings</a>
    <a href="#"><i class="bi bi-chat-dots"></i> Messages</a>
    <a href="#"><i class="bi bi-person-circle"></i> Profile</a>
    <a href="#"><i class="bi bi-gear"></i> Settings</a>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
      <div>
        <h2 class="welcome">Welcome back, <span>{{ Auth::user()->name }}</span> 👋</h2>
        <p class="text-muted">Here’s what’s happening today.</p>
      </div>
      <div class="text-end">
        <small class="text-muted">Logged in as <strong>{{ Auth::user()->role ?? 'Worker' }}</strong></small>
      </div>
    </div>

    <!-- Recommended Jobs -->
    <h4 class="section-title">Recommended Jobs For You</h4>
    <div class="row">
      @forelse($recommendedJobs as $job)
        <div class="col-md-4">
          <div class="job-card">
            <h5 class="fw-bold text-primary">{{ $job->title }}</h5>
            <p class="text-muted">{{ Str::limit($job->description, 90) }}</p>
            <p><i class="bi bi-geo-alt"></i> {{ $job->location ?? 'Nairobi, Kenya' }}</p>
            <button class="btn btn-apply w-100 text-white">Apply Now</button>
          </div>
        </div>
      @empty
        <p class="text-muted fade-in">No recommended jobs right now.</p>
      @endforelse
    </div>

    <!-- Available Jobs -->
    <h4 class="section-title">Available Jobs</h4>
    <div class="row">
      @forelse($availableJobs as $job)
        <div class="col-md-4">
          <div class="job-card">
            <h5 class="fw-bold">{{ $job->title }}</h5>
            <p class="text-muted">{{ Str::limit($job->description, 90) }}</p>
            <p><i class="bi bi-clock"></i> Posted {{ $job->created_at->diffForHumans() }}</p>
            <button class="btn btn-outline-primary w-100">View Details</button>
          </div>
        </div>
      @empty
        <p class="text-muted fade-in">No available jobs found.</p>
      @endforelse
    </div>

    <!-- Applied Jobs -->
    <h4 class="section-title">Jobs You’ve Applied For</h4>
    <div class="row">
      @forelse($appliedJobs as $application)
        <div class="col-md-6">
          <div class="job-card">
            <h5>{{ $application->job->title ?? 'Job Removed' }}</h5>
            <p>Status:
              <span class="badge
                @if($application->status=='pending') bg-warning
                @elseif($application->status=='accepted') bg-success
                @elseif($application->status=='rejected') bg-danger
                @else bg-secondary
                @endif">
                {{ ucfirst($application->status ?? 'Pending') }}
              </span>
            </p>
          </div>
        </div>
      @empty
        <p class="text-muted fade-in">You haven’t applied for any jobs yet.</p>
      @endforelse
    </div>
  </div>

  <footer>© {{ date('Y') }} WorkBridge | Empowering Skilled Workers 🌍</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Dark Mode Toggle
    const toggle = document.getElementById('themeToggle');
    toggle.addEventListener('change', function () {
      if (this.checked) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
      } else {
        document.documentElement.setAttribute('data-theme', 'light');
        localStorage.setItem('theme', 'light');
      }
    });

    // Persist theme
    if (localStorage.getItem('theme') === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
      toggle.checked = true;
    }
  </script>
</body>
</html>
