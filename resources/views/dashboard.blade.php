@extends('layouts.master')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Dashboard Overview</h3>
            <p class="text-muted small mb-0">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! Here's what's happening today.</p>
        </div>
        <div class="w-100" style="max-width: 200px;">
            <a href="{{ route('news.create') }}" class="btn btn-primary shadow-sm fw-bold w-100">
                <i class="bi bi-plus-circle me-1"></i> Add New Post
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 border-start border-4 border-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1" style="font-size: 0.8rem;">Total News</div>
                            <div class="h3 mb-0 fw-black text-dark">{{ \App\Models\News::count() }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-newspaper text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 border-start border-4 border-success shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-success text-uppercase mb-1" style="font-size: 0.8rem;">Total Views</div>
                            <div class="h3 mb-0 fw-black text-dark">{{ \App\Models\News::sum('view_count') ?? 0 }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-eye text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 border-start border-4 border-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1" style="font-size: 0.8rem;">Total Comments</div>
                            <div class="h3 mb-0 fw-black text-dark">{{ \App\Models\Comment::count() ?? 0 }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-chat-dots text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 border-start border-4 border-info shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-info text-uppercase mb-1" style="font-size: 0.8rem;">Categories</div>
                            <div class="h3 mb-0 fw-black text-dark">{{ \App\Models\Category::count() ?? 0 }}</div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-tags text-info fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-activity me-2 text-primary"></i>Recent News Activity</h6>
                    <a href="{{ route('news.manage') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Title</th>
                                    <th>Category</th>
                                    <th>Date Posted</th>
                                    <th class="text-end pe-4">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\News::with('category')->latest()->take(5)->get() as $recent)
                                <tr>
                                    <td class="ps-4 fw-medium text-dark text-wrap" style="min-width: 250px;">
                                        <a href="{{ route('news.edit', $recent->id) }}" class="text-decoration-none text-dark">
                                            {{ Str::limit($recent->title, 60) }}
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $recent->category->name ?? 'N/A' }}</span></td>
                                    <td class="text-muted small">{{ $recent->created_at->diffForHumans() }}</td>
                                    <td class="text-end pe-4 fw-bold text-primary">{{ number_format($recent->view_count) }} <i class="bi bi-eye ms-1"></i></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection