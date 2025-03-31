@extends('layouts.backend')

@push('head')

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css"/>

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <h4 class="text-primary mb-1">Organisation Structure</h4>
                        <p class="text-muted mb-0">Manage your organisational hierarchy</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary" id="expandAll">
                            <i class="fas fa-chevron-down me-1"></i> Expand All
                        </button>
                        <button class="btn btn-soft-primary" id="collapseAll">
                            <i class="fas fa-chevron-right me-1"></i> Collapse All
                        </button>
                        @if($rootType = \App\Models\Admin\OrganisationType::first())
                            <a class="btn btn-primary" href="{{ route('admin.organisations.create-root', ['type' => $rootType->id]) }}">
                                <i class="fa-solid fa-plus me-2"></i>Add {{ $rootType->name }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                @if($organisations->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-sitemap fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">No Organizations Yet</h5>
                        @if($rootType)
                            <p class="mb-4">Start by adding a {{ $rootType->name }}</p>
                            <a href="{{ route('admin.organisations.create-root', ['type' => $rootType->id]) }}" 
                               class="btn btn-primary">
                                <i class="fa-solid fa-plus me-2"></i>Add {{ $rootType->name }}
                            </a>
                        @else
                            <p class="mb-0">Please add organization types first</p>
                        @endif
                    </div>
                @else
                    <div class="org-tree">
                        @php
                            function renderTree($organisations, $parentId = null, $level = 0) {
                                // Get children and sort them based on their organization type's position in hierarchy
                                $children = $organisations->where('organisation_id', $parentId)
                                    ->sortBy(function($org) {
                                        return $org->organisationType->id;
                                    });

                                if ($children->isEmpty()) return;

                                foreach ($children as $org) {
                                    $hasChildren = $organisations->where('organisation_id', $org->id)->count() > 0;
                                    $levelClass = 'level-' . $level;
                                    
                                    echo '<div class="org-item ' . $levelClass . '">';
                                    echo '<div class="org-content">';
                                    
                                    // Left section with toggle and info
                                    echo '<div class="org-main">';
                                    if ($hasChildren) {
                                        echo '<button class="toggle-btn" data-bs-toggle="collapse" 
                                              data-bs-target="#org-' . $org->id . '">
                                              <i class="fas fa-caret-right"></i>
                                              </button>';
                                    }
                                    echo '<div class="org-info">';
                                    echo '<div class="org-title-section">';
                                    echo '<span class="org-type-badge">' . $org->organisationType->name . '</span>';
                                    echo '<h6 class="org-title mb-0">' . $org->name . '</h6>';
                                    echo '</div>';
                                    echo '<div class="org-meta">';
                                    if ($org->parentOrganisation) {
                                        echo '<span class="org-parent"><i class="fas fa-level-up-alt me-1"></i> Reports to: ' . $org->parentOrganisation->name . '</span>';
                                    }
                                    if ($org->description) {
                                        echo '<span class="org-description"><i class="fas fa-info-circle me-1"></i> ' . Str::limit($org->description, 100) . '</span>';
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    
                                    // Right section with stats and actions
                                    echo '<div class="org-actions">';
                                    echo '<div class="action-buttons d-flex gap-2">';

                                    // Stats dropdown
                                    echo '<div class="dropdown">
                                            <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-users"></i> Manage
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="' . route('admin.organisation-roles.index', $org->slug) . '">
                                                        <i class="fas fa-user-tag me-2"></i> Roles (' . $org->organisationRoles->count() . ')
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="' . route('admin.organisation-users.index', $org->slug) . '">
                                                        <i class="fas fa-users me-2"></i> Users (' . $org->users->count() . ')
                                                    </a>
                                                </li>
                                            </ul>
                                          </div>';

                                    // Actions dropdown
                                    echo '<div class="dropdown">
                                            <button class="btn btn-soft-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                            <ul class="dropdown-menu">';

                                    // For root level (no parent), use different route
                                    if ($parentId === null) {
                                        echo '<li>
                                                <a class="dropdown-item" href="' . route('admin.organisations.create-root', [
                                                    'type' => $org->organisation_type_id
                                                ]) . '">
                                                    <i class="fas fa-plus me-2"></i> Add ' . $org->organisationType->name . '
                                                </a>
                                              </li>';
                                    } else {
                                        echo '<li>
                                                <a class="dropdown-item" href="' . route('admin.organisations.create-child', [
                                                    'parent' => $org->organisation_id,
                                                    'type' => $org->organisation_type_id
                                                ]) . '">
                                                    <i class="fas fa-plus me-2"></i> Add ' . $org->organisationType->name . '
                                                </a>
                                              </li>';
                                    }

                                    // Add "Create Child" options for ALL possible child types
                                    $childTypes = $org->organisationType->children;
                                    if ($childTypes->isNotEmpty()) {
                                        foreach ($childTypes as $childType) {
                                            echo '<li>
                                                    <a class="dropdown-item" href="' . route('admin.organisations.create-child', [
                                                        'parent' => $org->id,
                                                        'type' => $childType->id
                                                    ]) . '">
                                                        <i class="fas fa-level-down-alt me-2"></i> Add ' . $childType->name . '
                                                    </a>
                                                  </li>';
                                        }
                                    }

                                    echo '</ul></div>';

                                    // Edit/Delete dropdown
                                    echo '<div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="' . route('admin.organisations.edit', $org->slug) . '">
                                                        <i class="fas fa-pencil me-2"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="' . route('admin.organisations.destroy', $org->slug) . '"
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm(\'Are you sure?\');">
                                                        ' . csrf_field() . method_field('DELETE') . '
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                          </div>';

                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    
                                    if ($hasChildren) {
                                        echo '<div class="collapse show children-container" id="org-' . $org->id . '">';
                                        renderTree($organisations, $org->id, $level + 1);
                                        echo '</div>';
                                    }
                                    
                                    echo '</div>';
                                }
                            }
                        @endphp

                        @php
                            renderTree($organisations);
                        @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
    .org-tree {
        padding: 1rem;
        padding-bottom: 200px;
    }

    .org-item {
        margin-bottom: 0.5rem;
    }

    /* Updated indentation and styling */
    .level-0 { margin-left: 0; }
    .level-1 { margin-left: 2rem; }
    .level-2 { margin-left: 4rem; }
    .level-3 { margin-left: 6rem; }
    .level-4 { margin-left: 8rem; }
    .level-5 { margin-left: 10rem; }

    .org-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        background-color: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .org-content:hover {
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        border-color: #e0e0e0;
    }

    /* Updated left border colors for hierarchy */
    .level-0 > .org-content { border-left: 4px solid #4361ee; }
    .level-1 > .org-content { border-left: 4px solid #3498db; }
    .level-2 > .org-content { border-left: 4px solid #2ecc71; }
    .level-3 > .org-content { border-left: 4px solid #f1c40f; }
    .level-4 > .org-content { border-left: 4px solid #e67e22; }
    .level-5 > .org-content { border-left: 4px solid #e74c3c; }

    .children-container {
        position: relative;
    }

    /* Add connecting lines */
    .children-container::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 1rem;
        width: 2px;
        background: #f0f0f0;
    }

    .org-main {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .org-info {
        flex: 1;
        min-width: 300px;
        padding-right: 1rem;
    }

    .org-title-section {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .org-type-badge {
        background-color: rgba(67, 97, 238, 0.1);
        color: #4361ee;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .org-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .org-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .org-parent, .org-description {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .org-parent i {
        font-size: 0.875rem;
        color: #64748b;
    }

    .org-description {
        color: #64748b;
        font-style: italic;
    }

    .toggle-btn {
        background: none;
        border: none;
        padding: 0.5rem;
        cursor: pointer;
        color: #6c757d;
        transition: transform 0.3s ease;
    }

    .toggle-btn i {
        transition: transform 0.3s ease;
    }

    .toggle-btn:not(.collapsed) i {
        transform: rotate(90deg);
    }

    .toggle-btn:hover {
        color: #111827;
        background-color: #f3f4f6;
        border-radius: 4px;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .org-title-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .org-meta {
            flex-direction: column;
            gap: 0.5rem;
        }

        .org-type-badge {
            align-self: flex-start;
        }
    }

    .dropdown-menu {
        min-width: 200px;
        padding: 0.5rem 0;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item.text-danger:hover {
        background-color: #fee2e2;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
    }

    .action-buttons .dropdown:not(:last-child) {
        margin-right: 0.25rem;
    }

    form .dropdown-item {
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    /* Add these new rules to handle dropdown positioning */
    .org-actions .dropdown {
        position: static;
    }

    /* Add specific positioning for the Add dropdown */
    .org-actions .dropdown:first-child .dropdown-menu {
        left: 0;
        right: auto;
    }

    /* Keep the ellipsis menu dropdown right-aligned */
    .org-actions .dropdown:last-child .dropdown-menu {
        left: auto;
        right: 0;
    }

    /* Add a data attribute to handle upward dropdowns */
    .org-actions .dropdown-menu[data-bs-popper] {
        margin-top: 0;
    }

    /* When the dropdown opens upward */
    .org-actions .dropup .dropdown-menu {
        bottom: 100%;
        top: auto !important;
        transform-origin: bottom;
    }
    </style>

@endsection

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
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle button rotation
            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', function() {
                    this.classList.toggle('collapsed');
                });
            });

            // Expand/Collapse All functionality
            document.getElementById('expandAll').addEventListener('click', function() {
                document.querySelectorAll('.collapse').forEach(el => el.classList.add('show'));
                document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('collapsed'));
            });

            document.getElementById('collapseAll').addEventListener('click', function() {
                document.querySelectorAll('.collapse').forEach(el => el.classList.remove('show'));
                document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.add('collapsed'));
            });

            // Add this new code for dropdown positioning
            const dropdowns = document.querySelectorAll('.org-actions .dropdown');
            
            dropdowns.forEach(dropdown => {
                const button = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                button.addEventListener('click', () => {
                    const rect = dropdown.getBoundingClientRect();
                    const spaceBelow = window.innerHeight - rect.bottom;
                    const spaceAbove = rect.top;
                    const menuHeight = menu.offsetHeight;

                    if (spaceBelow < menuHeight && spaceAbove > menuHeight) {
                        dropdown.classList.add('dropup');
                    } else {
                        dropdown.classList.remove('dropup');
                    }
                });
            });
        });
    </script>

@endpush
