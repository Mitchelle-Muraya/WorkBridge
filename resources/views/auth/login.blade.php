<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WorkBridge - Login</title>

  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow: hidden;
    }

    /* Background blur */
    .background {
      position: fixed;
      width: 100%;
      height: 100%;
      background: url('/images/image_1.png') center/cover no-repeat;
      filter: blur(6px);
      z-index: -1;
    }

    /* Main container */
    .form-container {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      background: rgba(0, 0, 0, 0.75);
      padding: 45px 40px;
      border-radius: 16px;
      box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.4);
      color: white;
      width: 360px;
    }

    .form-container h1 {
      margin-bottom: 5px;
      font-size: 1.8rem;
      font-weight: bold;
      color: #00aaff;
    }

    .form-container h2 {
      margin-bottom: 25px;
      font-size: 1.4rem;
      font-weight: 500;
    }

    .form-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
    }

    .form-container button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: #007bff;
      color: white;
      font-size: 1.1rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 10px;
    }

    .form-container button:hover {
      background: #0056b3;
    }

    .form-container a {
      display: block;
      margin-top: 12px;
      font-size: 0.9rem;
      color: #00aaff;
      text-decoration: none;
      transition: color 0.3s;
    }

    .form-container a:hover {
      color: #0099dd;
    }

    /* ✅ Google Button (Outlined Style) */
    .google-outline-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      margin-top: 20px;
      padding: 12px;
      border-radius: 8px;
      border: 2px solid #007bff;
      background: transparent;
      color: #007bff;
      font-size: 1rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .google-outline-btn:hover {
      background: rgba(0, 123, 255, 0.1);
      transform: translateY(-1px);
    }

    .google-outline-btn img {
      width: 20px;
      height: 20px;
    }

    /* Back button */
    .back-button {
      position: fixed;
      top: 20px;
      left: 20px;
      background: rgba(0, 123, 255, 0.85);
      color: white;
      border: none;
      padding: 6px 14px;
      font-size: 14px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .back-button:hover {
      background: rgba(0, 86, 179, 0.9);
    }
  </style>
</head>
<body>

  <button onclick="goBack()" class="back-button">← Back</button>

  <div class="background"></div>

  <div class="form-container">
    <h1>WorkBridge</h1>
    <h2>Login</h2>

    <!-- Laravel Login Form -->
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <a href="{{ route('register') }}">Don’t have an account? <strong>Sign up</strong></a>

    <!-- Google Sign-In -->
    <a href="{{ route('google.login') }}" class="google-outline-btn">
      <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Logo">
            Continue with Google
    </a>
  </div>

  <script>
    function goBack() {
      window.history.back();
    }
  </script>
</body>
</html>
