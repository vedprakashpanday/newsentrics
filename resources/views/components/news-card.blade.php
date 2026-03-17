@props(['news'])

<div class="relative bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition duration-300 group flex flex-col h-full">
    <div class="aspect-video w-full overflow-hidden bg-slate-100 relative">
        @if($news->image)
            <img src="{{ asset('uploads/news/' . $news->image) }}" alt="{{ $news->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-400">No Image</div>
        @endif
        
        <span class="absolute top-3 left-3 bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded shadow">
            {{ $news->country }}
        </span>
    </div>

    <div class="p-5 flex flex-col flex-grow">
        <p class="text-xs text-slate-400 font-medium mb-2">{{ $news->created_at->diffForHumans() }}</p>
        
        <h3 class="text-lg font-bold text-slate-800 mb-3 line-clamp-2 leading-snug group-hover:text-blue-600 transition">
            <a href="{{ url('news/' . $news->slug) }}" class="focus:outline-none">
                <span class="absolute inset-0" aria-hidden="true"></span>
                {{ $news->title }}
            </a>
        </h3>
        
        <p class="text-slate-600 text-sm line-clamp-3 mb-4 flex-grow">
            {{ Str::limit(strip_tags($news->content), 120) }}
        </p>
    </div>
</div>