<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkBridge - Login</title>
    <style>
        /* General Styles */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        /* Background Image */
        .background {
            position: fixed;
            width: 100%;
            height: 100%;
            background: url('/images/image_1.png') center/cover no-repeat;
            filter: blur(6px);
            z-index: -1;
        }

        /* Form Container */
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

        /* Google Button */
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 18px;
            padding: 12px;
            border-radius: 25px;
            background: white;
            color: #444;
            font-size: 1rem;
            font-weight: 500;
            border: 1px solid #ddd;
            text-decoration: none;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .google-btn img {
            height: 20px;
            margin-right: 10px;
        }

        .google-btn:hover {
            background: #f7f7f7;
        }

        /* Back Button */
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
        /* Google Button */
.google-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    margin-top: 15px;
    padding: 12px;
    border-radius: 8px;
    background: transparent;
    color: #007bff;
    font-size: 1rem;
    font-weight: bold;
    border: 2px solid #007bff;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.google-btn:hover {
    background: rgba(0, 123, 255, 0.1); /* faint blue hover */
}

/* Bigger Google "G" */
.google-icon {
    font-size: 1.6rem;
    font-weight: bold;
}

    </style>
</head>
<body>
    <button onclick="goBack()" class="back-button">← Back</button>

    <div class="background"></div>
    <div class="form-container">
        <h1>WorkBridge</h1>
        <h2>Login</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="{{ route('register') }}">Don’t have an account? <strong>Sign up</strong></a>

        <!-- Google Login -->
        <a href="{{ url('auth/google') }}" class="google-btn">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Logo">
            Continue with Google
        </a>
    </div>
</body>
<script>
    function goBack() {
        window.history.back();
    }
</script>
</html>
