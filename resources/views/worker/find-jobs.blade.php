@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
  <h3 class="mb-4 fw-bold text-primary">💼 Available Jobs</h3>

  <!-- Search Section -->
<div class="d-flex mb-4" style="max-width: 500px;">
  <input type="text" id="jobSearch" class="form-control me-2"
         placeholder="Search jobs, e.g., plumber, electrician...">
  <button id="searchBtn" type="button" class="btn btn-primary">
      <i class="fas fa-search"></i>
  </button>
</div>

<!-- Results will appear here -->
<div id="searchResults">
  @foreach($jobs as $job)
      <a href="{{ route('jobs.show', $job->id) }}" class="text-decoration-none text-dark">
          <div class="card job-card border-0 shadow-sm mb-3">
              <div class="card-body">
                  <h5 class="fw-bold text-primary mb-1">{{ $job->title }}</h5>
                  <p class="text-muted small mb-1">{{ ucfirst($job->location) }}</p>
                  <p class="mb-0">{{ Str::limit($job->description, 100) }}</p>
                  <div class="text-end mt-2">
                      <span class="text-success small fw-semibold">View Details →</span>
                  </div>
              </div>
          </div>
      </a>
  @endforeach
</div>

<style>
  body {
    background-color: #f8f9fa;
  }
  .job-card {
    border-radius: 10px;
    transition: 0.2s ease-in-out;
    cursor: pointer;
  }
  .job-card:hover {
    transform: scale(1.01);
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    background-color: #ffffff;
  }
  .card-body {
    padding: 1rem 1.2rem;
  }
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

    // Prevent default form behavior if there's any
    $(document).on('submit', 'form', function(e) {
        if (!$(this).hasClass('apply-form')) {
            e.preventDefault();
        }
    });

    // Trigger AJAX when clicking the search button
    $('#searchBtn').on('click', function(e) {
        e.preventDefault(); // stop full-page reload
        doSearch();
    });

    // Trigger AJAX as the user types (optional)
    $('#jobSearch').on('keyup', function() {
        let term = $(this).val().trim();
        if (term.length > 1) {
            doSearch();
        } else {
            $('#searchResults').html(''); // clear when input is empty
        }
    });

    function doSearch() {
        const term = $('#jobSearch').val().trim();

        $.ajax({
            url: "{{ route('search.jobs') }}",
            type: 'GET',
            data: { term: term },
            beforeSend: function() {
                $('#searchResults').html('<div class="text-center text-muted mt-3">🔍 Searching...</div>');
            },
            success: function(data) {
                $('#searchResults').html(data);
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#searchResults').html('<div class="text-danger mt-3 text-center">Error fetching results</div>');
            }
        });
    }

});
</script>

@endsection
