<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkBridge - Welcome</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        /* Slideshow background */
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

        /* Dark overlay */
        .overlay {
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        /* Navbar (top-right links) */
        .navbar {
            position: absolute;
            top: 20px;
            right: 30px;
            z-index: 2;
        }

        .navbar a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-size: 1rem;
        }

        /* Center content */
        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 1;
        }

        .content h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .content a {
    display: inline-block;
    margin: 10px;
    padding: 14px 28px;
    border-radius: 30px; /* More rounded */
    background: linear-gradient(135deg, #007bff, #0056b3); /* Gradient blue */
    color: white;
    text-decoration: none;
    font-size: 1.2rem;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4); /* Subtle glow */
    transition: all 0.3s ease-in-out;
}

.content a:hover {
    background: linear-gradient(135deg, #0056b3, #00408a);
    transform: scale(1.05); /* Slight grow on hover */
    box-shadow: 0 6px 20px rgba(0, 86, 179, 0.6);
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
    </div>

    <!-- Main content -->
    <div class="content">
        <h1>Welcome to WorkBridge</h1>
        <p>Connecting skilled workers with clients through intelligent job matching.</p>
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Sign Up</a>
    </div>
</body>
</html>
