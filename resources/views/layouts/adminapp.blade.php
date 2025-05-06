<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow">
        <!-- <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between">
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
        </div> -->
        @include('layouts.navigation')
    </nav>

    <main class="py-6">
        @yield('content')
    </main>
</body>

</html>