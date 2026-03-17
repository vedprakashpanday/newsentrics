@extends('layouts.master')

@section('title', 'Trending Insights')

@section('styles')
<style>
    :root { --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    
    .trending-wrapper { background: #f4f7f6; min-height: 90vh; border-radius: 20px 20px 0 0; padding: 20px; }
    
    .stat-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: 15px;
        padding: 30px 20px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    /* List Item Design - Best for Mobile */
    .country-row {
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
        border: 1px solid #eee;
    }

    .country-row:hover { border-color: #764ba2; transform: scale(1.01); }

    .country-info { display: flex; align-items: center; gap: 15px; }
    
    .flag-circle {
        width: 45px;
        height: 45px;
        background: #f0f2f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .country-details h6 { margin: 0; font-weight: 700; color: #333; }
    .country-details small { color: #888; }

    .action-btn {
        background: #f0f2f5;
        color: #555;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: 0.3s;
    }

    .action-btn:hover { background: #764ba2; color: white; }

    /* Mobile Specific Adjustments */
    @media (max-width: 576px) {
        .country-row { flex-direction: column; text-align: center; gap: 15px; }
        .country-info { flex-direction: column; gap: 10px; }
        .action-btn { width: 100%; }
        .stat-header h2 { font-size: 1.5rem; }
    }
</style>
@endsection

@section('content')
<div class="trending-wrapper">
    <div class="stat-header text-center">
        <h2 class="fw-bold mb-2">Newsentric Intel Center</h2>
        <p class="mb-0 opacity-75">Global search trends at your fingertips</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h5 class="mb-4 fw-bold text-secondary text-uppercase small" style="letter-spacing: 1px;">Target Markets</h5>
            
            @php
                $markets = [
                    ['name' => 'India', 'code' => 'IN', 'flag' => '🇮🇳', 'meta' => 'Highest Search Volume'],
                    ['name' => 'United States', 'code' => 'US', 'flag' => '🇺🇸', 'meta' => 'Global News Driver'],
                    ['name' => 'United Kingdom', 'code' => 'GB', 'flag' => '🇬🇧', 'meta' => 'European Pulse'],
                    ['name' => 'Australia', 'code' => 'AU', 'flag' => '🇦🇺', 'meta' => 'Pacific Region Buzz'],
                    ['name' => 'Canada', 'code' => 'CA', 'flag' => '🇨🇦', 'meta' => 'North America Insights']
                ];
            @endphp

            @foreach($markets as $market)
            <div class="country-row shadow-sm">
                <div class="country-info">
                    <div class="flag-circle shadow-sm">{{ $market['flag'] }}</div>
                    <div class="country-details text-start">
                        <h6>{{ $market['name'] }}</h6>
                        <small>{{ $market['meta'] }}</small>
                    </div>
                </div>
                <button onclick="openTrends('{{ $market['code'] }}')" class="action-btn">
                    Launch Trends <i class="bi bi-box-arrow-up-right ms-1"></i>
                </button>
            </div>
            @endforeach

            <div class="mt-5 p-3 rounded-4 bg-white border d-flex align-items-center">
                <div class="me-3 fs-3 text-warning"><i class="bi bi-lightbulb"></i></div>
                <div class="small text-muted">
                    <b>Laptop User?</b> Window popup screen ke side mein set karein.<br>
                    <b>Mobile User?</b> New tab mein open hoga, switch karke data copy karein.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openTrends(code) {
        const url = `https://trends.google.com/trends/trendingsearches/daily?geo=${code}`;
        // Mobile par popup ke bajaye window.open simple kaam karega
        if(window.innerWidth < 768) {
            window.open(url, '_blank');
        } else {
            const width = 1100;
            const height = 800;
            const left = (window.innerWidth - width) / 2;
            const top = (window.innerHeight - height) / 2;
            window.open(url, 'TrendsWindow', `width=${width},height=${height},left=${left},top=${top},scrollbars=yes`);
        }
    }
</script>
@endsection