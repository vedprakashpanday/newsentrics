@extends('layouts.master')

@section('title', 'Manage Categories')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-tags me-2 text-primary"></i> Category Management</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-top border-4 border-primary">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark">Add New Category</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Category Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Sports, Politics" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Save Category
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Category Name</th>
                                    <th>Slug (URL)</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td class="ps-4 text-muted">#{{ $category->id }}</td>
                                    <td class="fw-bold text-dark">{{ $category->name }}</td>
                                    <td><span class="badge bg-light text-secondary border">{{ $category->slug }}</span></td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}')">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pakka delete karna hai?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No categories found. Add one from the left!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Category Name</label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // JS function to pass data to the modal
    function editCategory(id, name) {
        // Set Action URL dynamically
        $('#editCategoryForm').attr('action', '/admin/categories/' + id);
        // Fill the input with old name
        $('#editCategoryName').val(name);
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        editModal.show();
    }
</script>
@endpush