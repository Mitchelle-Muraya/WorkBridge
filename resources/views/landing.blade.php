<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WorkBridge - Welcome</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: Arial, sans-serif;
      overflow: hidden; /* Prevent scrolling */
      color: white;
    }

    /* Background slideshow */
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

    /* Overlay for readability */
    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
      z-index: -1;
    }

    /* Navbar */
    .navbar {
      position: absolute;
      top: 20px;
      right: 30px;
      z-index: 2;
      display: flex;
      gap: 20px;
      align-items: center;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      font-size: 1rem;
      font-weight: bold;
      transition: 0.3s;
    }

    .navbar .btn-client {
      padding: 8px 16px;
      border: 2px solid white;
      border-radius: 20px;
      color: white;
    }

    .navbar .btn-client:hover {
      background: white;
      color: #007bff;
    }

    /* Hero Section */
    .hero {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      z-index: 1;
      max-width: 800px;
    }

    .hero h1 {
      font-size: 2.8rem;
      margin-bottom: 15px;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 25px;
    }

    /* Search bar */
    .search-bar {
      display: flex;
      justify-content: center;
      margin-bottom: 35px;
    }

    .search-bar input {
      padding: 14px;
      width: 70%;
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

    /* Categories Section */
    .categories {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }

    .category {
      text-align: center;
      transition: 0.3s;
      color: white;
    }

    .category i {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #00d1ff;
    }

    .category:hover {
      transform: translateY(-5px);
    }
  </style>
</head>
<body>
  <!-- Background slideshow -->
  <div class="slideshow"></div>
  <div class="overlay"></div>

  <!-- Navbar -->
  <div class="navbar">
    <a href="{{ route('about') }}">About</a>
    <a href="{{ route('contact') }}">Contact</a>
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Sign Up</a>

  </div>

  <!-- Hero Section -->
  <div class="hero">
    <h1>WorkBridge connects you to jobs and talent</h1>
    <p>Hire skilled workers or find jobs that match your skills- all in one place</p>

    <!-- Search bar -->
    <div class="search-bar">
      <input type="text" placeholder="What do you need today?">
      <button><i class="fas fa-search"></i></button>
    </div>
    <div class="flex justify-center gap-4 mt-6">

</div>


    <!-- Categories -->
    <div class="categories">
      <div class="category"><i class="fas fa-hammer"></i><p>Repairs</p></div>
      <div class="category"><i class="fas fa-broom"></i><p>Cleaning</p></div>
      <div class="category"><i class="fas fa-truck"></i><p>Moving</p></div>
      <div class="category"><i class="fas fa-paint-roller"></i><p>Painting</p></div>
      <div class="category"><i class="fas fa-tools"></i><p>Installation</p></div>
      <div class="category"><i class="fas fa-laptop-code"></i><p>Tech Support</p></div>
      <div class="category"><i class="fas fa-utensils"></i><p>Catering</p></div>

    </div>
  </div>
</body>
</html>
