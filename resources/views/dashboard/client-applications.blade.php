

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | Job Applications</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
/* ---------- THEME VARIABLES ---------- */
:root {
  --primary: #00b3ff;
  --accent: #00c9a7;
  --bg: #f6f9ff;
  --card-bg: #ffffff;
  --text: #0f172a;
  --muted: #5a6575;
  --border: rgba(0, 0, 0, 0.08);
  --sidebar-bg: linear-gradient(180deg, #00b3ff, #0077cc);
  --navbar-bg: #ffffff;
  --hover: rgba(0, 179, 255, 0.1);
}

[data-theme="dark"] {
  --bg: radial-gradient(circle at top left, #141a29, #0b0f19 70%);
  --card-bg: #1a2233;
  --text: #f8fafc;
  --muted: #cfd6e0;
  --border: rgba(255, 255, 255, 0.08);
  --sidebar-bg: linear-gradient(180deg, #101828, #0b132b);
  --navbar-bg: rgba(0, 0, 0, 0.88);
  --hover: rgba(0, 179, 255, 0.12);
}

/* ---------- BODY ---------- */
body {
  font-family: "Poppins", sans-serif;
  background: var(--bg);
  color: var(--text);
  margin: 0;
  transition: background 0.4s, color 0.4s;
}

/* ---------- NAVBAR ---------- */
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

/* ---------- SIDEBAR ---------- */
.sidebar {
  background: var(--sidebar-bg);
  width: 250px;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  padding-top: 90px;
  color: #fff;
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

.sidebar a:hover {
  background: rgba(255,255,255,0.15);
  border-left: 3px solid var(--accent);
  box-shadow: inset 3px 0 8px rgba(0, 179, 255, 0.2);
}

.sidebar a.active {
  background: rgba(255,255,255,0.18);
  border-left: 3px solid var(--accent);
  box-shadow: 0 0 8px rgba(0,179,255,0.3);
}

.sidebar a i {
  margin-right: 12px;
  font-size: 1.2rem;
}

/* ---------- CONTENT ---------- */
.content {
  margin-left: 270px;
  padding: 100px 40px;
}

h2 {
  font-weight: 700;
  color: var(--primary);
}

/* ---------- APPLICATION CARDS ---------- */
.application-card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 25px;
  box-shadow: 0 6px 20px rgba(0, 179, 255, 0.08);
  transition: all 0.3s;
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
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 179, 255, 0.25);
}

.text-muted {
  color: var(--muted) !important;
}

/* ---------- BUTTONS ---------- */
.btn-approve {
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 8px 16px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-approve:hover {
  background: var(--primary);
  box-shadow: 0 0 12px rgba(0,179,255,0.4);
}

.btn-reject {
  background: #ef4444;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 8px 16px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-reject:hover {
  background: #dc2626;
  box-shadow: 0 0 12px rgba(239,68,68,0.4);
}

/* ---------- FOOTER ---------- */
footer {
  text-align: center;
  padding: 25px;
  color: var(--muted);
  border-top: 1px solid var(--border);
  margin-top: 40px;
}
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
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
  border-radius: 24px;
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
.btn-outline-info {
  border: 1.5px solid var(--primary);
  color: var(--primary);
  font-weight: 600;
  border-radius: 10px;
  padding: 8px 16px;
  transition: 0.3s;
}

.btn-outline-info:hover {
  background: linear-gradient(90deg, var(--primary), var(--accent));
  color: white;
  transform: scale(1.05);
}
/* ---------- CHAT MODAL STYLING ---------- */
.chat-modal {
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 0 25px rgba(0, 179, 255, 0.25);
  background: var(--card-bg);
  color: var(--text);
  transition: background 0.4s, color 0.4s;
}

/* Gradient Header (matches theme) */
.chat-header {
  background: linear-gradient(90deg, var(--primary), var(--accent));
  color: #fff;
  border-bottom: none;
  padding: 14px 20px;
}

/* Chat Body (scrollable area) */
.chat-body {
  background: var(--bg);
  height: 400px;
  overflow-y: auto;
  padding: 20px;
}

/* Chat Footer */
.chat-footer {
  background: var(--card-bg);
  border-top: 1px solid var(--border);
}

/* Message Bubbles */
.msg-bubble {
  max-width: 75%;
  padding: 10px 14px;
  border-radius: 16px;
  margin-bottom: 8px;
  word-wrap: break-word;
  animation: fadeIn 0.3s ease-in-out;
}

/* Sent vs Received */
.msg-sent {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: #fff;
  align-self: flex-end;
  border-bottom-right-radius: 4px;
}

.msg-received {
  background: #e4e8f0;
  color: #0f172a;
  border-bottom-left-radius: 4px;
}

/* Dark Mode Adjustments */
[data-theme="dark"] .msg-received {
  background: #2a3245;
  color: #e4e8f0;
}

.btn-send {
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.btn-send:hover {
  background: var(--primary);
  box-shadow: 0 0 10px rgba(0,179,255,0.4);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}



  </style>
</head>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top px-4">
  <a class="navbar-brand" href="#">WorkBridge</a>

  <div class="ms-auto d-flex align-items-center gap-4">

    <!-- 🔔 Notification Bell -->
    <div class="dropdown position-relative">
      <i id="notifIcon" class="bi bi-bell-fill fs-4 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;"></i>
      <span id="notifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small d-none">0</span>

      <ul id="notifDropdown" class="dropdown-menu dropdown-menu-end shadow-sm p-2" style="width:280px; max-height:300px; overflow-y:auto;">
        <li class="text-center text-muted">No new notifications</li>
      </ul>
    </div>

    <!-- 🌗 Theme Toggle Switch -->
    <label class="switch mb-0" title="Toggle Light/Dark Mode">
      <input type="checkbox" id="themeToggle" />
      <span class="slider"></span>
    </label>

    <!-- 🔁 Switch Role Button -->
    <a href="{{ route('switch.mode') }}" class="btn btn-outline-info fw-semibold py-1 px-3">
      {{ Auth::user()->role === 'client' ? 'Become Client' : 'Become Worker' }}
    </a>

    <!-- 👤 Profile Dropdown -->
    <div class="dropdown">
      <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" data-bs-toggle="dropdown">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=00b3ff&color=fff&size=32"
             class="rounded-circle me-2">
        <span>{{ strtok(Auth::user()->name, ' ') }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
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


  <!-- SIDEBAR -->
  <div class="sidebar">
    <a href="{{ route('client.dashboard') }}"><i class="bi bi-grid"></i> Dashboard</a>

    <a href="{{ route('client.postJob') }}"><i class="bi bi-plus-circle"></i> Post Job</a>
    @if(session('recommended_workers'))
<div class="mt-4">
  <h4>👷 Recommended Workers for Your Job</h4>
  <ul>
    @foreach(session('recommended_workers') as $worker)
      <li>{{ $worker }}</li>
    @endforeach
  </ul>
</div>
@endif

    <a href="{{ route('client.my-jobs') }}"><i class="bi bi-briefcase"></i> My Jobs</a>

    <a href="{{ route('client.applications') }}" class="active"><i class="bi bi-envelope-open"></i> Applications</a>
    <a href="{{ route('messages.index') }}" class="position-relative">
  <i class="bi bi-chat-dots"></i> Messages
  <span id="msgBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small d-none">0</span>
</a>
    <a href="#"><i class="bi bi-star"></i> Reviews</a>
  </div>
<!-- 🌟 DASHBOARD SUMMARY SECTION -->
<h2 class="mb-4 text-primary"><i class="bi bi-speedometer2 me-2"></i>Dashboard Overview</h2>

<div class="row g-4 mb-5">
  <div class="col-md-4">
    <div class="application-card text-center py-4">
      <i class="bi bi-briefcase-fill fs-1 text-primary mb-2"></i>
      <h5 class="fw-bold">Total Jobs Posted</h5>
      <h3 class="fw-bold text-dark">{{ $totalJobs ?? 0 }}</h3>
    </div>
  </div>

  <div class="col-md-4">
    <div class="application-card text-center py-4">
      <i class="bi bi-gear-fill fs-1 text-info mb-2"></i>
      <h5 class="fw-bold">Jobs In Progress</h5>
      <h3 class="fw-bold text-dark">{{ $jobsInProgress ?? 0 }}</h3>
    </div>
  </div>

  <div class="col-md-4">
    <div class="application-card text-center py-4">
      <i class="bi bi-check-circle-fill fs-1 text-success mb-2"></i>
      <h5 class="fw-bold">Completed Jobs</h5>
      <h3 class="fw-bold text-dark">{{ $completedJobs ?? 0 }}</h3>
    </div>
  </div>
</div>

<hr class="my-5">

  <!-- CONTENT -->
  <div class="content">
    <h2 class="mb-4"><i class="bi bi-people-fill me-2"></i>Job Applications</h2>

   <h2 class="mb-4 text-primary"><i class="bi bi-speedometer2 me-2"></i>Dashboard Overview</h2>

<div class="row g-4">
  <div class="col-md-4">
    <div class="application-card text-center py-4">
      <i class="bi bi-briefcase-fill fs-1 text-primary mb-2"></i>
      <h5 class="fw-bold">Total Jobs Posted</h5>
      <h3 class="fw-bold text-dark">{{ $totalJobs ?? 0 }}</h3>
    </div>
  </div>

  <div class="col-md-4">
    <div class="application-card text-center py-4">
      <i class="bi bi-gear-fill fs-1 text-info mb-2"></i>
      <h5 class="fw-bold">Jobs In Progress</h5>
      <h3 class="fw-bold text-dark">{{ $jobsInProgress ?? 0 }}</h3>
    </div>
  </div>

  <div class="col-md-4">
    <div class="application-card text-center py-4">
      <i class="bi bi-check-circle-fill fs-1 text-success mb-2"></i>
      <h5 class="fw-bold">Completed Jobs</h5>
      <h3 class="fw-bold text-dark">{{ $completedJobs ?? 0 }}</h3>
    </div>
  </div>
</div>

<hr class="my-5">

<h4 class="mt-5 mb-3">📈 Recent Applications</h4>
@if(isset($applications) && $applications->isNotEmpty())
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>Worker</th>
          <th>Job Title</th>
          <th>Applied On</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($applications as $app)
          <tr>
            <td>{{ $app->user->name }}</td>
            <td>{{ $app->job->title }}</td>
            <td>{{ $app->created_at->format('M d, Y') }}</td>
            <td><span class="badge bg-info">{{ ucfirst($app->status) }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@else
  <div class="alert alert-info">No recent applications yet.</div>
@endif
