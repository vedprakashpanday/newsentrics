@extends('layouts.master')

@section('title', 'Fetched News Management')

@push('styles')
    

    
    <style>
        .news-img-thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .action-btns .btn {
            margin-right: 2px;
        }
        table.dataTable > tbody > tr > td {
            vertical-align: middle;
        }
    </style>
@endpush
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-gray-800"><i class="fas fa-rss text-primary"></i> Fetched News List</h2>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add News Manually
        </a>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Pending & Published News</h6>
            <button onclick="window.location.reload();" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="fetchedNewsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">Image</th>
                            <th width="35%">Title & Source</th>
                            <th width="10%">Country</th>
                            <th width="10%">Status</th>
                            <th width="15%">Fetched On</th>
                            <th width="20%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($news as $item)
                        <tr>
                            <td class="text-center">
                                <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset('uploads/news/' . $item->image) }}" 
                                     class="news-img-thumbnail" 
                                     onerror="this.src='https://placehold.co/80x60?text=No+Image'">
                            </td>
                            <td>
                                <strong class="text-dark d-block mb-1">{{ Str::limit($item->title, 60) }}</strong>
                                <span class="badge bg-secondary text-white shadow-sm">{{ $item->source }}</span>
                            </td>
                            <td>
                                <span class="badge border border-dark text-dark">{{ $item->country }}</span>
                            </td>
                            <td>
                                @if($item->status == 0)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pending</span>
                                @elseif($item->status == 1)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Published</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Rejected</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="far fa-calendar-alt"></i> {{ $item->created_at->format('d M, Y') }}<br>
                                    <i class="far fa-clock"></i> {{ $item->created_at->format('h:i A') }}
                                </span>
                            </td>
                            <td class="text-center action-btns">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Approve Button --}}
                                    @if($item->status != 1)
                                    <form action="{{ route('admin.news.approve', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success shadow-sm" title="Approve & Publish">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- AI Edit Button --}}
                                    <a href="{{ route('admin.news.ai_edit', $item->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Expand with AI & Edit">
                                        <i class="fas fa-magic"></i>
                                    </a>

                                    {{-- Reject Button --}}
                                    @if($item->status != 2)
                                    <form action="{{ route('admin.news.reject', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this news?');">
                                        @csrf
                                        <button class="btn btn-sm btn-danger shadow-sm" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-none">
                {{ $news->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#fetchedNewsTable').DataTable({
                "pageLength": 10,           // Kitni news ek page par dikhani hain
                "order": [[ 4, "desc" ]],   // Default sorting by Date (Column index 4)
                "responsive": true,         // Mobile friendly
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search News, Source or Country..."
                },
                "columnDefs": [
                    { "orderable": false, "targets": [0, 5] } // Image aur Actions column ko sortable mat rakho
                ]
            });
        });
    </script>
@endpush