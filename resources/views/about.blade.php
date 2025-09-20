<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>About WorkBridge</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/4b9ba14b0f.js" crossorigin="anonymous"></script>
  <style>
    .hero-bg {
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                  url('{{ asset('images/image_9.png') }}') center/cover no-repeat;
    }
    .counter-number { font-size: 2rem; font-weight: 700; color: #0ea5e9; }
  </style>
</head>
<body class="text-gray-900">

  <!-- Back button -->
  <button onclick="history.back()" class="fixed top-5 left-5 bg-blue-600 text-white px-3 py-2 rounded-md shadow-md z-50">← Back</button>

  <!-- Hero -->
  <header class="hero-bg text-white">
    <div class="relative z-10 max-w-5xl mx-auto text-center py-32 px-6">
      <h1 class="text-4xl md:text-5xl font-extrabold">Connecting Skilled Workers to Businesses — Effortlessly</h1>
      <p class="mt-4 text-lg md:text-xl text-gray-200">WorkBridge uses skill-based matching and smart recommendations so clients find the right worker fast.</p>
      <a href="{{ url('/register') }}" class="inline-block mt-8 border-2 border-white px-6 py-3 rounded-full font-semibold hover:bg-white hover:text-blue-700 transition">Get Started</a>
    </div>
  </header>

  <!-- Who we are -->
  <section class="max-w-6xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-12 items-center">
    <div>
      <h2 class="text-3xl font-bold text-blue-700">Who We Are</h2>
      <p class="mt-4 text-gray-600 leading-relaxed">WorkBridge is a platform that connects clients and skilled workers using intelligent matching — ensuring faster hiring and fair opportunities.</p>
      <ul class="mt-6 space-y-3 text-gray-700">
        <li><i class="fas fa-check-circle text-blue-600 mr-2"></i> Profile & skill verification</li>
        <li><i class="fas fa-check-circle text-blue-600 mr-2"></i> Smart job–worker recommendations</li>
        <li><i class="fas fa-check-circle text-blue-600 mr-2"></i> Secure communication & payments</li>
      </ul>
    </div>
    <div>
      <img id="team-img"
           src="{{ asset('images/image_1.jpg') }}"
           onerror="this.onerror=null; this.src='https://source.unsplash.com/800x600/?team,work';"
           alt="Team working together"
           class="rounded-lg shadow-lg w-full object-cover h-80 md:h-96">
    </div>
  </section>

  <!-- Counters -->
  <section id="counters" class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto text-center">
      <h2 class="text-3xl font-bold mb-12">Our Impact</h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        <div class="p-6 shadow-md rounded-lg bg-white">
          <i class="fas fa-user-check text-blue-600 text-4xl mb-4"></i>
          <h3 id="workers" class="counter-number">0</h3>
          <p class="mt-2 text-gray-600">Workers Connected</p>
        </div>

        <div class="p-6 shadow-md rounded-lg bg-white">
          <i class="fas fa-briefcase text-green-600 text-4xl mb-4"></i>
          <h3 id="jobs" class="counter-number">0</h3>
          <p class="mt-2 text-gray-600">Jobs Posted</p>
        </div>

        <div class="p-6 shadow-md rounded-lg bg-white">
          <i class="fas fa-handshake text-purple-600 text-4xl mb-4"></i>
          <h3 id="clients" class="counter-number">0</h3>
          <p class="mt-2 text-gray-600">Clients Served</p>
        </div>

        <div class="p-6 shadow-md rounded-lg bg-white">
          <i class="fas fa-star text-yellow-500 text-4xl mb-4"></i>
          <h3 id="reviews" class="counter-number">0</h3>
          <p class="mt-2 text-gray-600">Reviews Given</p>
        </div>
      </div>
    </div>
  </section>

  <!-- How it works -->
  <section class="max-w-6xl mx-auto px-6 py-20">
    <h3 class="text-2xl font-semibold text-blue-700 text-center">How WorkBridge Works</h3>
    <div class="mt-10 grid md:grid-cols-4 gap-6">
      <div class="p-6 bg-white rounded-xl shadow-sm">
        <h4 class="font-bold">1. Create Profile</h4>
        <p class="mt-2 text-gray-600">Add skills & resume for better matches.</p>
      </div>
      <div class="p-6 bg-white rounded-xl shadow-sm">
        <h4 class="font-bold">2. Post or Browse Jobs</h4>
        <p class="mt-2 text-gray-600">Clients list jobs. Workers apply and get recommended.</p>
      </div>
      <div class="p-6 bg-white rounded-xl shadow-sm">
        <h4 class="font-bold">3. Collaborate</h4>
        <p class="mt-2 text-gray-600">Chat, set milestones, and complete work.</p>
      </div>
      <div class="p-6 bg-white rounded-xl shadow-sm">
        <h4 class="font-bold">4. Get Paid</h4>
        <p class="mt-2 text-gray-600">Secure payouts and feedback.</p>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="bg-blue-700 text-white py-20 text-center">
    <div class="max-w-4xl mx-auto px-6">
      <h3 class="text-3xl font-semibold">Ready to join WorkBridge?</h3>
      <a href="{{ url('/register') }}" class="inline-block mt-6 bg-white text-blue-700 px-6 py-3 rounded-full font-semibold shadow hover:bg-gray-100 transition">Sign Up</a>
    </div>
  </section>

  <!-- Counter Script -->
  <script>
    function animateValue(el, end, duration = 1500) {
      let start = 0, startTime = null;
      const step = (ts) => {
        if (!startTime) startTime = ts;
        const progress = Math.min((ts - startTime) / duration, 1);
        el.textContent = Math.floor(progress * end).toLocaleString();
        if (progress < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    }

    document.addEventListener("DOMContentLoaded", () => {
      const section = document.getElementById("counters");
      const targets = { workers: 500, jobs: 1200, clients: 800, reviews: 300 };

      const startCounters = () => {
        for (let id in targets) {
          const el = document.getElementById(id);
          if (el) animateValue(el, targets[id], 1500 + Math.random() * 800);
        }
      };

      if ("IntersectionObserver" in window) {
        new IntersectionObserver((entries, obs) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              startCounters();
              obs.disconnect();
            }
          });
        }, { threshold: 0.4 }).observe(section);
      } else {
        setTimeout(startCounters, 500);
      }
    });
  </script>
</body>
</html>
