<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | My Jobs</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
  :root {
    --primary: #00b3ff;
    --accent: #00c9a7;
    --bg: #f6f9ff;
    --card-bg: #ffffff;
    --text: #0f172a;
    --muted: #5a6575;
    --border: rgba(0,0,0,0.08);
    --sidebar-bg: linear-gradient(180deg, #00b3ff, #0077cc);
    --navbar-bg: #ffffff;
  }

  [data-theme="dark"] {
    --bg: #0b0f19;
    --card-bg: #1a2233;
    --text: #ffffff;
    --muted: #cfd6e0;
    --border: rgba(255,255,255,0.08);
    --sidebar-bg: linear-gradient(180deg, #101828, #0b132b);
    --navbar-bg: rgba(0,0,0,0.92);
  }

  body {
    font-family: "Poppins", sans-serif;
    background: var(--bg);
    color: var(--text);
    transition: background 0.4s, color 0.4s;
  }

  .navbar {
    background: var(--navbar-bg);
    border-bottom: 1px solid var(--border);
    height: 70px;
    backdrop-filter: blur(10px);
  }

  .navbar-brand {
    color: var(--primary) !important;
    font-weight: 700;
    font-size: 1.5rem;
  }

  .sidebar {
    position: fixed;
    top: 0; left: 0;
    width: 250px;
    height: 100vh;
    background: var(--sidebar-bg);
    padding-top: 90px;
    color: white;
  }

  .sidebar a {
    display: flex;
    align-items: center;
    color: #fff;
    text-decoration: none;
    padding: 14px 22px;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
  }

  .sidebar a.active,
  .sidebar a:hover {
    background: rgba(255,255,255,0.15);
    border-left: 3px solid var(--accent);
  }

  .content {
    margin-left: 270px;
    padding: 100px 40px;
  }

  .job-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
  }

  .job-card:hover {
    transform: translateY(-3px);
    border-color: var(--primary);
  }

  .badge-status {
    font-size: 0.8rem;
    border-radius: 6px;
    padding: 5px 10px;
  }

  footer {
    text-align: center;
    color: var(--muted);
    padding: 25px;
    border-top: 1px solid var(--border);
    margin-top: 40px;
  }
  </style>
</head>

<body>
  <!-- 🔝 NAVBAR -->
  <nav class="navbar navbar-expand-lg fixed-top px-4">
    <a class="navbar-brand" href="#">WorkBridge</a>
    <div class="ms-auto d-flex align-items-center gap-4">
      <label class="switch mb-0" title="Toggle Light/Dark Mode">
        <input type="checkbox" id="themeToggle" />
        <span class="slider"></span>
      </label>
      <a href="{{ route('switch.mode') }}" class="btn btn-outline-info fw-semibold py-1 px-3">
        {{ Auth::user()->role === 'client' ? 'Become Worker' : 'Become Client' }}
      </a>
    </div>
  </nav>

  <!-- 📋 SIDEBAR -->
  <div class="sidebar">
    <a href="{{ route('client.dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a>
    <a href="{{ route('client.postJob') }}"><i class="bi bi-plus-circle me-2"></i> Post Job</a>
    <a href="{{ route('client.my-jobs') }}" class="active"><i class="bi bi-briefcase me-2"></i> My Jobs</a>
    <a href="{{ route('client.applications') }}"><i class="bi bi-envelope me-2"></i> Applications</a>
    <a href="{{ route('messages.index') }}"><i class="bi bi-chat-dots me-2"></i> Messages</a>
    <a href="{{ route('client.reviews') }}"><i class="bi bi-star me-2"></i> Reviews</a>
  </div>

  <!-- 💼 CONTENT -->
  <div class="content">
    <h2 class="fw-bold mb-4"><i class="bi bi-briefcase-fill text-primary me-2"></i>My Posted Jobs</h2>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if($jobs->isEmpty())
      <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> You haven’t posted any jobs yet.
      </div>
    @else
      @foreach($jobs as $job)
        <div class="job-card">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
              <h5 class="fw-bold mb-2">{{ $job->title }}</h5>
              <p class="text-muted mb-1">{{ Str::limit($job->description, 120) }}</p>
              <small class="text-muted">
                <i class="bi bi-calendar2-week"></i> Posted on {{ $job->created_at->format('M d, Y') }}
              </small>
            </div>
            <div>
              <span class="badge-status {{ $job->status == 'completed' ? 'bg-success' : ($job->status == 'in_progress' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                {{ ucfirst($job->status) }}
              </span>
            </div>
          </div>
        </div>
      @endforeach
    @endif
    @if(session('recommended_workers'))
  <div class="alert alert-info mt-4">
    <h5>🎯 Recommended Workers for your latest job</h5>
    <ul>
      @foreach(session('recommended_workers') as $worker)
        <li>
          <strong>{{ $worker['name'] }}</strong> — Skills: {{ $worker['skills'] }}
        </li>
      @endforeach
    </ul>
  </div>
@endif

  </div>

  <footer>© {{ date('Y') }} WorkBridge | Empowering Employers 🌍</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  // 🌙 Theme toggle
  const html = document.documentElement;
  const toggle = document.getElementById('themeToggle');
  if (localStorage.getItem('theme') === 'dark') {
    html.setAttribute('data-theme', 'dark');
    toggle.checked = true;
  }
  toggle.addEventListener('change', () => {
    const isDark = toggle.checked;
    html.setAttribute('data-theme', isDark ? 'dark' : 'light');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });
  </script>
</body>
</html>
