@extends('layouts.frontend')

@section('title', isset($query) ? 'Search Results for ' . $query : 'Newsentric - Live ' . $country . ' News & AI Insights')
@section('meta_description', 'Read the latest trending news and AI-generated insights for ' . $country . ' on Newsentric.')
@section('meta_keywords', 'latest news, ' . $country . ' news, AI news, trending')

@section('content')

@if(empty($query))

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 border-b-2 border-black pb-8 mb-10 mt-4">
    
    <div class="lg:col-span-1 flex flex-col gap-4 lg:border-r border-slate-200 lg:pr-6">
        <div class="flex items-center gap-2 border-b-2 border-red-600 pb-1 mb-2">
            <div class="w-2 h-2 bg-red-600 rounded-full animate-pulse"></div>
            <h3 class="font-black uppercase text-sm tracking-widest text-slate-900">Latest Updates</h3>
        </div>
        
        @foreach($latestNews as $lNews)
            <article class="relative border-b border-slate-100 pb-4 last:border-0 group">
                <span class="relative z-10 text-[10px] font-bold text-blue-600 uppercase tracking-wider block mb-1">
                    {{ $lNews->category->name ?? 'News Update' }}
                </span>
                <h4 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-blue-600 transition">
                    <a href="{{ route('news.show', $lNews->slug) }}" class="before:absolute before:inset-0 z-0">
                        {{ $lNews->title }}
                    </a>
                </h4>
            </article>
        @endforeach
    </div>

    <div class="lg:col-span-2 lg:px-4 flex flex-col">
        @if($heroNews)
            <div class="flex items-center gap-3 mb-3">
                <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 uppercase tracking-wider">Breaking News</span>
                <span class="text-xs font-bold text-slate-500 uppercase">{{ $heroNews->category->name ?? 'Top Story' }}</span>
            </div>
            
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-4 hover:text-blue-600 transition">
                <a href="{{ route('news.show', $heroNews->slug) }}">
                    {{ $heroNews->title }}
                    <span class="badge bg-secondary" style="font-size: 0.3em; vertical-align: middle;">
       <br>(Source: {{ $heroNews->source }} )
    </span>
                </a>
                
            </h1>
            
            <p class="text-slate-600 mb-6 text-base sm:text-lg leading-relaxed line-clamp-3">
                {{ Str::limit(strip_tags($heroNews->content), 200) }}
            </p>
            
           <div class="w-full aspect-[16/9] bg-slate-100 overflow-hidden mt-auto">
                <a href="{{ route('news.show', $heroNews->slug) }}" class="block w-full h-full group">
                    @if($heroNews->image)
                        <img src="{{ asset('uploads/news/' . $heroNews->image) }}" alt="{{ $heroNews->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-50 to-slate-200 text-slate-400 group-hover:bg-slate-200 transition duration-500">
                            <svg class="w-16 h-16 mb-3 text-slate-300 group-hover:text-blue-400 group-hover:scale-110 transition duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-bold uppercase tracking-widest text-slate-400 group-hover:text-blue-500 transition duration-500">View Article</span>
                        </div>
                    @endif
                </a>
            </div>
        @endif
    </div>

 <div class="lg:col-span-1 lg:border-l border-slate-200 lg:pl-6 pt-6 lg:pt-0">
        <div class="sticky top-24">
            
            <div class="bg-slate-900 text-white p-5 mb-5 border-b-4 border-blue-500 shadow-md">
                <div id="live-time" class="text-3xl font-black tracking-tighter mb-1 text-center font-mono">00:00:00</div>
                <div id="live-date" class="text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Loading Date...</div>
            </div>

          <div id="ai-sidebar-wrapper" class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-slate-100">
    <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
        <i class="bi bi-robot text-blue-600"></i> Today In History
    </h3>
    
    <div id="ai-sidebar-loading" class="py-10 text-center">
        <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
        <p class="text-xs text-slate-400 mt-2 italic">Gemini is analyzing news...</p>
    </div>

    
    <div id="ai-sidebar-content"></div>
</div>

           <div class="bg-slate-50 border border-slate-200 h-[250px] flex flex-col justify-center items-center text-slate-400 p-4 shadow-inner mb-6">
    @if(isset($all_ads['sidebar_square']))
        {!! $all_ads['sidebar_square'] !!}
    @else
        <span class="text-[10px] uppercase font-bold tracking-widest mb-2">Advertisement</span>
        <span class="text-center text-sm font-semibold">Square Ad<br>(300x250)</span>
    @endif
