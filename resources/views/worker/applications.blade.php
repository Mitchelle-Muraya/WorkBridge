<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | My Applications</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root {
      --primary: #00b3ff;
      --accent: #00c9a7;
      --bg: #f8fafc;
      --card-bg: #ffffff;
      --text: #0f172a;
      --muted: #475569;
      --border: rgba(0, 0, 0, 0.08);
      --hover: rgba(0, 179, 255, 0.1);
    }

    [data-theme="dark"] {
      --bg: #0b0f19;
      --card-bg: #151c29;
      --text: #f8fafc;
      --muted: #cbd5e1;
      --border: rgba(255, 255, 255, 0.12);
      --hover: rgba(0, 179, 255, 0.15);
    }

    body {
      font-family: "Poppins", sans-serif;
      background: var(--bg);
      color: var(--text);
      transition: all 0.4s ease;
      margin: 0;
      min-height: 100vh;
    }

    .navbar {
      background: var(--card-bg);
      border-bottom: 1px solid var(--border);
      height: 70px;
    }

    .navbar-brand {
      color: var(--primary) !important;
      font-weight: 700;
      font-size: 1.5rem;
    }

    .theme-toggle {
      position: relative;
      width: 48px;
      height: 24px;
      background: #cfd8dc;
      border-radius: 24px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .theme-toggle::before {
      content: "";
      position: absolute;
      width: 18px;
      height: 18px;
      top: 3px;
      left: 3px;
      background: #fff;
      border-radius: 50%;
      transition: 0.3s;
    }

    .theme-toggle.active {
      background: var(--accent);
      box-shadow: 0 0 10px var(--accent);
    }

    .theme-toggle.active::before {
      left: 27px;
    }

    .content {
      max-width: 900px;
      margin: 100px auto;
      padding: 0 20px;
    }

    h2 {
      color: var(--primary);
      font-weight: 700;
      margin-bottom: 25px;
    }

    .application-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 22px;
      box-shadow: 0 6px 18px rgba(0, 179, 255, 0.08);
      transition: all 0.3s ease;
    }

    .application-card:hover {
      transform: translateY(-5px);
      border-color: var(--accent);
      box-shadow: 0 8px 25px var(--hover);
    }

    .application-card h5 {
      color: var(--text);
      margin-bottom: 6px;
    }

    .application-card small {
      color: var(--muted);
    }

    .badge {
      font-size: 0.85rem;
      padding: 5px 10px;
    }

    footer {
      text-align: center;
      padding: 30px;
      color: var(--muted);
      border-top: 1px solid var(--border);
    }
  </style>
</head>

<body>
  <nav class="navbar px-4 fixed-top">
    <a class="navbar-brand" href="#">WorkBridge</a>
    <div class="ms-auto d-flex align-items-center gap-3">
      <div id="themeToggle" class="theme-toggle"></div>
      <a href="{{ route('worker.dashboard') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
      </form>
    </div>
  </nav>

  <div class="content">
    <h2><i class="bi bi-briefcase-fill me-2"></i> My Job Applications</h2>

    @forelse($applications as $app)
      <div class="application-card mb-4">
        <div class="d-flex justify-content-between flex-wrap align-items-center">
          <div>
            <h5 class="fw-bold">{{ $app->job->title }}</h5>
            <p class="mb-1">
              <i class="bi bi-geo-alt text-primary"></i> {{ $app->job->location }}
            </p>
            <p class="mb-1 small">
              <strong>Status:</strong>
              <span class="badge
                {{ $app->status == 'pending' ? 'bg-warning text-dark' :
                   ($app->status == 'accepted' ? 'bg-success' : 'bg-danger') }}">
                {{ ucfirst($app->status) }}
              </span>
            </p>
            <small><i class="bi bi-calendar2"></i> Applied on {{ $app->created_at->format('M d, Y h:i A') }}</small>
          </div>

          @if($app->status == 'accepted')
            <a href="{{ route('messages.index') }}" class="btn btn-success mt-3 mt-sm-0">
              <i class="bi bi-chat-dots"></i> Message Employer
            </a>
          @endif
        </div>
      </div>
    @empty
      <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> You haven’t applied for any jobs yet.
      </div>
    @endforelse
  </div>

  <footer>© {{ date('Y') }} WorkBridge | Empowering Workers 🌍</footer>

  <script>
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    if (localStorage.getItem('theme') === 'dark') {
      html.setAttribute('data-theme', 'dark');
      themeToggle.classList.add('active');
    }
    themeToggle.addEventListener('click', () => {
      const isDark = html.getAttribute('data-theme') === 'dark';
      html.setAttribute('data-theme', isDark ? 'light' : 'dark');
      themeToggle.classList.toggle('active');
      localStorage.setItem('theme', isDark ? 'light' : 'dark');
    });
  </script>
</body>
</html>
