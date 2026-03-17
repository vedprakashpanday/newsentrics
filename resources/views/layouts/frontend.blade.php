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

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
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

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2 text-2xl font-extrabold tracking-tight">
                <div class="w-8 h-8 bg-blue-600 rounded text-white flex items-center justify-center">N</div>
                News<span class="text-blue-600">entric</span>
            </a>
            
           <nav class="hidden md:flex gap-6 font-semibold text-slate-600">
                <a href="/" class="hover:text-blue-600 transition {{ request()->is('/') ? 'text-blue-600' : '' }}">Home</a>
                
                @foreach($categories as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="hover:text-blue-600 transition {{ request()->is('category/'.$cat->slug) ? 'text-blue-600' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </nav>

            <form action="{{ route('news.search') }}" method="GET" class="relative hidden sm:block">
                            <input type="hidden" name="country" id="search_country" value="">
                            
                            <input type="text" name="q" placeholder="Search news..." value="{{ request('q') }}" class="bg-slate-100 border border-slate-200 text-sm rounded-full pl-4 pr-10 py-2 focus:outline-none focus:border-blue-500 focus:bg-white transition w-48 lg:w-64" required>
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>
        </div>
    </header>

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 mt-auto py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-slate-500 text-sm">
            &copy; {{ date('Y') }} Newsentric. All rights reserved.
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Check agar user ne pehle se country save ki hui hai (Local Storage)
            let savedCountry = localStorage.getItem('user_country');
            
            if (savedCountry) {
                $('#globalCountrySelect').val(savedCountry);
            } else {
                // Agar first time visit hai, toh IP check karo (Free API use karke)
                $.get("https://ipapi.co/json/", function(response) {
                    let countryMapping = {
                        "IN": "India",
                        "US": "USA",
                        "GB": "UK",
                        "AU": "Australia",
                        "CA": "Canada"
                    };
                    
                    let userCountry = countryMapping[response.country_code];
                    
                    if(userCountry) {
                        $('#globalCountrySelect').val(userCountry);
                        localStorage.setItem('user_country', userCountry);
                        // Optional: Yahan hum page ko reload kar sakte hain nayi country ke saath
                        // window.location.reload();
                    }
                }).fail(function() {
                    console.log("Ad blocker ya network issue ki wajah se IP detect nahi ho paayi.");
                });
            }

            // Jab user dropdown change kare
            $('#globalCountrySelect').change(function() {
                localStorage.setItem('user_country', $(this).val());
                
                // Dropdown change hone par page reload hoga taaki nayi news aaye
                window.location.reload(); 
            });

                $('#search_country').val(localStorage.getItem('user_country') || 'India');
        });

    
    </script>

    @stack('scripts')
</body>
</html>