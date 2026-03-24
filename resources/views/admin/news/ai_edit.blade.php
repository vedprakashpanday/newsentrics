@extends('layouts.master')

@section('title', 'AI News Editor')

@push('styles')
<style>
    /* Image Preview Wrapper */
    .preview-wrapper {
        position: relative;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    
    /* Image Thumbnail */
    .preview-wrapper img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    /* Cross (Remove) Button */
    .remove-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545; /* Bootstrap Danger Red */
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: 0.2s;
    }
    
    .remove-btn:hover {
        background: #c82333;
        transform: scale(1.1);
    }
</style>
@endpush


@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h4 class="mb-0"><i class="fas fa-robot"></i> AI News Generator & Media</h4>
            <span class="badge bg-info">Source: {{ $news->source }}</span>
        </div>
        
        <div class="card-body">
            <form action="{{ route('admin.news.ai_update', $news->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-5">
                        
                        <h5 class="fw-bold text-primary border-bottom pb-2">1. News Details</h5>
                        
                        <div class="form-group mb-3">
                            <label class="fw-bold">News Headline</label>
                            <input type="text" id="news_title" name="title" class="form-control" value="{{ $news->title }}" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="fw-bold">Category</label>
                            <select name="category_id" class="form-select">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $news->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-bold">SEO Keywords</label>
                            <input type="text" id="news_keywords" name="keywords" class="form-control" value="{{ $news->keywords }}">
                        </div>

                        <div class="alert alert-secondary text-center p-2 mb-4">
                            <p class="small text-muted mb-2">Let AI expand the content and generate keywords.</p>
                            <button type="button" id="btn_generate_ai" class="btn btn-primary w-100 fw-bold">
                                <i class="fas fa-magic"></i> Generate Full Article
                            </button>
                        </div>

                   <h5 class="fw-bold text-success border-bottom pb-2">2. Media Files</h5>

<div class="form-group mb-3">
    <label class="fw-bold">Main News Image</label>
    <input type="file" id="main_image_input" name="image" class="form-control" accept="image/*">
    <small class="text-muted">Upload a new HD image.</small>
    
    <div id="main_image_preview" class="mt-3"></div>
</div>
<div class="form-group mb-3">
    <label class="fw-bold">Gallery Images (Optional)</label>
    <input type="file" id="gallery_images_input" name="gallery[]" class="form-control" accept="image/*" multiple>
    <small class="text-muted">Select multiple images by holding Ctrl/Cmd.</small>
    
    <div id="gallery_images_preview" class="mt-3 d-flex flex-wrap"></div>
</div>
                    </div>

                    <div class="col-md-7">
                        <h5 class="fw-bold text-dark border-bottom pb-2">3. Article Content</h5>
                        <div class="form-group mb-3">
                            <textarea name="content" id="news_content" class="form-control" style="height: 600px;" required>{{ $news->content }}</textarea>
                            <small class="text-muted mt-1 d-block">You can manually format this text or let AI do it.</small>
                        </div>
                    </div>
                </div>

                <hr>
                
                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm">
                        <i class="fas fa-save"></i> Save & Publish
                    </button>
                </div>
                
                <input type="hidden" name="country" value="{{ $news->country }}">
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>

// ==========================================
    // 1. MAIN IMAGE PREVIEW & REMOVE LOGIC
    // ==========================================
    const mainInput = document.getElementById('main_image_input');
    const mainPreview = document.getElementById('main_image_preview');

    mainInput.addEventListener('change', function() {
        mainPreview.innerHTML = ''; // Pehle se kuch hai toh hata do
        
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                // Wrapper aur cross button banayein
                let wrapper = document.createElement('div');
                wrapper.className = 'preview-wrapper';
                wrapper.innerHTML = `
                    <img src="${e.target.result}" alt="Main Image Preview">
                    <button type="button" class="remove-btn" onclick="removeMainImage()" title="Remove Image">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                mainPreview.appendChild(wrapper);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Main image remove karne ka function
    function removeMainImage() {
        mainInput.value = ''; // Input file clear karo
        mainPreview.innerHTML = ''; // Preview container khali karo
    }

    // ==========================================
    // 2. GALLERY IMAGES PREVIEW & REMOVE LOGIC
    // ==========================================
    const galleryInput = document.getElementById('gallery_images_input');
    const galleryPreview = document.getElementById('gallery_images_preview');
    
    // DataTransfer object mutiple files ko handle karne me help karta hai
    let galleryDataTransfer = new DataTransfer(); 

    galleryInput.addEventListener('change', function() {
        // Nayi selected files ko existing array me jodo
        for(let i = 0; i < this.files.length; i++) {
            galleryDataTransfer.items.add(this.files[i]);
        }
        
        // Input ko update karo saari files ke sath
        this.files = galleryDataTransfer.files;
        
        // UI render karo
        renderGallery();
    });

    function renderGallery() {
        galleryPreview.innerHTML = ''; // Pura container clear karo
        
        let files = galleryDataTransfer.files;
        Array.from(files).forEach((file, index) => {
            let reader = new FileReader();
            reader.onload = function(e) {
                let wrapper = document.createElement('div');
                wrapper.className = 'preview-wrapper';
                wrapper.innerHTML = `
                    <img src="${e.target.result}" alt="Gallery Preview">
                    <button type="button" class="remove-btn" onclick="removeGalleryImage(${index})" title="Remove Image">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                galleryPreview.appendChild(wrapper);
            }
            reader.readAsDataURL(file);
        });
    }

    // Gallery se specific image hatane ka function
    function removeGalleryImage(indexToRemove) {
        let newDataTransfer = new DataTransfer();
        let currentFiles = galleryDataTransfer.files;
        
        // Jis index pe click hua hai, usko chhod kar baki sab naye container me daal do
        for(let i = 0; i < currentFiles.length; i++) {
            if(i !== indexToRemove) {
                newDataTransfer.items.add(currentFiles[i]);
            }
        }
        
        // Purane ko naye se replace karo
        galleryDataTransfer = newDataTransfer;
        galleryInput.files = galleryDataTransfer.files; // Input box ko bhi update karo
        
        // Dobara render karo
        renderGallery();
    }






document.getElementById('btn_generate_ai').addEventListener('click', function() {
    let title = document.getElementById('news_title').value;
    let currentContent = document.getElementById('news_content').value;
    let btn = this;
    
    // UI Feedback
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Writing... Please wait';
    btn.classList.replace('btn-primary', 'btn-warning');

   axios.post("{{ route('admin.generate_fetched.ai') }}", {
        title: title,
        content: currentContent 
    })
    .then(response => {
        if(response.data.success) {
            // Populate Content Field
            document.getElementById('news_content').value = response.data.content;
            
            // Auto-fill keywords if empty
            let keywordField = document.getElementById('news_keywords');
            if(!keywordField.value && response.data.keywords) {
                keywordField.value = response.data.keywords;
            }

            alert("✨ Article generated successfully!");
        } else {
            alert("AI Error: " + response.data.message);
        }
    })
    .catch(error => {
        console.error(error);
        alert("Something went wrong with the server request.");
    })
    .finally(() => {
        // Reset Button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-magic"></i> Generate Full Article';
        btn.classList.replace('btn-warning', 'btn-primary');
    });
});
</script>
@endsection