<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="@yield('meta_keywords')">
    <title>Newsentric Admin - @yield('title')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; min-height: 100vh; flex-direction: column; }
        .wrapper { display: flex; flex: 1; }
        #sidebar { width: 250px; background: #343a40; color: white; min-height: 100vh; }
        #content { flex: 1; padding: 20px; background: #f8f9fa; }
        footer { background: #212529; color: white; padding: 10px; text-align: center; }
        .nav-link { color: white; }
        .nav-link:hover { background: #495057; }
    </style>
    @yield('styles') 
</head>
<body>

    <nav class="navbar navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="#">Newsentric Admin</a>
        <div class="text-white">Welcome, {{ Auth::user()->name }}</div>
    </nav>

    <div class="wrapper">
        <nav id="sidebar" class="p-3">
            <ul class="nav flex-column">
                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a></li>
                <li class="nav-item"><a href="{{ route('news.create') }}" class="nav-link">Post News</a></li>
                <li class="nav-item"><a href="{{ route('admin.trending') }}" class="nav-link">Trending News</a></li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-link nav-link">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>

        <div id="content">
            @yield('content')
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Newsentric - Admin Portal</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   @stack('scripts')
</body>
</html>