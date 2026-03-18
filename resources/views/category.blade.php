@extends('layouts.frontend')

@section('content')
<div class="py-6">
    <div class="border-b-4 border-blue-600 mb-8 pb-2 flex items-baseline gap-4">
        <h1 class="text-4xl font-black uppercase tracking-tighter text-slate-900">{{ $category->name }}</h1>
    </div>

    <div class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase">In {{ $userCountry }}</span>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        <div id="local-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($localNews as $news)
                @include('components.news-card', ['news' => $news])
            @endforeach
        </div>

        @if($localNews->count() >= 8)
        <div class="text-center mt-10">
            <button class="load-more-btn border-2 border-blue-600 text-blue-600 font-bold py-2 px-8 uppercase text-xs hover:bg-blue-600 hover:text-white transition" 
                    data-type="local" data-skip="8">
                Discover More in {{ $userCountry }}
            </button>
        </div>
        @endif
    </div>

    <div class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-slate-800 text-white text-xs font-bold px-3 py-1 rounded-full uppercase">Around the World</span>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        <div id="world-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($worldNews as $news)
                @include('components.news-card', ['news' => $news])
            @endforeach
        </div>

        @if($worldNews->count() >= 4)
        <div class="text-center mt-10">
            <button class="load-more-btn border-2 border-slate-800 text-slate-800 font-bold py-2 px-8 uppercase text-xs hover:bg-slate-800 hover:text-white transition" 
                    data-type="world" data-skip="4">
                Discover More World News
            </button>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.load-more-btn', function() {
        let btn = $(this);
        let type = btn.data('type');
        let skip = btn.data('skip');
        let grid = (type === 'local') ? $('#local-grid') : $('#world-grid');

        btn.html('<span class="animate-spin inline-block mr-2">⏳</span> Loading...').prop('disabled', true);

        $.ajax({
            url: window.location.href,
            type: 'GET',
            data: { type: type, skip: skip },
            success: function(response) {
                if(response.html) {
                    grid.append(response.html);
                    btn.data('skip', skip + 4); // Agli baar ke liye skip badha do
                    btn.html('Discover More ' + (type === 'local' ? 'in {{ $userCountry }}' : 'World News')).prop('disabled', false);
                }
                
                // Agar 4 se kam news aayi hain, matlab khatam ho gayi
                if(response.count < 4) {
                    btn.parent().html('<p class="text-slate-400 text-sm italic">No more stories to show.</p>');
                }
            },
            error: function() {
                alert('Connection error!');
                btn.html('Try Again').prop('disabled', false);
            }
        });
    });
</script>
@endpush