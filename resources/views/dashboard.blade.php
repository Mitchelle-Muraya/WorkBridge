<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkBridge Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7f7;
            text-align: center;
            margin-top: 80px;
        }
        h1 {
            color: #007bff;
        }
        p {
            color: #444;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
        }
        .logout-btn:hover {
            background: #b02a37;
        }
    </style>
</head>
<body>
    <h1>Welcome to WorkBridge</h1>
    <p>You are logged in via Firebase Google Sign-In 🎉</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="logout-btn" type="submit">Logout</button>
    </form>
</body>
</html>
