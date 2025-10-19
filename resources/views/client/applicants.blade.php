<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | Job Applicants</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root {
      --primary: #0066ff;
      --accent: #00c9a7;
      --bg: #f3f5fa;
      --text: #1f1f1f;
      --card-bg: #ffffff;
      --sidebar-gradient: linear-gradient(180deg, #0066ff, #0040c1);
      --dropdown-text: #222;
      --text-muted: #6c757d;
    }

    [data-theme="dark"] {
      --primary: #3399ff;
      --accent: #00e6b8;
      --bg: #0b132b;
      --text: #f8f9fa;
      --card-bg: #1b2433;
      --sidebar-gradient: linear-gradient(180deg, #14213d, #0b132b);
      --dropdown-text: #f1f1f1;
      --text-muted: #a9b7c6;
    }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: "Poppins", sans-serif;
      transition: all 0.4s ease;
    }

    /* Navbar */
    .navbar {
      background: var(--card-bg);
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      height: 70px;
      transition: all 0.4s ease;
    }

    .navbar-brand {
      font-weight: 700;
      color: var(--primary) !important;
      font-size: 1.4rem;
    }

    .navbar .bi-bell-fill {
      color: var(--primary);
      cursor: pointer;
      transition: 0.3s ease;
    }

    .navbar .bi-bell-fill:hover {
      color: var(--accent);
      transform: rotate(10deg);
    }

    /* Sidebar */
    .sidebar {
      background: var(--sidebar-gradient);
      width: 250px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 90px;
      color: white;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      color: white;
      text-decoration: none;
      padding: 14px 20px;
      font-weight: 500;
      transition: background 0.3s ease;
    }

    .sidebar a:hover {
      background: rgba(255, 255, 255, 0.15);
      border-left: 4px solid var(--accent);
    }

    .sidebar i {
      margin-right: 12px;
      font-size: 1.2rem;
    }

    /* Content */
    .content {
      margin-left: 270px;
      padding: 100px 30px 30px;
      transition: all 0.4s ease;
    }

    h2 {
      font-weight: 700;
      color: var(--primary);
    }

    /* Applicant Card */
    .applicant-card {
      background: var(--card-bg);
      border-radius: 14px;
      padding: 25px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border-left: 5px solid transparent;
    }

    .applicant-card:hover {
      transform: translateY(-4px);
      border-left: 5px solid var(--accent);
      box-shadow: 0 10px 25px rgba(0, 201, 167, 0.2);
    }

    .text-muted {
      color: var(--text-muted) !important;
    }

    /* Notification Dropdown */
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

    /* Dark Mode Switch */
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

    footer {
      text-align: center;
      padding: 25px;
      color: #888;
      font-size: 0.9rem;
    }

    .welcome span {
      color: var(--accent);
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
            <i class="bi bi-person-fill text-primary"></i>
            <span>New applicant submitted their profile.</span>
          </div>
          <div class="notification-item">
            <i class="bi bi-chat-dots text-success"></i>
            <span>Message received from a worker.</span>
          </div>
        </div>
      </div>

      <!-- Dark Mode Toggle -->
      <label class="switch">
        <input type="checkbox" id="themeToggle" />
        <span class="slider"></span>
      </label>

      <!-- Profile -->
      <div class="dropdown">
        <a class="dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
          <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
          </div>
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
    <a href="#"><i class="bi bi-grid"></i> Dashboard</a>
    <a href="#"><i class="bi bi-plus-circle"></i> Post Job</a>
    <a href="#"><i class="bi bi-briefcase"></i> My Jobs</a>
    <a href="#"><i class="bi bi-chat-dots"></i> Messages</a>
    <a href="#"><i class="bi bi-star"></i> Reviews</a>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
      <div>
        <h2 class="welcome">Applicants for: <span>{{ $job->title }}</span></h2>
        <p class="text-muted">Manage and review workers who applied for your job posting.</p>
      </div>
      <div class="text-end">
        <small class="text-muted">Logged in as <strong>Client</strong></small>
      </div>
    </div>

    <div class="row">
      @forelse($applications as $app)
        <div class="col-md-6">
          <div class="applicant-card">
            <h5 class="fw-bold text-primary">{{ $app->user->name }}</h5>
            <p class="text-muted">Applied on {{ $app->created_at->format('M d, Y') }}</p>
            <p><i class="bi bi-envelope"></i> {{ $app->user->email }}</p>
            <p><i class="bi bi-geo-alt"></i> {{ $app->user->location ?? 'Not specified' }}</p>
            <form action="{{ route('client.rateWorker', $app->id) }}" method="POST" class="mt-3">
              @csrf
              <select name="rating" class="form-select mb-2" required>
                <option value="">Rate worker</option>
                <option value="5">5 - Excellent</option>
                <option value="4">4 - Good</option>
                <option value="3">3 - Average</option>
                <option value="2">2 - Poor</option>
                <option value="1">1 - Terrible</option>
              </select>
              <textarea name="comment" class="form-control mb-2" placeholder="Leave a comment (optional)"></textarea>
              <button type="submit" class="btn btn-apply w-100 text-white" style="background: var(--accent);">Submit Review</button>
            </form>
          </div>
        </div>
      @empty
        <p class="text-muted">No applicants yet for this job.</p>
      @endforelse
    </div>
  </div>

  <footer>© {{ date('Y') }} WorkBridge | Empowering Employers 🌍</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
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

    if (localStorage.getItem('theme') === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
      toggle.checked = true;
    }
  </script>
</body>
</html>
