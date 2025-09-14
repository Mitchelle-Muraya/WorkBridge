<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workbridge- Login</title>
    <style>
        /* General Styles */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        /* Background Image */
        .background {
            position: fixed;
            width: 100%;
            height: 100%;
           background: url('/images/image_1.png') center/cover no-repeat;

            filter: blur(5px);
            z-index: -1;
        }

        /* Form Container */
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

        /* Back Button */
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
            width: auto;
            height: auto;
            min-width: 70px;
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
        <h2>Login</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="{{ route('register') }}">Don't have an account? Sign up</a>
        <a href="{{ url('auth/google') }}" class="btn btn-danger">Login with Google</a>
    </div>
</body>
<script>
    function goBack() {
        window.history.back();
    }
</script>
</html>
