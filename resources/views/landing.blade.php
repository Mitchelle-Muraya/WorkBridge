<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WorkBridge - Connect to Jobs and Talent</title>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden; /* Prevent scrolling */
      font-family: "Poppins", sans-serif;
      color: white;
    }

    /* === Background slideshow === */
    .slideshow {
      position: fixed;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      animation: slideShow 25s infinite;
      z-index: -2;
    }

    @keyframes slideShow {
      0%   { background-image: url('/images/image_1.png'); }
      33%  { background-image: url('/images/image_3.png'); }
      66%  { background-image: url('/images/image_9.png'); }
      100% { background-image: url('/images/image_7.png'); }
    }

    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: -1;
    }

    /* === Navbar === */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 60px;
      position: absolute;
      width: 100%;
      top: 0;
      z-index: 5;
      box-sizing: border-box;
    }

    .brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: #00d1ff;
      letter-spacing: 1px;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 25px;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      font-size: 1rem;
      transition: color 0.3s;
    }

    .nav-links a:hover {
      color: #00d1ff;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: center;
        padding: 15px;
      }
      .nav-links {
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
      }
      .brand {
        margin-bottom: 10px;
      }
    }

    /* === Hero Section === */
    .hero {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      width: 90%;
      max-width: 900px;
    }

    .hero h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 15px;
      line-height: 1.3;
    }

    .hero p {
      font-size: 1.1rem;
      margin-bottom: 30px;
      opacity: 0.9;
    }

    /* === Search Bar === */
    .search-bar {
      display: flex;
      justify-content: center;
      margin-bottom: 30px;
    }

    .search-bar input {
      width: 60%;
      padding: 14px;
      border: none;
      border-radius: 30px 0 0 30px;
      outline: none;
      font-size: 1rem;
    }

    .search-bar button {
      padding: 14px 24px;
      background: #007bff;
      border: none;
      border-radius: 0 30px 30px 0;
      color: white;
      font-size: 1rem;
      cursor: pointer;
    }

    .search-bar button:hover {
      background: #0056b3;
    }

    /* === Categories === */
    .categories {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 25px;
      margin-top: 10px;
    }

    .category {
      text-align: center;
      color: white;
      transition: 0.3s;
      width: 100px;
    }

    .category i {
      font-size: 2rem;
      margin-bottom: 8px;
      color: #00d1ff;
    }

    .category p {
      margin: 0;
      font-size: 0.95rem;
    }

    .category:hover {
      transform: translateY(-5px);
    }

    /* === CTA === */
    .cta {
      margin-top: 35px;
    }

    .cta a {
      background: #00d1ff;
      color: #000;
      padding: 12px 28px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .cta a:hover {
      background: white;
      color: #007bff;
    }

    /* === Footer Info === */
    .info {
      position: absolute;
      bottom: 10px;
      width: 100%;
      text-align: center;
      font-size: 0.9rem;
      opacity: 0.85;
      color: #d9d9d9;
    }
  </style>
</head>

<body>
  <div class="slideshow"></div>
  <div class="overlay"></div>

  <!-- Navbar -->
  <div class="navbar">
    <div class="brand">WorkBridge</div>
    <div class="nav-links">
      <a href="#about">About</a>
      <a href="#contact">Contact</a>

      @auth
        @if(Auth::user()->role == 'worker')
          <a href="{{ route('worker.dashboard') }}">Dashboard</a>
        @elseif(Auth::user()->role == 'client')
          <a href="{{ route('client.dashboard') }}">Dashboard</a>
        @elseif(Auth::user()->role == 'admin')
          <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
        @endif
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
      @else
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Sign Up</a>
      @endauth
    </div>
  </div>

  <!-- Hero Section -->
  <div class="hero">
    <h1>
      {{ Auth::check() ? 'Welcome back, ' . strtok(Auth::user()->name, ' ') . ' 👋' : 'WorkBridge connects you to jobs and talent' }}
    </h1>
    <p>{{ $message ?? 'Hire skilled workers or find jobs that match your skills — all in one place.' }}</p>

    <div class="search-bar">
      <input type="text" placeholder="What do you need today?">
      <button><i class="fas fa-search"></i></button>
    </div>

    <div class="categories">
      <div class="category"><i class="fas fa-hammer"></i><p>Repairs</p></div>
      <div class="category"><i class="fas fa-broom"></i><p>Cleaning</p></div>
      <div class="category"><i class="fas fa-truck"></i><p>Moving</p></div>
      <div class="category"><i class="fas fa-paint-roller"></i><p>Painting</p></div>
      <div class="category"><i class="fas fa-tools"></i><p>Installation</p></div>
      <div class="category"><i class="fas fa-laptop-code"></i><p>Tech Support</p></div>
      <div class="category"><i class="fas fa-utensils"></i><p>Catering</p></div>
    </div>

    <div class="cta">
      @auth
        <a href="{{ route('worker.dashboard') }}">Go to Dashboard</a>
      @else
        <a href="{{ route('register') }}">Get Started</a>
      @endauth
    </div>
  </div>

  <div class="info">
    © {{ date('Y') }} WorkBridge — Empowering Kenya’s skilled workforce 🌍
  </div>
</body>
</html>
