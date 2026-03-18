@extends('layouts.master')

@section('title', 'Post New News')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">✨ AI Powered Article Creator</h5>
                </div>
                <div class="card-body bg-light">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div id="aiSuccessMessage" class="alert alert-info alert-dismissible fade show d-none" role="alert">
                        <strong><i class="bi bi-magic"></i> AI Magic Complete!</strong> Aapki news ekdum professional tareeke se rewrite ho gayi hai. Review karein aur 'Publish News' par click karein!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data" id="newsForm">
                        @csrf
                        
                        <div class="bg-white p-4 rounded shadow-sm border mb-4">
                            <p class="text-muted small mb-4"><i class="bi bi-info-circle"></i> <strong>Tip:</strong> Google Trends se rough data copy karein aur fields mein paste kar dein. AI apne aap ise ek premium news article mein badal dega!</p>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Rough / Trending Heading</label>
                                <input type="text" name="title" id="newsTitle" class="form-control form-control-lg" placeholder="e.g. Google Trends heading..." required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Target Country</label>
                                    <select name="country" id="newsCountry" class="form-select" required>
                                        <option value="">-- Select Country --</option>
                                        <option value="India">India</option>
                                        <option value="USA">USA</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Canada">Canada</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">News Category</label>
                                    <select name="category_id" id="newsCategory" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Rough Content / Snippet</label>
                                <textarea name="content" id="newsContent" rows="6" class="form-control" placeholder="Paste rough news text, facts, or bullet points here..." required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Keywords / Trends</label>
                                    <input type="text" id="newsKeywords" name="keywords" class="form-control" placeholder="e.g. technology, AI, future...">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Feature Image (.webp auto-convert)</label>
                                    <input type="file" name="image" id="newsImage" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="button" id="generateBtn" class="btn btn-dark btn-lg w-50 fw-bold d-flex justify-content-center align-items-center">
                                <span class="me-2">✨</span> Generate AI Magic
                            </button>
                            
                            <button type="submit" id="publishBtn" class="btn btn-success btn-lg w-50 fw-bold d-flex justify-content-center align-items-center">
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
            // 1. Saari fields ka data uthao
            let title = $('#newsTitle').val();
            let content = $('#newsContent').val();
            let keywords = $('#newsKeywords').val();
            let country = $('#newsCountry').val();
            let category_id = $('#newsCategory').val();
            let category_name = $('#newsCategory option:selected').text(); // AI ko text name bhejna zyada better hai

            // Basic validation: AI ke liye kam se kam Heading ya content hona zaroori hai
            if(!title && !content) { 
                alert("Please enter some rough heading or content for the AI to process!"); 
                $('#newsTitle').focus();
                return; 
            }

            let btn = $(this);
            let originalBtnText = btn.html();
            
            // UI Update - Loading State
            btn.html('<span class="spinner-border spinner-border-sm me-2"></span> AI is Writing...').prop('disabled', true);
            $('#aiSuccessMessage').addClass('d-none'); // Hide success message if previously shown
            
            // 2. AJAX ke zariye backend ko bhejo
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
                        /* Yahan hum assume kar rahe hain ki aapka backend Controller 
                           JSON response wapas karega jisme refined data hoga.
                           Example: { success: true, title: "...", content: "...", keywords: "..." }
                        */
                        
                        // 3. AI se aaye hue data ko wapas fields mein fill karna
                        if(response.title) $('#newsTitle').val(response.title);
                        if(response.content) $('#newsContent').val(response.content);
                        if(response.keywords) $('#newsKeywords').val(response.keywords);
                        // Agar AI country ya category bhi suggest karta hai toh wo bhi yahan set kar sakte hain

                        // 4. Success Message Dikhayein
                        $('#aiSuccessMessage').removeClass('d-none').hide().fadeIn('slow');
                        
                        // User ka dhyan message par le jane ke liye thoda scroll
                        $('html, body').animate({ scrollTop: $(".card").offset().top - 50 }, 500);

                    } else {
                        alert("API Error: " + response.message);
                    }
                },
                error: function() {
                    alert("Server error! Backend controller ya API check karein.");
                },
                complete: function() {
                    // Button wapas normal karo
                    btn.html(originalBtnText).prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush