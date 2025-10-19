<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | Job Applications</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    /* -------- THEME VARIABLES -------- */
    :root {
      --primary: #00b3ff;
      --accent: #00c9a7;
      --bg: #f6f9ff;
      --card-bg: #ffffff;
      --text: #0f172a;
      --muted: #475569;
      --border: rgba(0, 0, 0, 0.08);
      --shadow: rgba(0, 179, 255, 0.08);
      --hover: rgba(0, 179, 255, 0.15);
    }

    [data-theme="dark"] {
      --bg: #0b0f19;
      --card-bg: #151c29;
      --text: #f8fafc;
      --muted: #d0d8e5;
      --border: rgba(255, 255, 255, 0.1);
      --shadow: rgba(0, 179, 255, 0.15);
      --hover: rgba(0, 179, 255, 0.2);
    }

    body {
      font-family: "Poppins", sans-serif;
      background: var(--bg);
      color: var(--text);
      transition: all 0.4s ease;
      margin: 0;
      min-height: 100vh;
    }

    /* -------- NAVBAR -------- */
    .navbar {
      background: var(--card-bg);
      border-bottom: 1px solid var(--border);
      height: 70px;
      backdrop-filter: blur(10px);
    }

    .navbar-brand {
      font-weight: 700;
      color: var(--primary) !important;
      font-size: 1.6rem;
    }

    /* -------- THEME TOGGLE -------- */
    .theme-toggle {
      position: relative;
      width: 52px;
      height: 26px;
      background: #d0d7de;
      border-radius: 25px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .theme-toggle::before {
      content: "";
      position: absolute;
      width: 22px;
      height: 22px;
      top: 2px;
      left: 2px;
      background: #fff;
      border-radius: 50%;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      transition: all 0.3s;
    }

    [data-theme="dark"] .theme-toggle {
      background: var(--primary);
      box-shadow: 0 0 12px rgba(0, 179, 255, 0.4);
    }

    [data-theme="dark"] .theme-toggle::before {
      left: 28px;
      background: #151c29;
    }

    /* -------- CONTENT -------- */
    .content {
      max-width: 1000px;
      margin: 100px auto;
      padding: 0 20px;
    }

    h2 {
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 30px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 10px;
    }

    /* -------- APPLICATION CARDS -------- */
    .application-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 20px var(--shadow);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .application-card::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      width: 5px;
      height: 100%;
      background: linear-gradient(180deg, var(--primary), var(--accent));
      border-radius: 5px 0 0 5px;
    }

    .application-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 25px var(--hover);
    }

    .text-muted {
      color: var(--muted) !important;
    }

    /* -------- BUTTONS -------- */
    .btn-approve {
      background: var(--accent);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 8px 16px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-approve:hover {
      background: var(--primary);
      box-shadow: 0 0 12px rgba(0, 179, 255, 0.4);
    }

    .btn-reject {
      background: #ef4444;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 8px 16px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-reject:hover {
      background: #dc2626;
      box-shadow: 0 0 12px rgba(239, 68, 68, 0.4);
    }

    footer {
      text-align: center;
      padding: 25px;
      color: var(--muted);
      border-top: 1px solid var(--border);
      margin-top: 50px;
    }
  </style>
</head>

<body>
  <nav class="navbar px-4 fixed-top">
    <a class="navbar-brand" href="#">WorkBridge</a>
    <div class="ms-auto d-flex align-items-center gap-3">
      <div id="themeToggle" class="theme-toggle"></div>
      <a href="{{ route('client.dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-danger">Logout</button>
      </form>
    </div>
  </nav>

  <div class="content">
    <h2><i class="bi bi-people-fill me-2"></i>Job Applications</h2>

    @forelse ($applications as $app)
      <div class="application-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="fw-bold mb-1">{{ $app->job->title }}</h4>
            <p class="text-muted mb-1">
              <i class="bi bi-person-fill text-primary"></i>
              <strong class="text-muted">Applicant:</strong> {{ $app->user->name }}
            </p>
            <p class="mb-1">
              <strong>Status:</strong>
              <span class="badge
                {{ $app->status == 'pending' ? 'bg-warning text-dark' : ($app->status == 'accepted' ? 'bg-success' : 'bg-danger') }}">
                {{ ucfirst($app->status) }}
              </span>
            </p>
            <small class="text-muted">
              <i class="bi bi-calendar2-week"></i> Applied on {{ $app->created_at->format('M d, Y h:i A') }}
            </small>
          </div>

          @if($app->status == 'pending')
            <div class="mt-3 mt-sm-0">
              <form action="{{ route('applications.updateStatus', $app->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="accepted">
                <button type="submit" class="btn-approve me-2">
                  <i class="bi bi-check2-circle"></i> Approve
                </button>
              </form>

              <form action="{{ route('applications.updateStatus', $app->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="btn-reject">
                  <i class="bi bi-x-circle"></i> Reject
                </button>
              </form>
            </div>
          @endif
        </div>
      </div>
    @empty
      <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> No applications yet.
      </div>
    @endforelse
  </div>

  <footer>© {{ date('Y') }} WorkBridge | Empowering Employers 🌍</footer>

  <script>
    const html = document.documentElement;
    const toggle = document.getElementById("themeToggle");

    // Load saved theme
    if (localStorage.getItem("theme") === "dark") {
      html.setAttribute("data-theme", "dark");
    }

    toggle.addEventListener("click", () => {
      const isDark = html.getAttribute("data-theme") === "dark";
      html.setAttribute("data-theme", isDark ? "light" : "dark");
      localStorage.setItem("theme", isDark ? "light" : "dark");
    });
  </script>
</body>
</html>