</div>
            
        </div>
    </div>
    
</div>

<div class="w-full mb-10">
    <div class="bg-slate-50 border border-slate-200 py-6 flex flex-col items-center justify-center text-slate-400 hidden sm:flex">
        @if(isset($all_ads['header_banner']))
            {!! $all_ads['header_banner'] !!}
        @else
            <span class="text-[10px] font-bold uppercase tracking-widest mb-1">Advertisement</span>
            <span class="text-sm font-semibold">Leaderboard Banner (728x90)</span>
        @endif
    </div>
</div>

@endif
<div class="mb-6 border-b-2 border-black pb-2">
    <h2 class="text-xl font-black text-slate-900 uppercase tracking-wide">
        @if(isset($query) && !empty($query))
            Search Results for: <span class="text-blue-600">"{{ $query }}"</span>
        @else
            More {{ $country }} Headlines
        @endif
    </h2>
</div>

<div id="news-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    @if($newsList->isEmpty() && isset($query) && !empty($query))
        <div class="col-span-full py-12 text-center text-slate-500 font-medium">
            No news found for "{{ $query }}". Try different keywords!
        </div>
    @else
        @include('components.news-list', ['newsList' => $newsList])
    @endif
</div>

<div class="w-full mt-10 mb-8">
    <div class="bg-slate-50 border border-slate-200 py-8 flex flex-col items-center justify-center text-slate-400">
        @if(isset($all_ads['sponsored_feed']))
            {!! $all_ads['sponsored_feed'] !!}
        @else
            <span class="text-[10px] font-bold uppercase tracking-widest mb-1">Advertisement</span>
            <span class="text-lg font-bold text-slate-300">Sponsored Feed Ad</span>
        @endif
    </div>
</div>

@if($newsList->hasMorePages())
    <div class="text-center mt-8 mb-8">
        <button id="load-more-btn" data-page="2" class="bg-slate-900 hover:bg-blue-600 text-white text-sm font-bold py-3 px-10 uppercase tracking-widest transition-all">
            Load More News
        </button>
    </div>
@else
    <div class="text-center mt-12 mb-8 text-slate-500 font-medium">
        You've reached the end of the latest updates!
    </div>
@endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // 1. IP Detection Update (URL parameters ke saath)
        $('#globalCountrySelect').change(function() {
            let selectedCountry = $(this).val();
            localStorage.setItem('user_country', selectedCountry);
            window.location.href = "?country=" + selectedCountry;
        });

        // 2. Load More Chunking Logic
        $('#load-more-btn').click(function() {
            let button = $(this);
            let page = button.attr('data-page');
            let country = "{{ $country }}"; 
            
            button.html('<span class="spinner-border spinner-border-sm"></span> Loading...');

            $.ajax({
                url: "?country=" + country + "&page=" + page,
                type: "GET",
                success: function(response) {
                    $('#news-container').append(response.html);
                    button.attr('data-page', parseInt(page) + 1);
                    button.html('Load More Stories');

                    if(!response.hasMore) {
                        button.fadeOut();
                        $('#news-container').after('<div class="text-center mt-8 text-slate-500 font-medium">You\'ve reached the end!</div>');
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                    button.html('Load More Stories');
                }
            });
        });

        // 3. LIVE DIGITAL CLOCK LOGIC
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            let ampm = hours >= 12 ? 'PM' : 'AM';
            
            hours = hours % 12;
            hours = hours ? hours : 12; 
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            
            let timeString = hours + ':' + minutes + ':' + seconds + ' <span class="text-sm text-blue-400">' + ampm + '</span>';
            $('#live-time').html(timeString);
            
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            $('#live-date').text(now.toLocaleDateString('en-US', options));
        }
        
        setInterval(updateClock, 1000);
        updateClock(); 

        // 4. BACKGROUND AI WIDGET FETCH
        let userCountry = "{{ $country }}"; 
        
          setTimeout(function() {
        $.ajax({
            url: "{{ route('api.sidebar-ai') }}", 
            type: "GET",
            data: { 
                country: "{{ $country ?? 'India' }}",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(html) {
                $('#ai-sidebar-loading').fadeOut(300, function() {
                    $('#ai-sidebar-content').html(html).fadeIn(500);
                });
            },
            error: function() {
                $('#ai-sidebar-wrapper').hide(); // Agar error aaye toh dabba gayab
            }
        });
    }, 800);
        
    });

    
</script>
@endpush