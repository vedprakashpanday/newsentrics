@extends('layouts.master')

@section('title', 'Manage Comments')

@section('content')
<div class="card shadow border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary">User Comments Moderation</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">User</th>
                    <th>Comment</th>
                    <th>News Post</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $comment->name }}</div>
                        <small class="text-muted">{{ $comment->email }}</small>
                    </td>
                    <td><p class="mb-0 small text-wrap" style="max-width: 300px;">{{ $comment->comment }}</p></td>
                    <td><a href="{{ route('news.show', $comment->news->slug) }}" target="_blank" class="text-decoration-none small">{{ Str::limit($comment->news->title, 40) }}</a></td>
                    <td>
                        @if($comment->status == 1)
                            <span class="badge bg-success-subtle text-success border border-success">Approved</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning">Pending</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        @if($comment->status == 0)
                        <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                        </form>
                        @endif
                        
                        <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Pakka uda de?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection