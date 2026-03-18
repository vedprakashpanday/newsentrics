<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Newsentric - Global AI News')</title>
    <meta name="description" content="@yield('meta_description', 'Get the latest trending news globally, powered by AI.')">
    <meta name="keywords" content="@yield('meta_keywords', 'news, latest news, global trends, AI news')">
    
    <meta property="og:title" content="@yield('title', 'Newsentric')">
    <meta property="og:image" content="@yield('meta_image', asset('default-news.png'))">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        /* Breaking News Ticker Animation */
        .ticker-wrap { overflow: hidden; white-space: nowrap; width: 100%; }
        .ticker-move { display: inline-block; animation: ticker 25s linear infinite; padding-left: 100%; }
        .ticker-move:hover { animation-play-state: paused; cursor: pointer; } 

        /* Scrollable Nav ke liye hide scrollbar */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes ticker {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }
    </style>
</head>
<body class="antialiased flex flex-col min-h-screen">

    <div class="bg-slate-900 text-white text-xs py-2">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <div class="hidden sm:block text-slate-400">
                <span class="animate-pulse inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span> 
                Live Updates Active
            </div>
            
            <div class="flex items-center gap-2 ml-auto">
                <label for="globalCountrySelect" class="text-slate-300 font-semibold">Your Edition:</label>
                <select id="globalCountrySelect" class="bg-slate-800 border border-slate-700 text-white rounded px-2 py-1 outline-none focus:border-blue-500 transition">
                    <option value="India">🇮🇳 India</option>
                    <option value="USA">🇺🇸 USA</option>
                    <option value="UK">🇬🇧 United Kingdom</option>
                    <option value="Australia">🇦🇺 Australia</option>
                    <option value="Canada">🇨🇦 Canada</option>
                </select>
            </div>
        </div>
    </div>

 <header class="bg-white shadow-sm sticky top-0 z-50 h-14 md:h-16 flex items-center">
        <div class="max-w-7xl w-full mx-auto px-4 flex items-center justify-between relative">
            
            <div class="shrink-0 z-30 bg-white">
                <a href="/" class="md:hidden relative flex flex-col items-center">
                    <div class="w-8 h-8 bg-blue-600 rounded text-white flex items-center justify-center text-xl font-black">N</div>
                    <span class="text-[7px] font-black text-slate-800 uppercase tracking-widest absolute -bottom-1.5 bg-white px-1 shadow-sm border border-slate-100 rounded-sm">Newsentric</span>
                </a>
                <a href="/" class="hidden md:flex items-center gap-2 text-2xl font-extrabold tracking-tight">
                    <div class="w-8 h-8 bg-blue-600 rounded text-white flex items-center justify-center">N</div>
                    News<span class="text-blue-600">entric</span>
                </a>
            </div>

            <nav id="mobile-nav" class="flex-1 flex overflow-x-auto gap-5 font-semibold text-slate-600 whitespace-nowrap scrollbar-hide items-center text-sm md:text-base py-2 pl-4 pr-4 md:pr-10 transition-opacity duration-300">
                <a href="/" class="hover:text-blue-600 transition shrink-0 {{ request()->is('/') ? 'text-blue-600' : '' }}">Home</a>
                @foreach($categories as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="hover:text-blue-600 transition shrink-0 {{ request()->is('category/'.$cat->slug) ? 'text-blue-600' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </nav>

            <form action="{{ route('news.search') }}" method="GET" class="relative hidden md:block shrink-0">
                <input type="hidden" name="country" class="search_country" value="">
                <input type="text" name="q" placeholder="Search news..." value="{{ request('q') }}" class="w-56 lg:w-72 bg-slate-100 border border-slate-200 text-sm rounded-full pl-4 pr-9 py-1.5 focus:outline-none focus:border-blue-500 focus:bg-white transition" required>
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>

            <button id="open-search-btn" type="button" class="md:hidden shrink-0 text-slate-600 hover:text-blue-600 p-1 z-10 transition-opacity duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>

            <form action="{{ route('news.search') }}" method="GET" id="mobile-search-form" class="absolute right-0 h-full z-20 bg-white flex items-center justify-end transition-all duration-500 ease-in-out w-0 opacity-0 overflow-hidden md:hidden">
                <input type="hidden" name="country" class="search_country" value="">
                <div class="flex items-center justify-end gap-2 w-full min-w-[240px] pr-2">
                    <button type="button" id="close-search-btn" class="shrink-0 text-slate-400 hover:text-red-500 p-1 bg-slate-100 rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <div class="relative w-full">
                        <input id="mobile-search-input" type="text" name="q" placeholder="Search..." value="{{ request('q') }}" class="w-full bg-slate-100 border border-slate-200 text-sm rounded-full pl-4 pr-9 py-1.5 focus:outline-none focus:border-blue-500" required>
                        <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </header>

    @php
        $tickerNews = \App\Models\News::orderBy('created_at', 'desc')->take(5)->get();
    @endphp

    @if($tickerNews->count() > 0)
    <div class="bg-red-600 text-white text-[13px] py-1.5 shadow-md relative z-40 border-b border-red-700">
        <div class="max-w-7xl mx-auto px-4 flex items-center">
            <div class="font-black uppercase tracking-widest pr-4 border-r border-red-400 bg-red-600 z-10 flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>
                Trending
            </div>
            <div class="ticker-wrap ml-3">
                <div class="ticker-move font-medium whitespace-nowrap">
                    @foreach($tickerNews as $news)
                        <a href="{{ route('news.show', $news->slug) }}" class="hover:text-red-200 transition">
                            {{ $news->title }}
                        </a>
                        <span class="mx-6 text-red-300 font-light">|</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 mt-auto py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-slate-500 text-sm">
            &copy; {{ date('Y') }} Newsentric. All rights reserved.
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>
    <script>
        $(document).ready(function() {
            // 1. Country Selection Logic
            let savedCountry = localStorage.getItem('user_country');
            
            if (savedCountry) {
                $('#globalCountrySelect').val(savedCountry);
            } else {
                $.get("https://ipapi.co/json/", function(response) {
                    let countryMapping = { "IN": "India", "US": "USA", "GB": "UK", "AU": "Australia", "CA": "Canada" };
                    let userCountry = countryMapping[response.country_code];
                    
                    if(userCountry) {
                        $('#globalCountrySelect').val(userCountry);
                        localStorage.setItem('user_country', userCountry);
                    }
                }).fail(function() {
                    console.log("IP detect nahi ho paayi.");
                });
            }

            $('#globalCountrySelect').change(function() {
                localStorage.setItem('user_country', $(this).val());
                window.location.reload(); 
            });

            $('#search_country').val(localStorage.getItem('user_country') || 'India');

           // 2. Premium Search Animation Logic (Fixed for Mobile & Return to Home)
            $('#open-search-btn').click(function() {
                $(this).addClass('opacity-0 pointer-events-none');
                $('#mobile-nav').addClass('opacity-0 pointer-events-none');
                $('#mobile-search-form').removeClass('w-0 opacity-0').addClass('w-[calc(100%-3rem)] opacity-100');
                setTimeout(() => $('#mobile-search-input').focus(), 300);
            });

            $('#close-search-btn').click(function() {
                $('#mobile-search-form').removeClass('w-[calc(100%-3rem)] opacity-100').addClass('w-0 opacity-0');
                $('#mobile-nav').removeClass('opacity-0 pointer-events-none');
                $('#open-search-btn').removeClass('opacity-0 pointer-events-none');

                // AUTO-RETURN TO HOME (Mobile Par Cross Dabane Par)
                let urlParams = new URLSearchParams(window.location.search);
                if(urlParams.has('q')) {
                    window.location.href = "/";
                }
            });

            // AUTO-RETURN TO HOME (Desktop Desktop par agar Search khali karke bahar click kiya jaye)
            $('input[name="q"]').on('blur', function() {
                let urlParams = new URLSearchParams(window.location.search);
                if(urlParams.has('q') && $(this).val().trim() === '') {
                    window.location.href = "/";
                }
            });

        }); 
    </script>
    @stack('scripts')
</body>
</html>