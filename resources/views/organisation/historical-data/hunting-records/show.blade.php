@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - Hunting Record Details
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0">
                    <i class="fas fa-paw me-2 text-primary"></i>Hunting Record Details
                </h5>
                <p class="mb-0 text-muted small">Viewing historical hunting data for {{ $huntingRecord->year }}</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('organisation.historical-data.hunting-records.edit', [$organisation, $huntingRecord]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i>Edit Record
                    </a>
                    <a href="{{ route('organisation.historical-data.hunting-records.index', $organisation) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-1"></i>Basic Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Year</div>
                            <div class="fs-5 fw-medium">
                                <span class="badge bg-secondary">
                                    <i class="fas fa-calendar-alt me-1"></i>{{ $huntingRecord->year }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Species</div>
                            <div class="fs-5 fw-medium">{{ $huntingRecord->species->name }}</div>
                            <div class="text-muted small fst-italic">{{ $huntingRecord->species->scientific_name }}</div>
                        </div>
                        <div class="mb-0">
                            <div class="text-muted small">Record Created</div>
                            <div>{{ $huntingRecord->created_at->format('F d, Y') }}</div>
                            <div class="text-muted small">Last Updated: {{ $huntingRecord->updated_at->format('F d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar me-1"></i>Hunting Statistics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card bg-light h-100">
                                    <div class="card-body text-center">
                                        <div class="text-muted small mb-2">Quota Allocated</div>
                                        <div class="fs-1 fw-bold text-primary">{{ $huntingRecord->quota }}</div>
                                        <div class="text-muted small">animals</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card {{ $huntingRecord->hunted <= $huntingRecord->quota ? 'bg-success-subtle' : 'bg-danger-subtle' }} h-100">
                                    <div class="card-body text-center">
                                        <div class="text-muted small mb-2">Animals Hunted</div>
                                        <div class="fs-1 fw-bold {{ $huntingRecord->hunted <= $huntingRecord->quota ? 'text-success' : 'text-danger' }}">
                                            {{ $huntingRecord->hunted }}
                                        </div>
                                        <div class="text-muted small">
                                            @if($huntingRecord->quota > 0)
                                                {{ round(($huntingRecord->hunted / $huntingRecord->quota) * 100) }}% of quota
                                            @else
                                                No quota set
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light h-100">
                                    <div class="card-body text-center">
                                        <div class="text-muted small mb-2">Revenue Generated</div>
                                        <div class="fs-1 fw-bold text-success">${{ number_format($huntingRecord->revenue, 0) }}</div>
                                        <div class="text-muted small">USD</div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($huntingRecord->notes)
                                <div class="col-12 mt-2">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="mb-2">
                                                <i class="fas fa-sticky-note me-1 text-primary"></i>Notes
                                            </h6>
                                            <p class="mb-0">{{ $huntingRecord->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('organisation.historical-data.hunting-records.index', $organisation) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
            <div>
                <a href="{{ route('organisation.historical-data.hunting-records.edit', [$organisation, $huntingRecord]) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Edit Record
                </a>
                <form action="{{ route('organisation.historical-data.hunting-records.destroy', [$organisation, $huntingRecord]) }}" 
                      method="POST" 
                      class="d-inline ms-2"
                      onsubmit="return confirm('Are you sure you want to delete this record? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i>Delete Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 