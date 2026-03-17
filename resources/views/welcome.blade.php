<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Newsentric - AI Powered Global News</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg { background: radial-gradient(circle at top, #f3f6fc 0%, #ffffff 100%); }
    </style>
</head>
<body class="antialiased hero-bg min-h-screen flex flex-col text-gray-900">

    <header class="w-full border-b border-gray-100 bg-white/80 backdrop-blur-md fixed top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg">N</div>
                    <span class="text-2xl font-extrabold tracking-tight">News<span class="text-blue-600">entric</span></span>
                </div>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-3 sm:gap-5">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-blue-600 font-semibold transition duration-200">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-semibold transition duration-200">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold shadow-md shadow-blue-500/30 transition-all hover:-translate-y-0.5">
                                    Create Account
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 pt-20">
        <div class="text-center max-w-4xl mx-auto py-16 sm:py-24">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-sm font-semibold uppercase tracking-wide mb-6">
                <span class="flex h-2 w-2 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Powered by Generative AI
            </div>

            <h1 class="text-5xl sm:text-7xl font-extrabold tracking-tight leading-tight mb-6">
                The Future of <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Global Journalism</span>
            </h1>
            
            <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed mb-10">
                Discover trending topics instantly, generate professional news articles with AI, and publish directly to the world in seconds.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:bg-blue-700 transition-transform hover:-translate-y-1">
                        Go to Admin Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-transform hover:-translate-y-1">
                        Start Publishing
                    </a>
                    <a href="{{ route('login') }}" class="bg-white text-gray-900 border border-gray-200 px-8 py-4 rounded-xl font-bold text-lg shadow-sm hover:bg-gray-50 transition-transform hover:-translate-y-1">
                        Editor Login
                    </a>
                @endauth
            </div>
            
        </div>
    </main>

    <footer class="bg-white py-8 border-t border-gray-100">
        <div class="text-center text-gray-500 text-sm font-medium">
            &copy; {{ date('Y') }} Newsentric Portal. All rights reserved.
        </div>
    </footer>

</body>
</html>