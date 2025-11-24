<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | Job Applications</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root {
      --primary: #00b3ff;
      --accent: #00c9a7;
      --bg: #f6f9ff;
      --card-bg: #ffffff;
      --text: #0f172a;
      --muted: #475569;
      --border: rgba(0, 0, 0, 0.08);
    }

    body {
      font-family: "Poppins", sans-serif;
      background: var(--bg);
      color: var(--text);
    }

    .navbar {
      background: #ffffff;
      border-bottom: 1px solid var(--border);
      height: 70px;
    }
    .navbar-brand {
      font-weight: 700;
      color: var(--primary) !important;
    }

    .content {
      max-width: 1200px;
      margin: 100px auto;
      padding: 0 20px;
    }

    table thead {
      background: #eef6ff;
    }
    th {
      padding: 15px;
      font-size: 0.92rem;
      font-weight: 600;
    }
    td {
      padding: 15px;
      vertical-align: middle;
      background: white;
      border-bottom: 1px solid #eee;
    }
    tr:hover {
      background: #f0faff;
    }

    .rating input { display: none; }
    .rating label {
      font-size: 1.3rem;
      cursor: pointer;
      color: #ccc;
      margin-right: 3px;
    }
    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label {
      color: gold;
    }

    .btn-sm {
      padding: 6px 14px;
      font-size: 0.85rem;
    }
  </style>
</head>

<body>

<nav class="navbar px-4 fixed-top">
  <a class="navbar-brand" href="#">WorkBridge</a>
  <div class="ms-auto d-flex align-items-center gap-3">
    <a href="{{ route('client.dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-outline-danger">Logout</button>
    </form>
  </div>
</nav>

<div class="content">
  <h2 class="fw-bold mb-4">
    <i class="bi bi-people-fill text-primary me-2"></i> Job Applications
  </h2>

  @if($applications->isEmpty())
    <div class="alert alert-info text-center">
      <i class="bi bi-info-circle"></i> No applications yet.
    </div>
  @else

  <div class="table-responsive rounded shadow-sm">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>Job Title</th>
          <th>Applicant</th>
          <th>Status</th>
          <th>Applied</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        @foreach ($applications as $app)
        <tr>
          <td>{{ $app->job->title }}</td>
          <td>{{ $app->user->name }}</td>

          <td>
            <span class="badge
              {{ $app->status == 'pending' ? 'bg-warning text-dark' :
                 ($app->status == 'accepted' ? 'bg-success' : 'bg-danger') }}">
                {{ ucfirst($app->status) }}
            </span>
          </td>

          <td>{{ $app->created_at->format('M d, Y h:i A') }}</td>

          <td>

            {{-- ========== PENDING STATUS ========== --}}
            @if($app->status === 'pending')

              <!-- Accept -->
              <form action="{{ route('applications.accept', $app->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-success btn-sm">Accept</button>
              </form>

              <!-- Reject -->
              <form action="{{ route('applications.reject', $app->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-danger btn-sm">Reject</button>
              </form>

              <!-- Message -->
              <a href="{{ route('chat.start', ['jobId' => $app->job->id, 'receiverId' => $app->user->id]) }}"
                 class="btn btn-primary btn-sm">
                <i class="bi bi-chat-dots"></i> Message
              </a>


            {{-- ========== ACCEPTED STATUS ========== --}}
            @elseif($app->status === 'accepted')

              <!-- Mark as Completed -->
              <button class="btn btn-success btn-sm mark-complete-btn"
                      data-job-id="{{ $app->job->id }}">
                Mark as Completed
              </button>

              <!-- Message -->
              <a href="{{ route('chat.start', ['jobId' => $app->job->id, 'receiverId' => $app->user->id]) }}"
                 class="btn btn-primary btn-sm">
                <i class="bi bi-chat-dots"></i> Message
              </a>

              <!-- Review Section -->
              <div id="review-section-{{ $app->job->id }}" style="display:none;" class="mt-2">

                <form class="review-form">
                  @csrf
                  <input type="hidden" name="job_id" value="{{ $app->job->id }}">
                  <input type="hidden" name="worker_id" value="{{ $app->user->id }}">

                  <div class="rating mb-1">
                    @for ($i = 5; $i >= 1; $i--)
                      <input type="radio" name="rating" id="star{{ $i }}-{{ $app->job->id }}" value="{{ $i }}">
                      <label for="star{{ $i }}-{{ $app->job->id }}">★</label>
                    @endfor
                  </div>

                  <textarea name="comment" class="form-control mb-2"
                    placeholder="Write your review..."></textarea>

                  <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                </form>

              </div>


            {{-- ========== REJECTED STATUS ========== --}}
            @else
              —
            @endif

          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.mark-complete-btn').forEach(button => {
  button.addEventListener('click', function() {

    let jobId = this.dataset.jobId;
    let token = document.querySelector('meta[name="csrf-token"]').content;
    let btn = this;

    fetch(`/jobs/complete/${jobId}`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {

        // CHANGE BUTTON
        btn.textContent = "✔ Marked as Completed";
        btn.classList.remove("btn-success");
        btn.classList.add("btn-secondary");
        btn.disabled = true;

        // OPEN REVIEW
        document.getElementById(`review-section-${jobId}`).style.display = 'block';
      }
    })
    .catch(err => console.error(err));
  });
});


document.querySelectorAll('.review-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let token = document.querySelector('meta[name="csrf-token"]').content;

        fetch("{{ route('reviews.store') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Review submitted successfully!');
                this.closest('tr').remove();
            } else {
                alert(data.message || 'Could not save review.');
            }
        })
        .catch(err => console.error(err));
    });
});
</script>

</body>
</html>
