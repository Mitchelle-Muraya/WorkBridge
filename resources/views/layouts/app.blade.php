<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HireHub</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-white shadow p-4 flex justify-between">
        <a href="{{ route('landing') }}" class="font-bold text-xl">HireHub</a>
        <div>
            @auth('worker')
                <span class="mr-3">Hi, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-600">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mr-3">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-3 py-1 rounded">Sign Up</a>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

</body>
</html>
