<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge | Job Applications</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
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

    .content { max-width: 1000px; margin: 100px auto; padding: 0 20px; }
    h2 { font-weight: 700; color: var(--primary); margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }

    .application-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 20px var(--shadow);
      margin-bottom: 20px;
    }

    .rating input { display: none; }
    .rating label {
      font-size: 1.5rem;
      color: #ccc;
      cursor: pointer;
    }
    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label { color: gold; }

    footer { text-align: center; padding: 25px; color: var(--muted); border-top: 1px solid var(--border); margin-top: 50px; }
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
    <h2><i class="bi bi-people-fill me-2"></i>Job Applications</h2>

    @forelse ($applications as $app)
      <div class="application-card">
        <h4 class="fw-bold mb-1">{{ $app->job->title }}</h4>
        <p class="text-muted mb-1"><strong>Applicant:</strong> {{ $app->user->name }}</p>
        <p><strong>Status:</strong>
          <span class="badge
            {{ $app->status == 'pending' ? 'bg-warning text-dark' : ($app->status == 'accepted' ? 'bg-success' : 'bg-danger') }}">
            {{ ucfirst($app->status) }}
          </span>
        </p>
        <small class="text-muted"><i class="bi bi-calendar2-week"></i> Applied on {{ $app->created_at->format('M d, Y h:i A') }}</small>

        @if($app->status == 'accepted')
        <div class="mt-3">
          <button class="btn btn-success btn-sm mark-complete-btn"
                  data-job-id="{{ $app->job->id }}"
                  data-worker-id="{{ $app->job->worker_id }}">
            Mark as Completed
          </button>

          <div id="review-section-{{ $app->job->id }}" class="mt-3" style="display:none;">
            <form class="review-form">
              @csrf
              <input type="hidden" name="job_id" value="{{ $app->job->id }}">
              <input type="hidden" name="worker_id" value="{{ $app->job->worker_id }}">

              <div class="mb-2">
                <label>Your Rating:</label>
                <div class="rating">
                  @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" id="star{{ $i }}-{{ $app->job->id }}" value="{{ $i }}">
                    <label for="star{{ $i }}-{{ $app->job->id }}">★</label>
                  @endfor
                </div>
              </div>
              <textarea name="comment" class="form-control mb-2" placeholder="Write your review..."></textarea>
              <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
            </form>
          </div>
        </div>
        @endif
      </div>
    @empty
      <div class="alert alert-info text-center"><i class="bi bi-info-circle"></i> No applications yet.</div>
    @endforelse
  </div>

  <footer>© {{ date('Y') }} WorkBridge | Empowering Employers 🌍</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.mark-complete-btn').forEach(button => {
      button.addEventListener('click', function() {
        let jobId = this.dataset.jobId;
        let workerId = this.dataset.workerId;
        let token = document.querySelector('meta[name="csrf-token"]').content;

        fetch(`/jobs/complete/${jobId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById(`review-section-${jobId}`).style.display = 'block';
          } else {
            alert(data.message || 'Error marking job as completed.');
          }
        })
        .catch(err => console.error(err));
      });
    });

    document.querySelectorAll('.review-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        let token = document.querySelector('meta[name="csrf-token"]').content;

        fetch("{{ route('reviews.store') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(Object.fromEntries(new FormData(this)))
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Review submitted successfully!');
            this.closest('.application-card').remove(); // hide the job card
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
