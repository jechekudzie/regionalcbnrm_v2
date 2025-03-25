@extends('layouts.organisation')

@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('title')
    Regional CBNRM - {{ $organisation->name }} - Human Resource Records
@endsection

@section('content')
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-users me-1"></i>
                    Human Resource Records
                </div>
                <div>
                    <a href="{{ route('organisations.historical-data.human-resource-records.create', $organisation) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New Record
                    </a>
                    <a href="{{ route('main-dashboard', $organisation) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="humanResourceRecordsTable">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Wildlife Managers</th>
                                <th>Game Scouts</th>
                                <th>Rangers</th>
                                <th>Total Staff</th>
                                <th>Employed By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($humanResourceRecords as $record)
                                <tr>
                                    <td>{{ $record->period }}</td>
                                    <td>{{ $record->wildlife_managers }}</td>
                                    <td>{{ $record->game_scouts }}</td>
                                    <td>{{ $record->rangers }}</td>
                                    <td>{{ $record->total_staff }}</td>
                                    <td>{{ $record->employed_by == 'community' ? 'Community' : $organisation->name }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('organisations.historical-data.human-resource-records.show', [$organisation, $record]) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('organisations.historical-data.human-resource-records.edit', [$organisation, $record]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('organisations.historical-data.human-resource-records.destroy', [$organisation, $record]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No human resource records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#humanResourceRecordsTable').DataTable({
                responsive: true,
                order: [[0, 'desc']], // Sort by year (first column) in descending order
                language: {
                    search: "Search records:",
                    lengthMenu: "Show _MENU_ records per page",
                    zeroRecords: "No matching records found",
                    info: "Showing _START_ to _END_ of _TOTAL_ records",
                    infoEmpty: "Showing 0 to 0 of 0 records",
                    infoFiltered: "(filtered from _MAX_ total records)"
                },
                columnDefs: [
                    { orderable: false, targets: 6 } // Disable sorting on the actions column
                ]
            });
        });
    </script>
@endsection 