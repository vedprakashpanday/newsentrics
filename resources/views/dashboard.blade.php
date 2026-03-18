@extends('layouts.master') @section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
        <h4 class="text-slate-400 text-sm font-bold uppercase mb-1">Total News</h4>
        <p class="text-3xl font-black text-slate-900">{{ \App\Models\News::count() }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
        <h4 class="text-slate-400 text-sm font-bold uppercase mb-1">Total Comments</h4>
        <p class="text-3xl font-black text-slate-900">{{ \App\Models\Comment::count() }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
        <h4 class="text-slate-400 text-sm font-bold uppercase mb-1">Total Views</h4>
        <p class="text-3xl font-black text-slate-900">{{ \App\Models\News::sum('view_count') }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <h3 class="font-bold text-lg mb-4">Recent News Activity</h3>
    </div>
@endsection