<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between">
            <a href="/" class="font-bold text-lg">{{ config('app.name') }}</a>
            <div>
                @auth
                    <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:underline">Profil</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="ml-4 text-gray-700 hover:underline">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:underline">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="py-6">
        @yield('content')
    </main>
</body>

</html>