{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>👑 Admin Dashboard</h1>
    <p>Halo, {{ auth()->user()->name }} (role: ADMIN)</p>
    <a href="{{ route('logout') }}"
       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
       Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">
        @csrf
    </form>
</body>
</html>
