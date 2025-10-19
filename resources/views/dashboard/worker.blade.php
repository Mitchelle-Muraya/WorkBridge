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
    --primary: #00b3ff;
    --accent: #00c9a7;
    --bg: #f8fafc;
    --card-bg: #ffffff;
    --text: #000000;
    --muted: #5f6c7b;
    --border: rgba(0, 0, 0, 0.08);
    --sidebar-bg: linear-gradient(180deg, #00b3ff, #0077cc);
    --navbar-bg: #ffffff;
  }

  [data-theme="dark"] {
  --bg: #0b0f19;
  --card-bg: #1a2233;
  --text: #ffffff;
  --muted: #e4e8ee; /* brighter muted text for visibility */
  --border: rgba(255, 255, 255, 0.12);
  --sidebar-bg: linear-gradient(180deg, #101828, #0b132b);
  --navbar-bg: rgba(0, 0, 0, 0.92);
}
[data-theme="dark"] .text-muted {
  color: #d8dee9 !important; /* bright soft grey for visibility */
}



  body {
    font-family: "Poppins", sans-serif;
    background: var(--bg);
    color: var(--text);
    margin: 0;
    transition: background 0.4s, color 0.4s;
  }

  /* NAVBAR */
  .navbar {
    background: var(--navbar-bg);
    border-bottom: 1px solid var(--border);
    height: 70px;
    backdrop-filter: blur(10px);
    transition: all 0.4s ease;
  }

  .navbar-brand {
    color: var(--primary) !important;
    font-weight: 700;
    font-size: 1.5rem;
  }

  #notifIcon {
    font-size: 1.5rem;
    color: #ffb703;
    cursor: pointer;
    transition: transform 0.2s, color 0.2s;
  }

  #notifIcon:hover {
    color: var(--accent);
    transform: scale(1.1);
  }

  /* THEME TOGGLE */
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
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
  }

  .theme-toggle.active {
    background: var(--accent);
    box-shadow: 0 0 10px var(--accent);
  }

  .theme-toggle.active::before {
    left: 27px;
  }

  /* BUTTONS */
  .btn-outline-info {
    border: 1.5px solid var(--primary);
    color: var(--primary);
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .btn-outline-info:hover {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 3px 8px rgba(0, 179, 255, 0.3);
  }

  .btn-apply {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 18px;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .btn-apply:hover {
    background: var(--accent);
    box-shadow: 0 4px 10px rgba(0, 201, 167, 0.3);
  }

  /* SIDEBAR */
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: var(--sidebar-bg);

    padding-top: 90px;
  }

  .sidebar a {
    display: flex;
    align-items: center;
    color: #fff;
    text-decoration: none;
    padding: 14px 22px;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: 0.3s;
  }


  .sidebar a.active {
    background: rgba(255, 255, 255, 0.15);
    border-left: 3px solid var(--accent);
  }

  /* CONTENT */
  .content {
    margin-left: 270px;
    padding: 100px 40px;
    min-height: 100vh;
  }

  h2 {
    font-weight: 700;
    color: var(--text);
    transition: color 0.3s ease;
  }

  h2 span {
    color: var(--text);
  }

  [data-theme="dark"] h2,
  [data-theme="dark"] h2 span {
    color: #ffffff;
  }

  /* PLACEHOLDER VISIBILITY */
  ::placeholder {
    color: var(--muted);
    opacity: 1;
  }

  [data-theme="dark"] ::placeholder {
    color: #cfd7e0;
  }

  /* ALERT BOX */
  .alert-custom {
    background: rgba(255, 213, 79, 0.15);
    border: 1px solid rgba(255, 213, 79, 0.25);
    color: #b45309;
    border-radius: 10px;
    font-size: 0.95rem;
    padding: 14px 18px;
  }

  [data-theme="dark"] .alert-custom {
    background: rgba(255, 213, 79, 0.12);
    color: #ffea80;
  }

  /* JOB CARDS */
  .job-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 25px;
    border: 1px solid var(--border);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .job-card:hover {
    transform: translateY(-3px);
    border-color: var(--primary);
  }

  .job-card h5,
  .job-card p {
    color: var(--text);
  }

  /* SEARCH */
  .search-container {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
  }

  .search-container input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--card-bg);
    color: var(--text);
    outline: none;
  }

  .search-container button {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 18px;
    transition: 0.3s;
  }

  .search-container button:hover {
    background: var(--accent);
  }

  /* FOOTER */
  footer {
    text-align: center;
    padding: 30px;
    color: var(--muted);
    font-size: 0.9rem;
    border-top: 1px solid var(--border);
    transition: color 0.3s ease;
  }

  [data-theme="dark"] footer {
    color: #cfd7e0;
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

.sidebar a:hover,
.sidebar a.active {
  background: rgba(255, 255, 255, 0.15);
  border-left: 3px solid var(--accent);
}


</style>

</head>

<body>
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

      <div id="themeToggle" class="theme-toggle"></div>
      <a href="{{ route('switch.mode') }}" class="btn btn-outline-info fw-semibold py-1 px-3">
        {{ Auth::user()->role === 'worker' ? 'Become Client' : 'Become Worker' }}
      </a>
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

  <div class="sidebar">
  <a href="{{ route('worker.dashboard') }}" class="{{ request()->routeIs('worker.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid me-2"></i> Dashboard
  </a>

  <a href="{{ route('worker.appliedJobs') }}" class="{{ request()->routeIs('worker.appliedJobs') ? 'active' : '' }}">
    <i class="bi bi-briefcase me-2"></i> My Jobs
  </a>

  <a href="{{ route('worker.applications') }}" class="{{ request()->routeIs('worker.applications') ? 'active' : '' }}">
    <i class="bi bi-envelope me-2"></i> Applications
  </a>

  <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.index') ? 'active' : '' }} position-relative">
    <i class="bi bi-chat-dots me-2"></i> Messages
    <span id="msgBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small d-none">0</span>
  </a>

  <a href="{{ route('worker.reviews') }}" class="{{ request()->routeIs('worker.reviews') ? 'active' : '' }}">
    <i class="bi bi-star me-2"></i> Reviews
  </a>

  <a href="{{ route('worker.profile') }}" class="{{ request()->routeIs('worker.profile') ? 'active' : '' }}">
    <i class="bi bi-person-circle me-2"></i> Profile
  </a>

  <a href="{{ route('worker.settings') }}" class="{{ request()->routeIs('worker.settings') ? 'active' : '' }}">
    <i class="bi bi-gear me-2"></i> Settings
  </a>
</div>


  <div class="content">
    @if($profileIncomplete)
      <div class="alert-custom mb-4">
        <strong>Complete your profile!</strong> Add your skills and experience to unlock job applications.
        <a href="{{ route('worker.profile') }}" class="btn btn-sm btn-warning ms-2 fw-semibold">Complete Now</a>
      </div>
    @endif

    <h2 class="dashboard-title">Welcome back, <span>{{ Auth::user()->name }}</span> 👋</h2>
    <p class="text-muted mb-4">Here’s what’s happening today.</p>

    <form method="GET" action="{{ route('worker.dashboard') }}" class="search-container">
      <input type="text" name="query" value="{{ request('query') }}" placeholder="Search for jobs..." />
      <button type="submit"><i class="bi bi-search"></i></button>
    </form>

    <h4 class="fw-semibold mt-4 mb-3"><i class="bi bi-stars text-warning"></i> Recommended Jobs</h4>
    @if($recommendedJobs->isEmpty())
      <div class="job-card text-center text-muted">No recommended jobs right now. (Coming soon)</div>
    @else
      @foreach($recommendedJobs as $job)
        <div class="job-card mb-4"><h5 class="fw-bold">{{ $job->title }}</h5><p class="text-muted small">{{ $job->description }}</p></div>
      @endforeach
    @endif

    <h4 class="fw-semibold mt-4 mb-3"><i class="bi bi-briefcase-fill text-primary"></i> Available Jobs</h4>
    @if($jobs->isEmpty())
      <div class="job-card text-center text-muted">No available jobs found.</div>
    @else
      @foreach($jobs as $job)
        <div class="job-card mb-4">
          <h5 class="fw-bold">{{ $job->title }}</h5>
          <p class="text-muted small"><i class="bi bi-geo-alt"></i> {{ $job->location }}</p>
          <p class="text-muted small"><i class="bi bi-tags"></i> {{ $job->category }}</p>
          <p class="small text-muted">Budget: <span class="fw-semibold text-success">Ksh {{ number_format($job->budget) }}</span></p>
          <p class="small text-muted">Deadline: {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}</p>
          @php $hasApplied = $applications->contains('job_id', $job->id); @endphp
          @if($hasApplied)
            <button class="btn btn-success w-100" disabled><i class="bi bi-check-circle"></i> Applied</button>
          @else
            <form action="{{ route('apply.job', $job->id) }}" method="POST">@csrf
              <button type="submit" class="btn-apply w-100"><i class="bi bi-send"></i> Apply</button>
            </form>
          @endif
        </div>
      @endforeach
    @endif

   <h4 class="fw-semibold mt-4 mb-3"><i class="bi bi-check-circle-fill text-success"></i> Jobs You’ve Applied For</h4>
@if($applications->isEmpty())
  <div class="job-card text-muted">You haven’t applied for any jobs yet.</div>
@else
  @foreach($applications as $app)
    <div class="job-card mb-4">
      <h6 class="fw-bold">{{ $app->job->title }}</h6>
      <p class="small mb-1">
        <strong>Status:</strong>
        <span class="badge bg-{{ $app->status == 'pending' ? 'warning' : ($app->status == 'accepted' ? 'success' : 'danger') }}">
          {{ ucfirst($app->status) }}
        </span>
      </p>




  </div>

    @endforeach
@endif




  <footer>© {{ date('Y') }} WorkBridge | Empowering Skilled Workers 🌍</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// ---------------------------
// 🌗 THEME TOGGLE HANDLER
// ---------------------------
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

// ---------------------------
// 🔔 FETCH JOB NOTIFICATIONS
// ---------------------------
function fetchNotifications() {
  $.ajax({
    url: "{{ route('notifications.fetch') }}",
    method: 'GET',
    success: function(data) {
      const notifDropdown = $('#notifDropdown');
      const notifCount = $('#notifCount');

      notifDropdown.empty();

      if (data.length > 0) {
        notifCount.removeClass('d-none').text(data.length);

        data.forEach(n => {
          notifDropdown.append(`
            <li class="p-2 border-bottom small">
              <strong>${n.title}</strong><br>
              <span class="text-muted">${n.message}</span>
            </li>
          `);
        });

        notifDropdown.append(`
          <li class="text-center mt-2">
            <button id="markReadBtn" class="btn btn-sm btn-outline-primary">
              Mark all as read
            </button>
          </li>
        `);
      } else {
        notifCount.addClass('d-none');
        notifDropdown.append('<li class="text-center text-muted">No new notifications</li>');
      }
    }
  });
}

// ---------------------------
// 💬 FETCH MESSAGE UNREAD COUNT
// ---------------------------
function fetchMessageCount() {
  $.ajax({
    url: "{{ route('messages.list') }}", // ChatController@chatList
    method: 'GET',
    success: function(data) {
      let unreadTotal = 0;
      data.forEach(chat => unreadTotal += chat.unread);

      const msgBadge = $('#msgBadge');
      if (unreadTotal > 0) {
        msgBadge.removeClass('d-none').text(unreadTotal);
      } else {
        msgBadge.addClass('d-none');
      }
    }
  });
}

// ---------------------------
// 🔁 REFRESH BOTH EVERY 10 SECONDS
// ---------------------------
setInterval(() => {
  fetchNotifications();
  fetchMessageCount();
}, 10000);

// Initial load
fetchNotifications();
fetchMessageCount();

// ---------------------------
// ✅ MARK ALL NOTIFICATIONS AS READ
// ---------------------------
$(document).on('click', '#markReadBtn', function() {
  $.ajax({
    url: "{{ route('notifications.markRead') }}",
    method: 'POST',
    data: { _token: '{{ csrf_token() }}' },
    success: function() {
      fetchNotifications();
    }
  });
});

// ---------------------------
// 💬 CHAT MODAL INTERACTION
// ---------------------------
$(document).on('click', '.openChat', function(e) {
  e.preventDefault();

  const jobId = $(this).data('job');
  const receiverId = $(this).data('receiver');
  const clientName = $(this).data('name');
  const clientAvatar = $(this).data('avatar');

  // Update modal header
  $('#chatModalLabel').text(clientName);
  $('#chatAvatar').attr('src', clientAvatar);
  $('#chatStatus').text('online 🟢');

  // Highlight active chat
  $('.openChat').removeClass('active-chat');
  $(this).addClass('active-chat');

  // Show modal
  $('#chatModal').modal('show');

  // Fetch chat messages
  $.get(`/chat/fetch/${jobId}/${receiverId}`, function(data) {
    let html = '';
    const authId = @json(Auth::id());

    data.forEach(msg => {
      const isSent = msg.sender_id === authId;
      html += `
        <div class="d-flex ${isSent ? 'justify-content-end' : 'justify-content-start'}">
          <div class="msg-bubble ${isSent ? 'msg-sent' : 'msg-received'}">
            ${msg.message}
          </div>
        </div>
      `;
    });

    $('#chatContent').html(html).scrollTop($('#chatContent')[0].scrollHeight);
  });

  // Send message
  $('#chatForm').off('submit').on('submit', function(ev) {
    ev.preventDefault();
    const message = $('#chatMessage').val().trim();
    if (!message) return;

    $.post('/chat/send', {
      _token: '{{ csrf_token() }}',
      job_id: jobId,
      receiver_id: receiverId,
      message: message
    }, function() {
      $('#chatMessage').val('');
      $('.openChat[data-job="' + jobId + '"]').click(); // reload chat
      fetchMessageCount(); // update unread badge instantly
    });
  });
});

// ---------------------------
// 🔁 AUTO REFRESH OPEN CHAT
// ---------------------------
setInterval(function() {
  if ($('#chatModal').hasClass('show')) {
    $('.openChat.active-chat').click();
  }
}, 5000);

// ---------------------------
// 🕓 STATIC STATUS FOR NOW
// ---------------------------
$('#chatStatus').text('last seen 2 hours ago');
</script>
