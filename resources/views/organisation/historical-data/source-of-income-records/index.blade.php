@extends('layouts.organisation')

@push('head')
<!-- Datatable CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" />
@endpush

@section('title')
Regional CBNRM - {{ $organisation->name }} - Source of Income Records
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-1 text-primary"></i>Source of Income Records
                </h5>
                <p class="mb-0 text-muted small">Manage income sources for {{ $organisation->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('organisation.dashboard', $organisation->slug) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
                <a href="{{ route('source_of_income_records.create', $organisation->slug) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i>Add Source of Income Record
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <i class="fas fa-check-circle me-2 mt-1"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="sourceOfIncomeRecordsTable">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" style="width: 60px">#</th>
                        <th scope="col">Year</th>
                        <th scope="col">Trophy Fee</th>
                        <th scope="col">Hides</th>
                        <th scope="col">Meat</th>
                        <th scope="col">Hunting Concession</th>
                        <th scope="col">Photographic</th>
                        <th scope="col">Other</th>
                        <th scope="col">Total</th>
                        <th scope="col" class="text-end" style="width: 120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sourceOfIncomeRecords as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-calendar-alt me-1"></i>{{ $record->period }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">${{ number_format($record->trophy_fee_amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">${{ number_format($record->hides_amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">${{ number_format($record->meat_amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">${{ number_format($record->hunting_concession_fee_amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">${{ number_format($record->photographic_fee_amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-dark">${{ number_format($record->other_amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">${{ number_format($record->total_amount, 2) }}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('source_of_income_records.show', [$organisation->slug, $record->id]) }}"
                                        class="btn btn-sm btn-outline-info"
                                        title="View Record">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('source_of_income_records.edit', [$organisation->slug, $record->id]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit Record">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('source_of_income_records.destroy', [$organisation->slug, $record->id]) }}"
                                        method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete Record"
                                            onclick="return confirm('Are you sure you want to delete this source of income record?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted mb-0">No Source of Income Records Found</h5>
                                    <p class="text-muted mb-3">Get started by adding your first source of income record</p>
                                    <a href="{{ route('source_of_income_records.create', $organisation->slug) }}" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-1"></i>Add Source of Income Record
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table > :not(caption) > * > * {
        padding: 1rem 1rem;
    }
    .btn-outline-primary:hover i, 
    .btn-outline-danger:hover i,
    .btn-outline-info:hover i {
        color: white;
    }
    .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        float: right;
    }
    .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        outline: none;
    }
    .dataTables_filter input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sourceOfIncomeRecordsTable').DataTable({
            responsive: true,
            autoWidth: false,
            order: [
                [1, 'desc'] // Sort by year (descending)
            ],
            columnDefs: [
                { orderable: false, targets: 9 } // Disable sorting on actions column
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records...",
                lengthMenu: "Show _MENU_ records",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    });
</script>
@endpush
@endsection 