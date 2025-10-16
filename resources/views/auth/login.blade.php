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

        .background {
            position: fixed;
            width: 100%;
            height: 100%;
            background: url('/images/image_1.png') center/cover no-repeat;
            filter: blur(6px);
            z-index: -1;
        }

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

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            margin-top: 18px;
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
            background: rgba(0, 123, 255, 0.1);
        }

        .google-icon {
            font-size: 1.6rem;
            font-weight: bold;
        }

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

        <!-- Laravel Default Login -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <a href="{{ route('register') }}">Don’t have an account? <strong>Sign up</strong></a>

        <!-- Google Login Button -->
        <button type="button" onclick="googleLogin()" class="google-btn">
            <span class="google-icon">G</span>
            Sign in with Google
        </button>
    </div>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.13.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.13.0/firebase-auth.js"></script>

    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyDWMPQnoJ0NtdkZAEAXWX6lQGIM-htT5ys",
            authDomain: "workbridge-4bc7d.firebaseapp.com",
            projectId: "workbridge-4bc7d",
            storageBucket: "workbridge-4bc7d.firebasestorage.app",
            messagingSenderId: "183529435827",
            appId: "1:183529435827:web:13b37dddda662db5dfb6c6",
            measurementId: "G-VPWSKRY137"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        // Handle Google Sign-In
        function googleLogin() {
            const provider = new firebase.auth.GoogleAuthProvider();

            firebase.auth().signInWithPopup(provider)
                .then(result => result.user.getIdToken())
                .then(token => {
                    // Send token to Laravel backend
                    fetch('/firebase/verify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ token })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.user) {
                            window.location.href = '/dashboard';
                        } else {
                            alert('Login failed. Please try again.');
                        }
                    })
                    .catch(err => alert('Server error: ' + err.message));
                })
                .catch(error => {
                    console.error(error);
                    alert('Google Sign-In failed: ' + error.message);
                });
        }

        // Back button
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
