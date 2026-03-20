@extends('layouts.frontend')

@section('title', $page->title)

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4">
   <h1 class="text-4xl font-extrabold text-slate-900 mb-2">{{ $page->title }}</h1>
<p class="text-slate-400 text-sm mb-8 italic">Last Updated: {{ $page->updated_at->format('F d, Y') }}</p>

<div class="prose max-w-none">
    {!! $page->content !!}
</div>
</div>
@endsection