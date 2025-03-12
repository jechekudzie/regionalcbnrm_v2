@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h4 class="text-primary mb-1">Permission Assignment</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.organisation-roles.index', $organisation->slug) }}">{{ $organisation->name }}</a>
                            </li>
                            <li class="breadcrumb-item">{{ $role->name }}</li>
                            <li class="breadcrumb-item active">Assign Permissions</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.organisation-roles.index', $organisation->slug) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Roles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <form action="{{ route('admin.permissions.assign-permission-to-role', [$organisation->slug, $role->id]) }}" 
                          method="POST">
                        @csrf
                        
                        <div class="accordion" id="modulesAccordion">
                            @foreach($modules as $module)
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#module{{ $module->id }}">
                                        <div class="d-flex align-items-center w-100">
                                            <div class="me-auto">
                                                <h6 class="mb-0">{{ $module->name }}</h6>
                                                <small class="text-muted">Configure access levels for this module</small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">
                                                {{ $module->permission_count }} Permissions
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="module{{ $module->id }}" 
                                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                     data-bs-parent="#modulesAccordion">
                                    <div class="accordion-body p-4">
                                        <!-- Module Controls -->
                                        <div class="d-flex justify-content-end mb-3">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" 
                                                        class="btn btn-outline-secondary select-module" 
                                                        data-module="{{ $module->id }}">
                                                    <i class="fas fa-check-square me-1"></i> Select All
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-outline-secondary deselect-module" 
                                                        data-module="{{ $module->id }}">
                                                    <i class="fas fa-square me-1"></i> Deselect All
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Default Permissions -->
                                        <div class="permission-section mb-4">
                                            <h6 class="text-primary mb-3">
                                                <i class="fas fa-shield-alt me-2"></i>Default Permissions
                                            </h6>
                                            <div class="row g-3">
                                                @foreach(['view', 'create', 'read', 'update', 'delete'] as $action)
                                                    @php
                                                        $permissionName = "{$action}-{$module->slug}";
                                                        $hasPermission = $rolePermissions->contains('name', $permissionName);
                                                    @endphp
                                                    <div class="col-md-4 col-lg-3">
                                                        <div class="permission-card {{ $hasPermission ? 'active' : '' }}">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" 
                                                                       name="permissions[]"
                                                                       value="{{ $permissionName }}"
                                                                       id="{{ $permissionName }}"
                                                                       {{ $hasPermission ? 'checked' : '' }}>
                                                                <label class="form-check-label text-capitalize w-100" 
                                                                       for="{{ $permissionName }}">
                                                                    {{ $permissionName }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Custom Permissions -->
                                        @php
                                            $customPermissions = $permissions->filter(function($permission) use ($module) {
                                                $action = explode('-', $permission->name)[0];
                                                return str_contains($permission->name, "-{$module->slug}") && 
                                                       !in_array($action, ['view', 'create', 'read', 'update', 'delete']);
                                            });
                                        @endphp

                                        @if($customPermissions->count() > 0)
                                        <div class="permission-section">
                                            <h6 class="text-primary mb-3">
                                                <i class="fas fa-key me-2"></i>Custom Permissions
                                            </h6>
                                            <div class="row g-3">
                                                @foreach($customPermissions as $permission)
                                                    <div class="col-md-4 col-lg-3">
                                                        <div class="permission-card {{ $rolePermissions->contains('name', $permission->name) ? 'active' : '' }}">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" 
                                                                       name="permissions[]"
                                                                       value="{{ $permission->name }}"
                                                                       id="{{ $permission->name }}"
                                                                       {{ $rolePermissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                                                <label class="form-check-label text-capitalize w-100" 
                                                                       for="{{ $permission->name }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Footer Actions -->
                        <div class="p-3 bg-light border-top">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Permissions
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: var(--bs-primary);
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}

.permission-card {
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.permission-card:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.permission-card.active {
    background-color: #f8f9fa;
    border-color: var(--bs-primary);
}

.form-check-input:checked ~ .form-check-label {
    color: var(--bs-primary);
    font-weight: 500;
}

.permission-section {
    position: relative;
}

.permission-section:not(:last-child)::after {
    content: '';
    position: absolute;
    bottom: -2rem;
    left: 0;
    right: 0;
    height: 1px;
    background: #e9ecef;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: var(--bs-primary);
}

.card {
    border-radius: 0.75rem;
}

@media (max-width: 768px) {
    .accordion-button {
        padding: 1rem;
    }
    
    .accordion-body {
        padding: 1rem;
    }
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-outline-secondary:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Module-specific select/deselect handlers
    document.querySelectorAll('.select-module').forEach(btn => {
        btn.addEventListener('click', function() {
            const moduleId = this.dataset.module;
            const moduleCheckboxes = document.querySelectorAll(`#module${moduleId} input[type="checkbox"]`);
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
                checkbox.closest('.permission-card').classList.add('active');
            });
        });
    });

    document.querySelectorAll('.deselect-module').forEach(btn => {
        btn.addEventListener('click', function() {
            const moduleId = this.dataset.module;
            const moduleCheckboxes = document.querySelectorAll(`#module${moduleId} input[type="checkbox"]`);
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.closest('.permission-card').classList.remove('active');
            });
        });
    });

    // Individual checkbox handler
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.permission-card');
            if (this.checked) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });
    });
});
</script>
@endpush
@endsection
