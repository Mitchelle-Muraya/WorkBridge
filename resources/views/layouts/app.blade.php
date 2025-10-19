<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkBridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">WorkBridge</a>
    <div>
        @auth
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-light btn-sm ms-2">Logout</button>
        </form>
        @else
        <a href="{{ route('login') }}" class="btn btn-light btn-sm">Login</a>
        <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm ms-2">Sign Up</a>
        @endauth
    </div>
  </div>
</nav>

<main class="py-4">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
