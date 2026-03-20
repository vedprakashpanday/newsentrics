@extends('layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 font-weight-bold text-primary">Manage Static Pages</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Page Title</th>
                        <th>URL Slug</th>
                        <th>Last Updated</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $page->title }}</td>
                        <td><code class="text-primary">/p/{{ $page->slug }}</code></td>
                        <td>{{ $page->updated_at->diffForHumans() }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square"></i> Edit Content
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection