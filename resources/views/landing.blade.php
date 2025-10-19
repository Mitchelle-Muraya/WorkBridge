<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WorkBridge – Connect to Jobs and Talent</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    /* ---------- GLOBAL ---------- */
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: "Poppins", sans-serif;
      color: white;
      scroll-behavior: smooth;
      overflow-x: hidden;
    }

    body {
      background: #000;
    }

    /* ---------- SLIDESHOW ---------- */
    .slideshow {
      position: fixed;
      inset: 0;
      width: 100%;
      height: 100%;
      z-index: -3;
      overflow: hidden;
    }

    .slideshow img {
      position: absolute;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      animation: fadeCycle 32s infinite;
      filter: brightness(0.65) contrast(1.05);
    }

    .slideshow img:nth-child(1) { animation-delay: 0s; }
    .slideshow img:nth-child(2) { animation-delay: 8s; }
    .slideshow img:nth-child(3) { animation-delay: 16s; }
    .slideshow img:nth-child(4) { animation-delay: 24s; }

    @keyframes fadeCycle {
      0%, 25% { opacity: 1; }
      30%, 100% { opacity: 0; }
    }

    /* ---------- OVERLAY ---------- */
    .overlay {
      position: fixed;
      inset: 0;
      background: linear-gradient(180deg, rgba(0,0,0,0.65), rgba(0,0,0,0.4));
      z-index: -2;
    }

    /* ---------- NAVBAR ---------- */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 60px;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 10;
      background: rgba(0,0,0,0.35);
      backdrop-filter: blur(10px);
      transition: background 0.3s ease;
    }

    .navbar:hover { background: rgba(0,0,0,0.55); }

    .brand {
      font-weight: 700;
      font-size: 1.6rem;
      color: #00d1ff;
    }

    .nav-links {
      display: flex;
      gap: 25px;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }

    .nav-links a:hover { color: #00d1ff; }

    /* ---------- HERO ---------- */
    .hero {
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 0 20px;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 15px;
      text-shadow: 0 2px 25px rgba(0, 209, 255, 0.3);
    }

    .hero h1 span { color: #00d1ff; }

    .hero p {
      font-size: 1.1rem;
      margin-bottom: 35px;
      opacity: 0.95;
    }

    /* ---------- SEARCH BAR ---------- */
    .search-bar {
      display: flex;
      justify-content: center;
      margin-bottom: 25px;
    }

    .search-bar input {
      width: 60%;
      padding: 14px;
      border: none;
      border-radius: 30px 0 0 30px;
      outline: none;
      background: rgba(255,255,255,0.1);
      color: white;
    }

    .search-bar input::placeholder { color: #ccc; }

    .search-bar button {
      padding: 14px 24px;
      background: linear-gradient(90deg, #007bff, #00b8ff);
      border: none;
      border-radius: 0 30px 30px 0;
      color: white;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s;
    }

    .search-bar button:hover {
      background: #00b8ff;
      box-shadow: 0 0 15px rgba(0,136,255,0.6);
    }

    /* ---------- CATEGORIES ---------- */
    .categories {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 25px;
    }

    .category {
      text-align: center;
      transition: 0.3s;
      width: 100px;
    }

    .category i {
      font-size: 2rem;
      margin-bottom: 8px;
      color: #00d1ff;
      transition: all 0.3s ease;
    }

    .category:hover i {
      color: #00ffcc;
      transform: scale(1.2);
    }

    /* ---------- CTA ---------- */
    .cta { margin-top: 35px; }

    .cta a {
      background: linear-gradient(90deg, #00b8ff, #007bff);
      color: #fff;
      padding: 12px 28px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      box-shadow: 0 0 15px rgba(0,136,255,0.5);
      transition: all 0.3s ease;
    }

    .cta a:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 25px rgba(0,204,255,0.7);
    }

    /* ---------- ABOUT / CONTACT ---------- */
    #about, #contact {
      padding: 100px 20px;
      text-align: center;
      background: rgba(0,0,0,0.55);
      backdrop-filter: blur(12px);
      border-top: 1px solid rgba(255,255,255,0.05);
    }

    #about h2, #contact h2 {
      font-size: 2rem;
      color: #00d1ff;
      margin-bottom: 20px;
    }

    #about p {
      max-width: 800px;
      margin: 0 auto;
      color: #e0e0e0;
      line-height: 1.8;
    }

    .contact-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 50px;
      margin-top: 40px;
    }

    .contact-details i {
      font-size: 1.8rem;
      color: #00d1ff;
      margin-bottom: 10px;
    }

    .contact-details a {
      display: block;
      color: #ccc;
      margin-bottom: 15px;
      text-decoration: none;
    }

    .contact-details a:hover { color: #00d1ff; }

    .contact-form input, .contact-form textarea {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: none;
      background: rgba(255,255,255,0.1);
      color: white;
      margin-bottom: 15px;
    }

    .contact-form button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, #00b8ff, #007bff);
      border: none;
      border-radius: 8px;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
    }

    /* ---------- FOOTER ---------- */
    footer {
      text-align: center;
      padding: 25px;
      background: rgba(0,0,0,0.8);
      color: #aaa;
      border-top: 1px solid rgba(255,255,255,0.05);
    }

    footer a { color: #00d1ff; text-decoration: none; }
    footer a:hover { text-decoration: underline; }
  </style>
</head>

<body>
  <!-- SLIDESHOW -->
  <div class="slideshow">
    <img src="{{ asset('images/image_1.png') }}" alt="">
    <img src="{{ asset('images/image_3.png') }}" alt="">
    <img src="{{ asset('images/image_7.png') }}" alt="">
    <img src="{{ asset('images/image_9.png') }}" alt="">
  </div>
  <div class="overlay"></div>

  <!-- NAVBAR -->
  <div class="navbar">
    <div class="brand">WorkBridge</div>
    <div class="nav-links">
      <a href="#about">About</a>
      <a href="#contact">Contact</a>
      @auth
        <a href="{{ route('worker.dashboard') }}">Dashboard</a>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
      @else
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Sign Up</a>
      @endauth
    </div>
  </div>

  <!-- HERO -->
  <section class="hero">
    <h1><span id="typed-text"></span></h1>
    <p>Hire skilled professionals or find work that matches your abilities.</p>

    <div class="search-bar">
      <input type="text" placeholder="What service do you need today?">
      <button><i class="fas fa-search"></i></button>
    </div>

    <div class="categories">
      <div class="category"><i class="fas fa-hammer"></i><p>Repairs</p></div>
      <div class="category"><i class="fas fa-broom"></i><p>Cleaning</p></div>
      <div class="category"><i class="fas fa-paint-roller"></i><p>Painting</p></div>
      <div class="category"><i class="fas fa-tools"></i><p>Installation</p></div>
      <div class="category"><i class="fas fa-laptop-code"></i><p>Tech</p></div>
      <div class="category"><i class="fas fa-utensils"></i><p>Catering</p></div>
    </div>

    <div class="cta">
      @auth
        <a href="{{ route('worker.dashboard') }}">Go to Dashboard</a>
      @else
        <a href="{{ route('register') }}">Get Started</a>
      @endauth
    </div>
  </section>

  <!-- ABOUT -->
  <section id="about">
    <h2>About WorkBridge</h2>
    <p>
      WorkBridge is Kenya’s digital bridge between talent and opportunity.
      We empower skilled professionals — from electricians to designers — by giving them online visibility and easy access to jobs that fit their expertise.
      Our goal is to build a trusted platform where employers and workers collaborate seamlessly and confidently.
    </p>
  </section>

  <!-- CONTACT -->
  <section id="contact">
    <h2>Contact Us</h2>
    <div class="contact-container">
      <div class="contact-details">
        <i class="fas fa-envelope"></i>
        <a href="mailto:support@workbridge.co.ke">support@workbridge.co.ke</a>
        <i class="fas fa-phone"></i>
        <a href="tel:+254712345678">+254 712 345 678</a>
        <i class="fas fa-map-marker-alt"></i>
        <a href="https://www.google.com/maps/place/Nairobi,+Kenya" target="_blank">Nairobi, Kenya</a>
      </div>
      <div class="contact-form">
        <form method="POST" action="/contact">
          @csrf
          <input type="text" name="name" placeholder="Your Name" required>
          <input type="email" name="email" placeholder="Your Email" required>
          <textarea name="message" rows="4" placeholder="Your Message" required></textarea>
          <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
        </form>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    © {{ date('Y') }} WorkBridge — Empowering Kenya’s Skilled Workforce 🌍
    <br>Built with ❤️ by <a href="#">Mitchelle Muraya</a>
  </footer>

  <!-- Typed.js -->
  <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      new Typed("#typed-text", {
        strings: [
          "Connecting Kenya’s skilled professionals...",
          "Empowering your next hire...",
          "Find work that truly fits your skills."
        ],
        typeSpeed: 55,
        backSpeed: 30,
        loop: true
      });
    });
  </script>
</body>
</html>
