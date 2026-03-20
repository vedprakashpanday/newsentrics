<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('uploads/logo/newsentric.jpg') }}">
    <title>@yield('title', ($site_settings->site_name ?? 'Newsentric') . ' - Global AI News')</title>
    <meta name="description" content="@yield('meta_description', 'Get the latest trending news globally, powered by AI.')">
    <meta name="keywords" content="@yield('meta_keywords', 'news, latest news, global trends, AI news')">
    
    <meta property="og:title" content="@yield('title', 'Newsentric')">
    <meta property="og:image" content="@yield('meta_image', asset('default-news.png'))">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        @media (min-width: 768px) {
    .md\:h-16 {
        height: 6rem;
    }
}

 @media (max-width: 576px) {
   .h-logo {
        height: 2rem;
    }
}
 @media (min-width: 577px) {
   .h-logo {
        width: 12rem;
    height: 6rem;
    }
}
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
<body class="antialiased flex flex-col min-h-screen bg-slate-50">

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
    @if(isset($site_settings) && $site_settings->logo)
        <a href="/" class="hidden md:flex items-center">
            <img src="{{ asset('uploads/logo/' . $site_settings->logo) }}" alt="{{ $site_settings->site_name }}" class="h-logo object-contain">
        </a>

        <a href="/" class="md:hidden relative flex flex-col items-center">
            <div class="w-8 h-8 bg-blue-600 rounded text-white flex items-center justify-center text-xl font-black">N</div>
            <span class="text-[7px] font-black text-slate-800 uppercase tracking-widest absolute -bottom-1.5 bg-white px-1 shadow-sm border border-slate-100 rounded-sm">{{ $site_settings->site_name ?? 'Newsentric' }}</span>
        </a>
    @else
        <a href="/" class="md:hidden relative flex flex-col items-center">
            <div class="w-8 h-8 bg-blue-600 rounded text-white flex items-center justify-center text-xl font-black">N</div>
            <span class="text-[7px] font-black text-slate-800 uppercase tracking-widest absolute -bottom-1.5 bg-white px-1 shadow-sm border border-slate-100 rounded-sm">{{ $site_settings->site_name ?? 'Newsentric' }}</span>
        </a>
        <a href="/" class="hidden md:flex items-center gap-2 text-2xl font-extrabold tracking-tight">
            <div class="w-8 h-8 bg-blue-600 rounded text-white flex items-center justify-center">N</div>
            {{ $site_settings->site_name ?? 'Newsentric' }}
        </a>
    @endif
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

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 py-10 mb-10">
        @yield('content')
    </main>

   <footer class="bg-white border-t border-slate-200 mt-auto pt-15 pb-6">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
            
            <div class="flex flex-col items-start text-left">
              <div class="shrink-0 z-30 bg-white mt-5 mb-5 ps-3">
    @if(isset($site_settings) && $site_settings->logo)
        <a href="/" class="hidden md:flex items-center">
            <img src="{{ asset('uploads/logo/' . $site_settings->logo) }}" alt="{{ $site_settings->site_name }}" class="h-logo object-contain">
        </a>

        <a href="/" class="md:hidden relative flex flex-col items-center">
            <div class="w-8 h-8 bg-blue-600 rounded text-white flex items-center justify-center text-xl font-black">N</div>
            <span class="text-[7px] font-black text-slate-800 uppercase tracking-widest absolute -bottom-1.5 bg-white px-1 shadow-sm border border-slate-100 rounded-sm">{{ $site_settings->site_name ?? 'Newsentric' }}</span>
        </a>
    @else
        <a href="/" class="md:hidden relative flex flex-col items-center">
            <div class="w-8 h-8 bg-blue-600 rounded text-white flex items-center justify-center text-xl font-black">N</div>
            <span class="text-[7px] font-black text-slate-800 uppercase tracking-widest absolute -bottom-1.5 bg-white px-1 shadow-sm border border-slate-100 rounded-sm">{{ $site_settings->site_name ?? 'Newsentric' }}</span>
        </a>
        <a href="/" class="hidden md:inline-flex items-center gap-2 text-2xl mt-10 font-extrabold tracking-tight text-slate-900 hover:opacity-80 transition-opacity">
    <div class="w-10 h-10 bg-blue-600 rounded shadow-sm text-white flex items-center justify-center">N</div>
    {{ $site_settings->site_name ?? 'Newsentric' }}
</a>
    @endif
</div>

                @if(isset($site_settings) && $site_settings->footer_about)
                    <p class="text-slate-500 text-sm leading-relaxed pr-0 md:pr-6">
                        {{ $site_settings->footer_about }}
                    </p>
                @endif
            </div>

            <div class="flex flex-col items-start md:items-center">
                <div class="text-left mt-10">
                    <h4 class="text-slate-900 font-bold mb-5 uppercase tracking-wider text-sm">Quick Links</h4>
                   <ul class="space-y-3 text-sm text-slate-500 font-medium">
    <li><a href="/" class="hover:text-blue-600 transition">Home</a></li>
    <li><a href="{{ route('page.show', 'about-us') }}" class="hover:text-blue-600 transition">About Us</a></li>
    <li><a href="{{ route('page.show', 'privacy-policy') }}" class="hover:text-blue-600 transition">Privacy Policy</a></li>
    <li><a href="{{ route('page.show', 'terms-and-conditions') }}" class="hover:text-blue-600 transition">Terms & Conditions</a></li>
    <li><a href="{{ route('contact') }}" class="hover:text-blue-600 transition">Contact Us</a></li>
</ul>
                </div>
            </div>

            <div class="flex flex-col items-start md:items-end">
                <div class="text-left md:text-right mt-10">
                    <h4 class="text-slate-900 font-bold mb-5 uppercase tracking-wider text-sm">Follow Us</h4>
                    
                    @if(isset($site_settings) && ($site_settings->facebook || $site_settings->twitter || $site_settings->instagram))
                        <div class="flex items-center justify-start md:justify-end gap-4">
                            @if($site_settings->facebook)
                                <a href="{{ $site_settings->facebook }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white transition">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"/></svg>
                                </a>
                            @endif
                            
                            @if($site_settings->twitter)
                                <a href="{{ $site_settings->twitter }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-black hover:text-white transition">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                            @endif

                            @if($site_settings->instagram)
                                <a href="{{ $site_settings->instagram }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-pink-600 hover:text-white transition">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-slate-400">Social links coming soon.</p>
                    @endif
                </div>
            </div>

        </div>

        <div class="border-t border-slate-200 pt-6 text-center">
            <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider">
                &copy; {{ date('Y') }} {{ $site_settings->site_name ?? 'Newsentric' }}. All rights reserved.
            </div>
        </div>

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