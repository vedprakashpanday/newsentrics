@extends('layouts.master')

@section('title', 'Post New News')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Add New Article - Newsentric</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
                    <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="mb-4 p-3 bg-light rounded border border-warning">
        <label class="form-label fw-bold text-danger">✨ AI News Generator (Paste Google Trends Here)</label>
        <div class="input-group">
            <input type="text" id="aiKeywords" name="keywords" class="form-control" placeholder="e.g. Oscars 2026 winners, Best Actor...">
            <button type="button" id="generateBtn" class="btn btn-dark">✨ Generate AI News</button>
        </div>
        <small class="text-muted">Ye keywords SEO meta tags mein bhi automatically save ho jayenge.</small>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">News Heading</label>
        <input type="text" name="title" class="form-control" placeholder="Enter trending heading..." required>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Target Country</label>
            <select name="country" class="form-select" required>
                <option value="">-- Select Country --</option>
                <option value="India">India</option>
                <option value="USA">USA</option>
                <option value="UK">United Kingdom</option>
                <option value="Australia">Australia</option>
                <option value="Canada">Canada</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label fw-bold">News Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Feature Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">News Content</label>
        <textarea name="content" rows="10" class="form-control" placeholder="Write full news details here..." required></textarea>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-success btn-lg fw-bold">
            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Publish News
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
            let keywords = $('#aiKeywords').val();
            if(!keywords) { 
                alert("Pehle trends copy karke keywords box mein daaliye!"); 
                return; 
            }

            let btn = $(this);
            btn.html('<span class="spinner-border spinner-border-sm"></span> Writing...').prop('disabled', true);
            
            $.ajax({
                url: "{{ route('admin.generate.ai') }}",
                method: "POST",
                data: { 
                    _token: "{{ csrf_token() }}", 
                    keywords: keywords 
                },
                success: function(response) {
                    console.log(response);
                    
                    if(response.success) {
                        // AI response ko fields mein set karna
                        // Agar response mein Heading: aur Content: hai toh split karein
                        let text = response.text;
                        if(text.includes('Content:')) {
                            let parts = text.split('Content:');
                            let heading = parts[0].replace('Heading:', '').trim();
                            let content = parts[1].trim();
                            $('input[name="title"]').val(heading);
                            $('textarea[name="content"]').val(content);
                        } else {
                            $('textarea[name="content"]').val(text);
                        }
                    } else {
                        alert("API Error: " + response.message);
                    }
                },
                error: function() {
                    alert("Server error! Check your API key or Route.");
                },
                complete: function() {
                    btn.html('✨ Generate AI News').prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush

