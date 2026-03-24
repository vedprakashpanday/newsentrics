@extends('layouts.master')

@section('title', 'Edit News')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                    <h5 class="mb-0 fs-6 fs-md-5"><i class="bi bi-pencil-square"></i> Edit News Article</h5>
                    <a href="{{ route('admin.news.manage') }}" class="btn btn-outline-light btn-sm text-nowrap">Back to List</a>
                </div>
                
                <div class="card-body bg-light p-3 p-md-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="aiSuccessMessage" class="alert alert-info alert-dismissible fade show d-none" role="alert">
                        <strong><i class="bi bi-magic"></i> AI Magic Complete!</strong> Aapki news update ho gayi hai. Review karein aur 'Update News' par click karein!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data" id="newsForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="bg-white p-3 p-md-4 rounded shadow-sm border mb-4">
                            <p class="text-muted small mb-4"><i class="bi bi-info-circle text-primary"></i> <strong>Tip:</strong> Aap yahan bhi AI ka use karke content rewrite karwa sakte hain.</p>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Heading / Title</label>
                                <input type="text" name="title" id="newsTitle" class="form-control form-control-lg fs-6 fs-md-5" value="{{ old('title', $news->title) }}" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Target Country</label>
                                    <select name="country" id="newsCountry" class="form-select" required>
                                        <option value="">-- Select Country --</option>
                                        <option value="India" {{ $news->country == 'India' ? 'selected' : '' }}>India</option>
                                        <option value="USA" {{ $news->country == 'USA' ? 'selected' : '' }}>USA</option>
                                        <option value="UK" {{ $news->country == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                        <option value="Australia" {{ $news->country == 'Australia' ? 'selected' : '' }}>Australia</option>
                                        <option value="Canada" {{ $news->country == 'Canada' ? 'selected' : '' }}>Canada</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">News Category</label>
                                    <select name="category_id" id="newsCategory" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $news->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Content</label>
                                <textarea name="content" id="newsContent" rows="8" class="form-control" required>{{ old('content', $news->content) }}</textarea>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Keywords / Trends</label>
                                    <input type="text" id="newsKeywords" name="keywords" class="form-control" value="{{ old('keywords', $news->keywords) }}">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Feature Image</label>
                                    <input type="file" name="image" id="newsImage" class="form-control" accept="image/*">
                                    <div class="mt-2 text-muted small">Nayi image upload karne par purani delete ho jayegi.</div>
                                    
                                    @if($news->image)
                                        <div class="mt-3 border rounded p-2 d-inline-block bg-light">
                                            <span class="small d-block mb-2 fw-bold text-secondary">Current Image:</span>
                                            <img src="{{ asset('uploads/news/' . $news->image) }}" alt="Current Image" class="img-fluid rounded shadow-sm" style="max-height: 120px; object-fit: cover;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-3">
                            <button type="button" id="generateBtn" class="btn btn-dark btn-lg w-100 fw-bold d-flex justify-content-center align-items-center">
                                <span class="me-2">✨</span> Rewrite with AI
                            </button>
                            
                            <button type="submit" id="updateBtn" class="btn btn-success btn-lg w-100 fw-bold d-flex justify-content-center align-items-center">
                                <i class="bi bi-save me-2"></i> Update News
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#generateBtn').on('click', function() {
            let title = $('#newsTitle').val();
            let content = $('#newsContent').val();
            let keywords = $('#newsKeywords').val();
            let country = $('#newsCountry').val();
            let category_name = $('#newsCategory option:selected').text();

            if(!title && !content) { 
                alert("Heading ya content hona zaroori hai AI ke liye!"); 
                $('#newsTitle').focus();
                return; 
            }

            let btn = $(this);
            let originalBtnText = btn.html();
            
            btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Rewriting...').prop('disabled', true);
            $('#aiSuccessMessage').addClass('d-none');
            
            $.ajax({
                url: "{{ route('admin.generate.ai') }}",
                method: "POST",
                data: { 
                    _token: "{{ csrf_token() }}", 
                    title: title,
                    content: content,
                    keywords: keywords,
                    country: country,
                    category: category_name 
                },
                success: function(response) {
                    if(response.success) {
                        if(response.title) $('#newsTitle').val(response.title);
                        if(response.content) $('#newsContent').val(response.content);
                        if(response.keywords) $('#newsKeywords').val(response.keywords);

                        $('#aiSuccessMessage').removeClass('d-none').hide().fadeIn('slow');
                        $('html, body').animate({ scrollTop: $(".card").offset().top - 50 }, 500);
                    } else {
                        alert("API Error: " + response.message);
                    }
                },
                error: function() {
                    alert("Server error! Backend check karein.");
                },
                complete: function() {
                    btn.html(originalBtnText).prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush