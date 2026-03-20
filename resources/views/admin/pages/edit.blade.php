@extends('layouts.master')

@section('title', 'Edit Page: ' . $page->title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i> Edit Page: {{ $page->title }}</h3>
        <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold text-slate-700">Page Title</label>
                        <input type="text" name="title" class="form-control form-control-lg bg-light border-0 shadow-sm" value="{{ $page->title }}" required>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold text-slate-700">Meta Description (For SEO)</label>
                        <textarea name="meta_description" class="form-control bg-light border-0 shadow-sm" rows="2" placeholder="Short summary for search engines...">{{ $page->meta_description }}</textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold text-slate-700">Page Content</label>
                        <textarea name="content" id="summernote" class="form-control" required>{{ $page->content }}</textarea>
                    </div>
                </div>

                <div class="mt-3 border-top pt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 shadow">
                        <i class="bi bi-save me-2"></i> Update Page Content
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
    
    <style>
        /* Editor container ko sundar banane ke liye */
        .note-editor.note-frame { 
            border: 1px solid #dee2e6 !important; 
            border-radius: 0.5rem !important; 
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .note-toolbar { 
            background-color: #f8fafc !important; 
            border-bottom: 1px solid #dee2e6 !important; 
        }
        /* Dashboard ke dropdowns ke peeche na chhup jaye */
        .note-editor .note-editing-area { z-index: 0; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'Yahan apna content likhein...',
                tabsize: 2,
                height: 400,
                // Lite version mein ye toolbar ekdum sahi dikhega
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
@endpush