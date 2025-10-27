<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | WorkBridge</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  @vite(['resources/css/app.css'])
  <style>
    /* ====== GLOBAL STYLES ====== */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f6f9ff;
      transition: background 0.3s ease, color 0.3s ease;
    }

    /* ====== SIDEBAR ====== */
    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, #007bff, #00c9a7);
      color: #fff;
      min-height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      padding: 20px 0;
      overflow-y: auto;
      transition: all 0.3s ease;
    }

    .sidebar.collapsed {
      width: 80px;
      text-align: center;
    }

    .sidebar h4 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 30px;
      color: #fff;
    }

    .sidebar a {
      display: block;
      padding: 12px 25px;
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      transition: 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(255,255,255,0.15);
      border-left: 4px solid #fff;
    }

    .sidebar.collapsed a {
      padding: 12px 10px;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar.collapsed a span {
      display: none;
    }

    /* ====== MAIN CONTENT ====== */
    .main-content {
      margin-left: 250px;
      padding: 25px;
      transition: margin-left 0.3s ease;
    }

    .main-content.expanded {
      margin-left: 80px;
    }

    /* ====== NAVBAR ====== */
    .navbar {
      background: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      position: sticky;
      top: 0;
      z-index: 100;
      border-radius: 8px;
    }

    .navbar h5 {
      font-weight: 600;
      color: #007bff;
    }

    /* ====== DARK MODE ====== */
    body.dark-mode {
      background-color: #1e1e2f;
      color: #f5f5f5;
    }

    body.dark-mode .navbar {
      background: #2d2d3e;
      color: #f5f5f5;
    }

    body.dark-mode .sidebar {
      background: linear-gradient(180deg, #111122, #2d2d3e);
    }

    body.dark-mode .sidebar a {
      color: #ddd;
    }

    body.dark-mode .sidebar a.active {
      background: rgba(255,255,255,0.2);
    }

    body.dark-mode .card {
      background: #2d2d3e;
      color: #fff;
    }
    .card {
  transition: all 0.3s ease-in-out;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 0 20px rgba(0, 179, 255, 0.25);
}

  </style>
</head>

<body>
  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <h4><i class="bi bi-gear-fill me-2"></i><span>WorkBridge</span></h4>
    <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> <span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}"><i class="bi bi-people"></i> <span>Users</span></a>
    <a href="{{ route('admin.jobs') }}"><i class="bi bi-briefcase"></i> <span>Jobs</span></a>
    <a href="{{ route('admin.reports') }}" class="active"><i class="bi bi-bar-chart-line"></i> <span>Reports</span></a>
    <a href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i> <span>Settings</span></a>

    <hr class="mx-3 text-white">
    <a href="{{ route('logout') }}" class="text-danger"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content" id="mainContent">
    <nav class="navbar p-3 mb-4 shadow-sm">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
          <button id="toggleSidebar" class="btn btn-outline-primary btn-sm me-2">
            <i class="bi bi-list"></i>
          </button>
          <h5 class="d-inline">Admin Panel</h5>
        </div>

        <div class="d-flex align-items-center gap-3">
          <button id="darkModeToggle" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-moon"></i>
          </button>
          <span class="fw-semibold">{{ Auth::user()->name ?? 'Admin' }}</span>
          <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=007bff&color=fff"
               class="rounded-circle" width="35" height="35">
        </div>
      </div>
    </nav>

    @yield('content')
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // === Sidebar Toggle ===
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebar');
    const darkToggle = document.getElementById('darkModeToggle');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
    });

    // === Dark Mode Toggle ===
    const enableDark = () => {
      document.body.classList.add('dark-mode');
      localStorage.setItem('darkMode', 'enabled');
      darkToggle.innerHTML = '<i class="bi bi-sun"></i>';
    };
    const disableDark = () => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('darkMode', 'disabled');
      darkToggle.innerHTML = '<i class="bi bi-moon"></i>';
    };

    // Load preference
    if (localStorage.getItem('darkMode') === 'enabled') {
      enableDark();
    }

    darkToggle.addEventListener('click', () => {
      if (document.body.classList.contains('dark-mode')) disableDark();
      else enableDark();
    });
  </script>
</body>
</html>
