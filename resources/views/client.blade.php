<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Become a Client - WorkBridge</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/4b9ba14b0f.js" crossorigin="anonymous"></script>
</head>
<body class="text-gray-900 bg-gray-50">

  <!-- Hero Section -->
  <header class="relative bg-blue-700 text-white">
    <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image:url('https://source.unsplash.com/1600x600/?business,office');"></div>
    <div class="relative z-10 max-w-5xl mx-auto text-center py-28 px-6">
      <h1 class="text-4xl md:text-5xl font-extrabold">Hire the Right Talent Faster</h1>
      <p class="mt-4 text-lg md:text-xl text-blue-100">Post jobs, review profiles, and get matched with skilled workers instantly.</p>
      <a href="{{ url('/register?role=client') }}"
         class="inline-block mt-8 bg-white text-blue-700 px-6 py-3 rounded-full font-semibold shadow hover:bg-gray-100 transition">
         Get Started as Client
      </a>
    </div>
  </header>

  <!-- Why Clients Choose WorkBridge -->
  <section class="max-w-6xl mx-auto px-6 py-20 text-center">
    <h2 class="text-3xl font-bold text-blue-700">Why Choose WorkBridge?</h2>
    <div class="mt-10 grid md:grid-cols-3 gap-10">
      <div class="p-6 bg-white shadow-md rounded-lg">
        <i class="fas fa-search text-blue-600 text-4xl mb-4"></i>
        <h3 class="text-xl font-semibold">Find Skilled Workers</h3>
        <p class="mt-2 text-gray-600">Browse verified profiles and get smart recommendations based on job requirements.</p>
      </div>
      <div class="p-6 bg-white shadow-md rounded-lg">
        <i class="fas fa-clipboard-list text-green-600 text-4xl mb-4"></i>
        <h3 class="text-xl font-semibold">Post & Manage Jobs</h3>
        <p class="mt-2 text-gray-600">Easily create job postings, track applications, and manage your hiring process.</p>
      </div>
      <div class="p-6 bg-white shadow-md rounded-lg">
        <i class="fas fa-lock text-purple-600 text-4xl mb-4"></i>
        <h3 class="text-xl font-semibold">Secure Payments</h3>
        <p class="mt-2 text-gray-600">Pay only for completed work with secure and transparent transactions.</p>
      </div>
    </div>
  </section>

  <!-- Metrics -->
  <section class="py-20 bg-gray-100">
    <div class="max-w-6xl mx-auto text-center">
      <h2 class="text-3xl font-bold mb-12">Trusted by Businesses</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="p-6 bg-white shadow rounded-lg">
          <h3 class="text-4xl font-bold text-blue-600">500+</h3>
          <p class="mt-2 text-gray-600">Verified Workers</p>
        </div>
        <div class="p-6 bg-white shadow rounded-lg">
          <h3 class="text-4xl font-bold text-green-600">1200+</h3>
          <p class="mt-2 text-gray-600">Jobs Completed</p>
        </div>
        <div class="p-6 bg-white shadow rounded-lg">
          <h3 class="text-4xl font-bold text-purple-600">95%</h3>
          <p class="mt-2 text-gray-600">Client Satisfaction</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Steps for Clients -->
  <section class="max-w-6xl mx-auto px-6 py-20">
    <h2 class="text-2xl font-semibold text-blue-700 text-center">How to Get Started</h2>
    <div class="mt-10 grid md:grid-cols-4 gap-6">
      <div class="p-6 bg-white rounded-xl shadow-sm text-center">
        <i class="fas fa-user-plus text-blue-600 text-3xl mb-3"></i>
        <h4 class="font-bold">1. Sign Up</h4>
        <p class="mt-2 text-gray-600">Create a free client account in minutes.</p>
      </div>
      <div class="p-6 bg-white rounded-xl shadow-sm text-center">
        <i class="fas fa-briefcase text-green-600 text-3xl mb-3"></i>
        <h4 class="font-bold">2. Post a Job</h4>
        <p class="mt-2 text-gray-600">Describe your task and set your budget.</p>
      </div>
      <div class="p-6 bg-white rounded-xl shadow-sm text-center">
        <i class="fas fa-users text-purple-600 text-3xl mb-3"></i>
        <h4 class="font-bold">3. Review Applications</h4>
        <p class="mt-2 text-gray-600">Check worker profiles & select the best fit.</p>
      </div>
      <div class="p-6 bg-white rounded-xl shadow-sm text-center">
        <i class="fas fa-check-circle text-yellow-500 text-3xl mb-3"></i>
        <h4 class="font-bold">4. Hire & Pay</h4>
        <p class="mt-2 text-gray-600">Collaborate securely and pay upon completion.</p>
      </div>
    </div>
  </section>

  <!-- Final CTA -->
  <section class="bg-blue-700 text-white py-20 text-center">
    <div class="max-w-4xl mx-auto px-6">
      <h3 class="text-3xl font-semibold">Start Hiring with WorkBridge Today</h3>
      <a href="{{ url('/register?role=client') }}" class="inline-block mt-6 bg-white text-blue-700 px-6 py-3 rounded-full font-semibold shadow hover:bg-gray-100 transition">Sign Up as Client</a>
    </div>
  </section>

</body>
</html>
