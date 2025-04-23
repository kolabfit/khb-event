{{-- resources/views/user/dashboard.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h1>👤 User Dashboard</h1>
    <p>Halo, {{ auth()->user()->name }} (role: USER)</p>
    <a href="{{ route('logout') }}"
       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
       Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">
        @csrf
    </form>
</body>
</html>
