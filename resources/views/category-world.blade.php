@extends('layouts.frontend')

@section('title', 'World News - Global Updates')

@section('content')
<div class="mb-10">
    <h1 class="text-3xl font-extrabold text-slate-900 border-l-4 border-blue-600 pl-4 mb-2">
        World News
    </h1>
    <p class="text-slate-500">Global updates from around the world, excluding {{ $userCountry }}.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    @foreach($worldNews as $news)
        @include('components.news-card', ['news' => $news])
    @endforeach
</div>

<div class="mt-12">
    {{ $worldNews->links() }} 
</div>
@endsection