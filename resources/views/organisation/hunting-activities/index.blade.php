@extends('layouts.organisation')

@section('title', 'Hunting Activities')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_info {
            padding-top: 15px;
        }
        .card-header {
            background-color: #2e7d32;
            color: white;
        }
        .dt-buttons {
            margin-bottom: 15px;
        }
        .dt-button {
            background-color: #f8f9fa !important;
            border-color: #ddd !important;
        }
        .dt-button:hover {
            background-color: #e9ecef !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mt-4">Hunting Activities</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Hunting Activities</li>
            </ol>
        </div>
        <div>
            <button class="btn btn-outline-success me-2" onclick="window.location.reload();">
                <i class="fas fa-sync me-1"></i> Refresh
            </button>
            <a href="{{ route('organisation.hunting-activities.create', $organisation->slug ?? '') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Add New Activity
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <small>{{ session('success') }}</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas fa-hiking me-1"></i>
            Hunting Activities List
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="huntingActivitiesTable" class="table table-striped table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Concession</th>
                            <th>Safari Operator</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Species Count</th>
                            <th>Total Off-take</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td>{{ $activity->huntingConcession->name }}</td>
                                <td>{{ $activity->safariOperator->name ?? 'Not assigned' }}</td>
                                <td>{{ $activity->start_date->format('Y-m-d') }}</td>
                                <td>{{ $activity->end_date?->format('Y-m-d') ?? 'Ongoing' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">{{ $activity->species->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success rounded-pill">{{ $activity->species->sum('pivot.off_take') }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('organisation.hunting-activities.show', [$organisation->slug ?? '', $activity]) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('organisation.hunting-activities.edit', [$organisation->slug ?? '', $activity]) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('organisation.hunting-activities.destroy', [$organisation->slug ?? '', $activity]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this activity?')" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hunting activities found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#huntingActivitiesTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[ 2, "desc" ]], // Sort by start date (column 2) in descending order
                pageLength: 10,
                language: {
                    search: "Search activities:",
                    lengthMenu: "Show _MENU_ entries per page",
                    zeroRecords: "No matching activities found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)"
                },
                columnDefs: [
                    { orderable: false, targets: 6 } // Disable sorting on the actions column
                ]
            });
        });
    </script>
@endpush
@endsection 