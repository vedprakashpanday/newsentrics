@extends('layouts.master')

@section('content')
<div class="card shadow border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-envelope-paper me-2"></i>User Inquiries / Messages</h5>
    </div>
    <div class="card-body p-4"> <table id="contactTable" class="table table-hover align-middle mb-0 w-100">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Sender</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($messages as $msg)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $msg->name }}</div>
                        <small class="text-muted">{{ $msg->email }}</small>
                    </td>
                    <td><p class="mb-0 small" style="max-width: 400px;">{{ $msg->message }}</p></td>
                    <td>{{ $msg->created_at->format('d M, Y') }}</td>
                    <td class="text-end pe-4">
                        <form action="{{ route('admin.messages.destroy', $msg->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        .dt-buttons { margin-bottom: 15px; }
        .btn-excel { background-color: #198754 !important; color: white !important; border: none !important; }
        .btn-excel:hover { background-color: #157347 !important; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Aaj ki date format: DD-MM-YYYY
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); 
            var yyyy = today.getFullYear();
            var currentDate = dd + '-' + mm + '-' + yyyy;

            $('#contactTable').DataTable({
                "dom": '<"d-flex justify-content-between align-items-center"Bf>rt<"d-flex justify-content-between align-items-center"ip>',
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Download Excel',
                        className: 'btn btn-excel btn-sm shadow-sm',
                        title: 'Contact Person Details',
                        filename: "contact_person's details_" + currentDate,
                        exportOptions: {
                            columns: [0, 1, 2] // Sirf Sender, Message aur Date export karega (Action nahi)
                        }
                    }
                ],
                "pageLength": 10,
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search inquiries..."
                }
            });
        });
    </script>
@endpush