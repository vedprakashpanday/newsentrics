@extends('layouts.master')
@section('content')
<div class="container-fluid py-4">
    <h3 class="fw-bold mb-4">Manage Advertisements</h3>
    <div class="row">
        @foreach($ads as $ad)
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h5 class="fw-bold text-primary">{{ $ad->location_name }}</h5>
                    <form action="{{ route('admin.ads.update', $ad->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Ad Script / HTML Code</label>
                            <textarea name="ad_code" class="form-control font-monospace" rows="5" placeholder="Paste AdSense code here...">{{ $ad->ad_code }}</textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" {{ $ad->is_active ? 'checked' : '' }}>
                                <label class="form-check-label">Status (Active)</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm px-4">Save Ad</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection