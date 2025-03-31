@extends('layouts.organisation')

@section('title', 'Quota Allocations')

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
            <h1 class="mt-4">Quota Allocations</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Quota Allocations</li>
            </ol>
        </div>
        <div>
            <button class="btn btn-outline-success me-2" onclick="window.location.reload();">
                <i class="fas fa-sync me-1"></i> Refresh
            </button>
            <a href="{{ route('organisation.quota-allocations.create', $organisation->slug) }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Add New Quota
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
            <i class="fas fa-chart-pie me-1"></i>
            Quota Allocations List
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="quotaAllocationsTable" class="table table-striped table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Period</th>
                            <th>Species</th>
                            <th>Hunting Quota</th>
                            <th>Rational Killing Quota</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotaAllocations as $quota)
                        <tr>
                            <td><span class="badge bg-warning text-dark">{{ $quota->period }}</span></td>
                            <td>{{ $quota->species->name }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill">{{ $quota->hunting_quota }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success rounded-pill">{{ $quota->rational_killing_quota }}</span>
                            </td>
                            <td>{{ $quota->start_date->format('Y-m-d') }}</td>
                            <td>{{ $quota->end_date->format('Y-m-d') }}</td>
                            <td>
                                @if($quota->notes)
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $quota->notes }}">
                                        {{ $quota->notes }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('organisation.quota-allocations.edit', [$organisation->slug, $quota->id]) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('organisation.quota-allocations.destroy', [$organisation->slug, $quota->id]) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this quota allocation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
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
    $('#quotaAllocationsTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [[4, 'desc']], // Sort by start date by default
        pageLength: 10,
        language: {
            search: "Search quotas:",
            lengthMenu: "Show _MENU_ entries per page",
            zeroRecords: "No matching quotas found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)"
        },
        columnDefs: [
            { orderable: false, targets: 7 } // Disable sorting on the actions column
        ]
    });
});
</script>
@endpush
