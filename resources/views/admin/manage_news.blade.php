@extends('layouts.master')

@section('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        /* Desktop Flexbox for Search and Button */
        div.dataTables_wrapper div.dataTables_filter {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap; /* Mobile par wrap hone dega */
        }
        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0; /* Bootstrap default margin hataya */
            display: inline-block;
            width: auto;
        }

        /* Mobile Responsive Fixes */
        @media (max-width: 768px) {
            div.dataTables_wrapper div.dataTables_filter {
                justify-content: space-between; /* Mobile par search aur button ko spread karega */
                margin-top: 15px;
            }
            div.dataTables_wrapper div.dataTables_length {
                margin-bottom: 10px;
            }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white border-bottom-primary d-flex align-items-center">
            <h5 class="m-0 font-weight-bold text-primary"><i class="bi bi-list-task me-2"></i>Manage News Articles</h5>
        </div>
        <div class="card-body p-3 p-md-4">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="newsTable" class="table table-bordered table-hover align-middle text-nowrap" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="12%">Date</th>
                            <th width="8%">Image</th>
                            <th width="35%">Title</th>
                            <th width="15%">Category</th>
                            <th width="15%">Country</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allNews as $news)
                        <tr>
                            <td class="small text-muted" data-sort="{{ $news->created_at }}">{{ $news->created_at->format('d M, Y') }}</td>
                            <td>
                                @if($news->image)
                                    <img src="{{ asset('uploads/news/'.$news->image) }}" width="60" class="rounded shadow-sm" style="height: 40px; object-fit: cover;">
                                @else
                                    <span class="badge bg-secondary">No Image</span>
                                @endif
                            </td>
                            <td class="font-weight-bold text-dark text-wrap" style="min-width: 250px;">{{ Str::limit($news->title, 60) }}</td>
                            <td><span class="badge bg-info text-white border">{{ $news->category->name ?? 'N/A' }}</span></td>
                            <td><span class="badge bg-dark border">{{ $news->country }}</span></td>
                            <td>
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('news.edit', $news->id) }}" class="btn btn-primary btn-sm me-2" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('news.destroy', $news->id) }}" method="POST" onsubmit="return confirm('Pakka delete karna hai? Yeh action undo nahi hoga!')" style="display:inline;">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#newsTable').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 10,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search news..."
                }
            });

            // Mobile friendly Add button
            var addButton = '<a href="{{ route('news.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm ms-2"><i class="bi bi-plus-circle me-1 d-none d-sm-inline"></i> Add News</a>';
            $('#newsTable_filter').append(addButton);
        });
    </script>
@endpush