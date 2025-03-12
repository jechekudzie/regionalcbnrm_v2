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
                        <h4 class="text-primary mb-1">Organisation Type Structure</h4>
                        <p class="text-muted mb-0">Manage your organisation type hierarchy</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary" id="expandAll">
                            <i class="fas fa-chevron-down me-1"></i> Expand All
                        </button>
                        <button class="btn btn-soft-primary" id="collapseAll">
                            <i class="fas fa-chevron-right me-1"></i> Collapse All
                        </button>
                        <a class="btn btn-primary" href="{{route('admin.organisation-types.create')}}">
                            <i class="fa-solid fa-plus me-2"></i>Add Organisation Type
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="org-tree">
                    @php
                        function renderTypeTree($types, $parentId = null, $level = 0) {
                            if ($parentId === null) {
                                $currentTypes = $types->filter(function($type) {
                                    return !$type->parentOrganisationType();
                                });
                            } else {
                                $parent = $types->find($parentId);
                                $currentTypes = $parent ? $parent->children : collect();
                            }

                            foreach ($currentTypes as $type) {
                                $hasChildren = $type->children->isNotEmpty();
                                $levelClass = 'level-' . $level;
                                
                                echo '<div class="org-item ' . $levelClass . '">';
                                echo '<div class="org-content">';
                                
                                // Left section with toggle and name
                                echo '<div class="org-main">';
                                if ($hasChildren) {
                                    echo '<button class="toggle-btn collapsed" data-bs-toggle="collapse" 
                                          data-bs-target="#type-' . $type->id . '">
                                          <i class="fas fa-play"></i>
                                          </button>';
                                } else {
                                    echo '<div class="org-indicator">
                                          <i class="fas fa-play"></i>
                                          </div>';
                                }
                                echo '<div class="org-info">';
                                echo '<div class="org-title">' . $type->name . '</div>';
                                echo '<div class="org-meta">';
                                $parent = $type->parentOrganisationType();
                                if ($parent) {
                                    echo '<span class="org-parent">Parent Type: ' . $parent->name . '</span>';
                                }
                                if ($hasChildren) {
                                    echo '<span class="org-children">Child Types: ' . $type->children->count() . '</span>';
                                }
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';

                                // Right section with actions
                                echo '<div class="org-actions">';
                                echo '<div class="action-buttons d-flex gap-2">';

                                // Add dropdown
                                echo '<div class="dropdown">
                                        <button class="btn btn-soft-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                        <ul class="dropdown-menu">';

                                // Add Same Level Type
                                if ($parent) {
                                    echo '<li>
                                            <a class="dropdown-item" href="' . route('admin.organisation-types.create', [
                                                'parent' => $parent->id
                                            ]) . '">
                                                <i class="fas fa-plus me-2"></i> Add Same Level Type
                                            </a>
                                          </li>';
                                }

                                // Add Child Type
                                echo '<li>
                                        <a class="dropdown-item" href="' . route('admin.organisation-types.create', [
                                            'parent' => $type->id
                                        ]) . '">
                                            <i class="fas fa-level-down-alt me-2"></i> Add Child Type
                                        </a>
                                      </li>';
                                
                                echo '</ul></div>';

                                // Edit/Delete dropdown
                                echo '<div class="dropdown">
                                        <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="' . route('admin.organisation-types.edit', ['organisationType' => $type->slug]) . '">
                                                    <i class="fas fa-pencil me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="' . route('admin.organisation-types.destroy', ['organisationType' => $type->slug]) . '"
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm(\'Are you sure? This will affect all organizations of this type.\');">
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
                                    echo '<div class="collapse show children-container" id="type-' . $type->id . '">';
                                    renderTypeTree($types, $type->id, $level + 1);
                                    echo '</div>';
                                }
                                
                                echo '</div>';
                            }
                        }

                        renderTypeTree($organisationTypes);
                    @endphp
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Reset any potential conflicting styles */
    html, body {
        overflow-x: hidden;
        min-height: 100%;
        position: relative;
    }

    .container-fluid {
        padding-bottom: 2rem;
        width: 100%;
        position: relative;
    }

    .card {
        position: relative;
        z-index: 1;
        overflow: visible !important;
    }

    .card-body {
        position: relative;
        overflow: visible !important;
    }

    .org-tree {
        padding: 1rem;
        padding-bottom: 200px;
    }

    .org-item {
        margin-bottom: 0.5rem;
    }

    /* Level indentation */
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

    /* Hierarchy border colors */
    .level-0 > .org-content { border-left: 4px solid #4361ee; }
    .level-1 > .org-content { border-left: 4px solid #3498db; }
    .level-2 > .org-content { border-left: 4px solid #2ecc71; }
    .level-3 > .org-content { border-left: 4px solid #f1c40f; }
    .level-4 > .org-content { border-left: 4px solid #e67e22; }
    .level-5 > .org-content { border-left: 4px solid #e74c3c; }

    /* Connecting lines */
    .children-container {
        position: relative;
    }

    .children-container::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 1rem;
        width: 2px;
        background: #f0f0f0;
    }

    /* Main content area */
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

    /* Toggle button */
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

    /* Dropdown styling */
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

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
    }

    /* Dropdown positioning */
    .action-buttons .dropdown {
        position: relative;
    }

    .action-buttons .dropdown:first-child .dropdown-menu {
        position: absolute;
        left: auto;
        right: 100%;
        top: 0;
        margin-right: 0.5rem;
    }

    .action-buttons .dropdown:last-child .dropdown-menu {
        position: absolute;
        right: 0;
        left: auto;
        top: 100%;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .org-meta {
            flex-direction: column;
            gap: 0.5rem;
        }
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
        });
    </script>
@endpush 