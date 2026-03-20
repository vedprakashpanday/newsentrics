@extends('layouts.frontend')

@section('title', $news->title . ' - Newsentric')
@section('meta_description', Str::limit(strip_tags($news->content), 150))
@section('meta_keywords', $news->keywords ?? 'latest news, trending, global updates')
@section('meta_image', $news->image ? asset('uploads/news/' . $news->image) : asset('default.jpg'))
@section('og_title', $news->title)

@section('content')
<div class="max-w-7xl mx-auto py-6">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <div class="lg:col-span-2">
            
            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium mb-4">
                <a href="/" class="hover:text-blue-600 transition">Home</a>
                <span>/</span>
                <span class="text-slate-800">{{ $news->country }}</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-6 tracking-tight">
                {{ $news->title }}
            </h1>

            <div class="flex flex-wrap items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow">N</div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Newsentric AI Desk</p>
                        <p class="text-xs text-slate-500">{{ $news->created_at->format('M d, Y • h:i A') }} • <span class="text-blue-600 font-semibold">{{ $news->view_count }} Views</span></p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="https://wa.me/?text={{ urlencode($news->title . ' - ' . Request::url()) }}" target="_blank" class="w-9 h-9 rounded-full bg-[#25D366]/10 text-[#25D366] flex items-center justify-center hover:bg-[#25D366] hover:text-white transition" title="Share on WhatsApp">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.652.825 5.12 2.373 7.164l-1.574 5.753 5.881-1.543A11.972 11.972 0 0012.031 24c6.646 0 12.031-5.385 12.031-12.031S18.677 0 12.031 0zm3.924 17.203c-.63.955-1.745 1.353-2.585 1.405-.71.045-1.595-.145-2.618-.545-3.41-1.33-5.61-4.835-5.785-5.07-.175-.235-1.38-1.84-1.38-3.515 0-1.675.875-2.5 1.185-2.825.31-.325.68-.405.905-.405.225 0 .45 0 .645.01.205.01.485-.075.76.59.275.665.94 2.3 1.025 2.475.085.175.14.38.025.605-.115.225-.175.365-.34.555-.165.19-.345.415-.49.565-.16.165-.33.345-.145.665.185.32 825 1.34 1.735 2.16.895.805 1.915 1.115 2.235 1.27.32.155.51.125.7-.095.19-.22.82-1.005 1.045-1.35.225-.345.45-.29.74-.185.29.105 1.835.865 2.15.102.315.16.535.24.605.37.07.13.07.765-.56 1.72z"/></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ Request::url() }}&text={{ urlencode($news->title) }}" target="_blank" class="w-9 h-9 rounded-full bg-black/5 text-black flex items-center justify-center hover:bg-black hover:text-white transition" title="Share on X">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                </div>
            </div>

            @if($news->image)
                <div class="w-full aspect-video mb-8 overflow-hidden rounded-xl shadow-sm bg-slate-100">
                    <img src="{{ asset('uploads/news/' . $news->image) }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            <article class="prose max-w-none text-slate-700 leading-relaxed mb-8 text-lg">
              {!! Illuminate\Support\Str::markdown($contentPart1) !!}
            </article>

            <div class="w-full bg-slate-100 border border-slate-200 rounded-lg py-10 flex flex-col items-center justify-center mb-8 text-slate-400">
    @if(isset($all_ads['in_article']))
        {!! $all_ads['in_article'] !!}
    @else
        <span class="text-xs font-bold uppercase tracking-widest mb-1">Advertisement</span>
        <span class="text-lg">Responsive Ad Block (In-Article)</span>
    @endif
</div>

            <article class="prose max-w-none text-slate-700 leading-relaxed mb-10 text-lg">
               {!! Illuminate\Support\Str::markdown($contentPart2) !!}
            </article>
