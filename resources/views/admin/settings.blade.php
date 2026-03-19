@extends('layouts.master')

@section('title', 'Global Site Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-gear-fill me-2 text-primary"></i> Site Settings</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Basic Info</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Site Name</label>
                            <input type="text" name="site_name" class="form-control" value="{{ $setting->site_name ?? 'Newsentric' }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Site Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            @if(isset($setting->logo))
                                <div class="mt-2 p-2 bg-light border rounded d-inline-block">
                                    <img src="{{ asset('uploads/logo/' . $setting->logo) }}" height="50" alt="Current Logo">
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Footer About Text</label>
                            <textarea name="footer_about" class="form-control" rows="4" placeholder="Write a short description for the footer...">{{ $setting->footer_about ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Social Media Links</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-facebook text-primary me-1"></i> Facebook URL</label>
                            <input type="url" name="facebook" class="form-control" value="{{ $setting->facebook ?? '' }}" placeholder="https://facebook.com/yourpage">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-twitter-x text-dark me-1"></i> Twitter (X) URL</label>
                            <input type="url" name="twitter" class="form-control" value="{{ $setting->twitter ?? '' }}" placeholder="https://twitter.com/yourhandle">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-instagram text-danger me-1"></i> Instagram URL</label>
                            <input type="url" name="instagram" class="form-control" value="{{ $setting->instagram ?? '' }}" placeholder="https://instagram.com/yourprofile">
                        </div>
                    </div>

                </div>

                <div class="mt-4 border-top pt-4">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 shadow-sm">
                        <i class="bi bi-save me-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection