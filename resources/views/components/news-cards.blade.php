<article class="bg-white border border-slate-200 rounded-lg overflow-hidden group hover:shadow-xl transition-all duration-300">
    <a href="{{ route('news.show', $news->slug) }}" class="block relative aspect-video overflow-hidden">
        @if($news->image)
            <img src="{{ asset('uploads/news/' . $news->image) }}" alt="{{ $news->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
        @else
            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300 font-bold">Newsentric</div>
        @endif
        <span class="absolute top-2 left-2 bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">{{ $news->country }}</span>
    </a>
    
    <div class="p-4">
        <h3 class="font-bold text-slate-900 leading-snug mb-2 group-hover:text-blue-600 transition">
            <a href="{{ route('news.show', $news->slug) }}">{{ Str::limit($news->title, 65) }}</a>
        </h3>
        <p class="text-slate-500 text-xs leading-relaxed line-clamp-2">
            {{ Str::limit(strip_tags($news->content), 100) }}
        </p>
        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center">
            <span class="text-[10px] text-slate-400 font-bold uppercase">{{ $news->created_at->diffForHumans() }}</span>
            <i class="bi bi-arrow-right text-blue-600"></i>
        </div>
    </div>
</article>