<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | My Jobs</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
  :root {
    --primary: #00b3ff;
    --accent: #00c9a7;
    --bg: #f6f9ff;
    --card-bg: #ffffff;
    --text: #0f172a;
    --muted: #5a6575;
    --border: rgba(0,0,0,0.1);
    --sidebar-bg: linear-gradient(180deg, #00b3ff, #0077cc);
  }

  body {
    font-family: "Poppins", sans-serif;
    background: var(--bg);
    color: var(--text);
  }

  /* NAVBAR */
  .navbar {
    background: #ffffff;
    border-bottom: 1px solid var(--border);
    height: 70px;
  }
  .navbar-brand { color: var(--primary) !important; font-weight: 700; }

  /* SIDEBAR */
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
    padding: 14px 22px;
    font-weight: 500;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: 0.3s ease;
  }
  .sidebar a.active,
  .sidebar a:hover {
    background: rgba(255,255,255,0.15);
    border-left: 3px solid var(--accent);
  }

  /* CONTENT AREA */
  .content {
    margin-left: 270px;
    padding: 100px 40px;
  }

  /* TABLE DESIGN */
  table thead {
    background: #eef6ff;
  }
  th {
    padding: 15px;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text);
  }
  td {
    padding: 16px;
    vertical-align: middle;
  }
  tr {
    background: #fff;
    border-bottom: 1px solid #eee;
  }
  tr:hover {
    background: #f0faff;
  }

  .status-badge {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.85rem;
  }

  .btn-action {
    font-size: 1.2rem;
    cursor: pointer;
    margin-right: 12px;
    transition: 0.2s;
  }
  .btn-action:hover {
    transform: scale(1.2);
  }

  .edit-icon { color: #0077ff; }
  .delete-icon { color: #e63946; }
  </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top px-4 shadow-sm">
    <a class="navbar-brand" href="#">WorkBridge</a>
</nav>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="{{ route('client.dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a>
    <a href="{{ route('client.postJob') }}"><i class="bi bi-plus-circle me-2"></i> Post Job</a>
    <a href="{{ route('client.my-jobs') }}" class="active"><i class="bi bi-briefcase me-2"></i> My Jobs</a>
    <a href="{{ route('client.applications') }}"><i class="bi bi-envelope me-2"></i> Applications</a>
    <a href="{{ route('messages.index') }}"><i class="bi bi-chat-dots me-2"></i> Messages</a>
    <a href="{{ route('client.reviews') }}"><i class="bi bi-star me-2"></i> Reviews</a>
</div>

<!-- PAGE CONTENT -->
<div class="content">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-briefcase-fill text-primary me-2"></i> My Posted Jobs
    </h2>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($jobs->isEmpty())
      <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> You haven’t posted any jobs yet.
      </div>
    @else

    <div class="table-responsive shadow-sm rounded">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Job Title</th>
            <th>Budget</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Posted</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach($jobs as $job)
          <tr>
            <td>{{ $job->title }}</td>
            <td>Ksh {{ number_format($job->budget) }}</td>
            <td>{{ date('M d, Y', strtotime($job->deadline)) }}</td>
            <td>
              <span class="status-badge
                {{ $job->status == 'completed' ? 'bg-success text-white' :
                   ($job->status == 'in_progress' ? 'bg-warning text-dark' : 'bg-secondary text-white') }}">
                {{ ucfirst($job->status) }}
              </span>
            </td>
            <td>{{ $job->created_at->format('M d, Y') }}</td>

            <td class="text-center">

              <!-- EDIT -->
              <a href="{{ route('client.job.edit', $job->id) }}" class="btn-action edit-icon">
                <i class="bi bi-pencil-square"></i>
              </a>

              <!-- DELETE -->
              <form action="{{ route('client.job.delete', $job->id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Are you sure you want to delete this job?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn-action delete-icon border-0 bg-transparent">
                    <i class="bi bi-trash"></i>
                  </button>
              </form>

            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @endif
</div>

</body>
</html>