@if($news->keywords)
                @php
                    // 1. Agar keywords mein comma nahi hai, toh spaces se split karega
                    // 2. Agar comma hai, toh comma se split karega
                    $separator = str_contains($news->keywords, ',') ? ',' : ' ';
                    
                    // 3. Faltu spaces hata kar clean array banayega
                    $tags = array_filter(array_map('trim', explode($separator, $news->keywords)));
                @endphp

                @if(!empty($tags))
                    <div class="mt-8 mb-10 pt-6 border-t border-slate-200">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                            Trending Search Tags
                        </h4>
                        <div class="text-slate-600 font-medium text-sm leading-relaxed">
                            @foreach($tags as $tag)
                                <a href="#" class="hover:text-blue-600 transition font-semibold">#{{ $tag }}</a>@if(!$loop->last)<span class="mr-1">,</span>@endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 py-6 border-t border-b border-slate-200 mb-10">
                <div class="w-full sm:w-1/2">
                    @if($previous)
                        <a href="{{ route('news.show', $previous->slug) }}" class="group block p-3 rounded-lg hover:bg-slate-50 transition border border-transparent hover:border-slate-200">
                            <span class="text-xs text-slate-400 uppercase font-bold tracking-wider block mb-1">&larr; Previous Article</span>
                            <span class="font-semibold text-slate-800 group-hover:text-blue-600 line-clamp-1">{{ $previous->title }}</span>
                        </a>
                    @endif
                </div>
                <div class="w-full sm:w-1/2 text-left sm:text-right">
                    @if($next)
                        <a href="{{ route('news.show', $next->slug) }}" class="group block p-3 rounded-lg hover:bg-slate-50 transition border border-transparent hover:border-slate-200">
                            <span class="text-xs text-slate-400 uppercase font-bold tracking-wider block mb-1">Next Article &rarr;</span>
                            <span class="font-semibold text-slate-800 group-hover:text-blue-600 line-clamp-1">{{ $next->title }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="mb-10">
                <h3 class="text-2xl font-bold text-slate-900 mb-6 border-l-4 border-blue-600 pl-3">Leave a Comment</h3>
                
                <div id="comment-success" class="hidden bg-green-50 text-green-700 border border-green-200 p-4 rounded-xl mb-6 font-medium">
                    <i class="bi bi-check-circle-fill mr-2"></i> <span id="success-text"></span>
                </div>

                <form id="commentForm" class="bg-slate-50 p-6 rounded-xl border border-slate-200 mb-8 shadow-sm">
                    @csrf
                    <input type="hidden" id="news_id" value="{{ $news->id }}">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <input type="text" id="comment_name" placeholder="Your Name" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
                        <input type="email" id="comment_email" placeholder="Email Address (Will not be published)" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
                    </div>
                    <textarea id="comment_body" rows="4" placeholder="Share your thoughts..." class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition mb-4" required></textarea>
                    
                    <button type="submit" id="submitCommentBtn" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        Post Comment
                    </button>
                </form>
                
                <div id="comment-list" class="space-y-6 mt-8">
                    @forelse($news->comments as $comment)
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold flex-shrink-0 uppercase shadow-sm">
                                {{ substr($comment->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="bg-slate-100 p-4 rounded-xl rounded-tl-none border border-slate-200">
                                    <p class="font-bold text-sm text-slate-900">
                                        {{ $comment->name }} 
                                        <span class="text-xs text-slate-400 font-normal ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                    </p>
                                    <p class="text-slate-700 mt-1 text-sm leading-relaxed">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="no-comment-msg" class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                            <p class="text-slate-500 font-medium">No comments yet. Be the first to share your thoughts!</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div> <div class="lg:col-span-1">
            <div class="sticky top-24">
                
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-4 border-b-2 border-black pb-2">
                        <div class="w-3 h-3 bg-red-600"></div>
                        <h3 class="text-lg font-bold text-slate-900 uppercase">Related News</h3>
                    </div>
                    
                    <div class="flex flex-col gap-5">
                        @foreach($relatedNews as $related)
                        <a href="{{ route('news.show', $related->slug) }}" class="group flex gap-3 items-start">
                            <div class="w-24 h-20 flex-shrink-0 overflow-hidden rounded bg-slate-200">
                                @if($related->image)
                                    <img src="{{ asset('uploads/news/' . $related->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800 leading-snug group-hover:text-blue-600 line-clamp-3">
                                    {{ $related->title }}
                                </h4>
                                <span class="text-xs text-slate-400 mt-1 block">{{ $related->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <div class="w-full h-[400px] bg-slate-100 border border-slate-200 rounded-lg flex flex-col items-center justify-center text-slate-400">
    @if(isset($all_ads['sidebar_tall']))
        {!! $all_ads['sidebar_tall'] !!}
    @else
        <span class="text-xs font-bold uppercase tracking-widest mb-1">Advertisement</span>
        <span class="text-center px-4">Sidebar Ad Block<br>(300x400)</span>
    @endif
</div>

            </div>
        </div> </div> <<div class="w-full mt-12 bg-slate-100 border border-slate-200 rounded-lg py-16 flex flex-col items-center justify-center text-slate-400">
    @if(isset($all_ads['footer_banner']))
        {!! $all_ads['footer_banner'] !!}
    @else
        <span class="text-xs font-bold uppercase tracking-widest mb-1">Advertisement</span>
        <span class="text-xl">Leaderboard Banner Ad (728x90)</span>
    @endif
</div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#commentForm').submit(function(e) {
            e.preventDefault(); // Page reload hone se rokna
            
            let btn = $('#submitCommentBtn');
            let originalText = btn.html();
            
            // Button loading state
            btn.html('<span class="spinner-border spinner-border-sm mr-2"></span> Posting...').prop('disabled', true);
            
            let news_id = $('#news_id').val();
            
            $.ajax({
                url: "/news/" + news_id + "/comment",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#comment_name').val(),
                    email: $('#comment_email').val(),
                    comment: $('#comment_body').val()
                },
                success: function(response) {
                    if(response.success) {
                        // Form clear karna
                        $('#commentForm')[0].reset();
                        
                        // Success message dikhana
                        $('#success-text').text(response.message);
                        $('#comment-success').hide().removeClass('hidden').fadeIn('fast').delay(4000).fadeOut('slow');
                        
                        // 'No comments' wala message hata dena
                        $('#no-comment-msg').remove();
                        
                        // Naya comment turant list mein upar add karna
                        let newCommentHtml = `
                            <div class="flex gap-4 hidden new-comment">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold flex-shrink-0 uppercase shadow-sm">
                                    ${response.data.initial}
                                </div>
                                <div>
                                    <div class="bg-slate-100 p-4 rounded-xl rounded-tl-none border border-slate-200 border-l-4 border-l-blue-500">
                                        <p class="font-bold text-sm text-slate-900">
                                            ${response.data.name} 
                                            <span class="text-xs text-slate-400 font-normal ml-2">Just now</span>
                                            <span class="ml-2 bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider">New</span>
                                        </p>
                                        <p class="text-slate-700 mt-1 text-sm leading-relaxed">${response.data.comment}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        $('#comment-list').prepend(newCommentHtml);
                        $('.new-comment').slideDown('slow').removeClass('hidden new-comment');
                    }
                },
                error: function(xhr) {
                    alert("Kuch error aayi hai. Kripya check karein ki saare fields bhare hue hain.");
                },
                complete: function() {
                    // Button normal karna
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush