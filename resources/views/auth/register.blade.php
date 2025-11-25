<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkBridge - Register</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .background {
            position: fixed;
            width: 100%;
            height: 100%;
            background: url('/images/image_2.png') center/cover no-repeat;
            filter: blur(5px);
            z-index: -1;
        }

        .form-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            color: white;
            width: 350px;
        }

        .form-container h2 {
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-container button:hover {
            background: #0056b3;
        }

        .form-container a {
            display: block;
            margin-top: 10px;
            color: #00aaff;
            text-decoration: none;
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

        .google-btn img {
            height: 20px;
        }

        .google-btn:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button onclick="goBack()" class="back-button">← Back</button>

    <div class="background"></div>
    <div class="form-container">
        <h2>Create Your Account</h2>

        <form method="POST" action="{{ url('/register') }}">
    @csrf
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
@error('password')
   <small style="color: red;">{{ $message }}</small>
@enderror


    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

    <button type="submit">Sign Up</button>
</form>


        <a href="{{ route('login') }}">Already have an account? Login</a>

        <!-- Google Sign Up -->
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
