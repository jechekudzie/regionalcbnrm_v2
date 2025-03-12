@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Modules and Permissions Display -->
        <div class="col-xxl-8 col-lg-8 col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">System Modules & Permissions</h5>
                </div>
                <div class="card-body">
                    @foreach($modules as $module)
                    <div class="module-section mb-4">
                        <div class="d-flex align-items-center bg-light p-3 rounded">
                            <h6 class="mb-0 flex-grow-1">{{ $module->name }}</h6>
                            <span class="badge bg-primary">
                                {{ $permissions->filter(function($permission) use ($module) {
                                    return str_contains($permission->name, "-{$module->slug}");
                                })->count() }} Permissions
                            </span>
                        </div>
                        <div class="permissions-grid mt-3">
                            <div class="row g-3">
                                @php
                                    $modulePermissions = $permissions->filter(function($permission) use ($module) {
                                        return str_contains($permission->name, "-{$module->slug}");
                                    });
                                @endphp
                                
                                @foreach($modulePermissions as $permission)
                                <div class="col-md-4">
                                    <div class="permission-card p-2 border rounded">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2"><i class="fas fa-key"></i></span>
                                            <span class="flex-grow-1 permission-name">{{ $permission->name }}</span>
                                            @if(!in_array($permission->name, [
                                                "view-{$module->slug}", 
                                                "create-{$module->slug}", 
                                                "read-{$module->slug}", 
                                                "update-{$module->slug}", 
                                                "delete-{$module->slug}"
                                            ]))
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="event.preventDefault(); 
                                                                 if(confirm('Are you sure you want to delete this permission?')) {
                                                                     document.getElementById('delete-permission-{{ $permission->id }}').submit();
                                                                 }">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-permission-{{ $permission->id }}" 
                                                      action="{{ route('admin.permissions.destroy', $permission->id) }}" 
                                                      method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="col-xxl-4 col-lg-4 col-md-4">
            <!-- Add Module Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Add New Module</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.permissions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="permission_type" value="module">
                        <div class="mb-3">
                            <label class="form-label">Module Name</label>
                            <input type="text" class="form-control" name="name" required
                                   placeholder="Enter module name">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                Create Module
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add Custom Permission Form -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Add Custom Permission</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.permissions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="permission_type" value="individual">
                        <div class="mb-3">
                            <label class="form-label">Select Module</label>
                            <select class="form-select" name="module_id" required>
                                <option value="">Choose module...</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permission Name</label>
                            <input type="text" class="form-control" name="name" required
                                   placeholder="Enter permission name">
                            <small class="text-muted">This will be automatically prefixed with the module name</small>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                Add Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.module-section {
    border-bottom: 1px solid #eee;
}
.module-section:last-child {
    border-bottom: none;
}
.permission-card {
    background-color: #fff;
    transition: all 0.3s ease;
}
.permission-card:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.permission-name {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}
</style>
@endsection

@push('head')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endpush

@push('scripts')
    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            $('#buttons-datatables').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'print', 'pdf']
            });
        });

        $(document).ready(function () {
            var submitButton = $('#submit-button');
            submitButton.text('Add New');
            $('#name').val('');

            $('#new-button').on('click', function () {
                $('#edit-form').attr('action', '{{ route("admin.permissions.store") }}');
                $('input[name="_method"]').val('POST');
                submitButton.text('Add New');
                $('#name').val('');
                $('#card-title').text('Add System Modules');
                $('#page-title').text('Add New System Modules');
            });
        });
    </script>
@endpush
