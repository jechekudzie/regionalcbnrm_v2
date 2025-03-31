@extends('layouts.organisation')

@section('title', 'Hunting Activity Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-nature-green shadow-sm" style="background-color: #2e7d32 !important;">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-0 text-white" style="color: white !important;">Hunting Activity Details</h3>
                            <p class="mb-0 opacity-75 text-white" style="color: white !important;">
                                <i class="fas fa-calendar me-1"></i> {{ $huntingActivity->start_date->format('d M') }} - {{ $huntingActivity->end_date->format('d M Y') }}
                                <span class="ms-3 badge bg-warning text-white">Period: {{ $huntingActivity->period }}</span>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('organisation.hunting-activities.edit', [$organisation->slug, $huntingActivity->id]) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Activity
                            </a>
                            <a href="{{ route('organisation.hunting-activities.index', $organisation->slug) }}" 
                               class="btn btn-outline-warning text-white" style="color: white !important;">
                                <i class="fas fa-arrow-left me-1 text-white" style="color: white !important;"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="row mb-4">
        <!-- Basic Information Card -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header py-3" style="background-color: #2e7d32 !important; color: white !important;">
                    <div class="d-flex align-items-center">
                        <span class="icon-circle me-3">
                            <i class="fas fa-info" style="color: white !important;"></i>
                        </span>
                        <h5 class="mb-0 fw-bold" style="color: white !important;">Basic Information</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Hunting Concession</div>
                        <div class="fw-medium">{{ $huntingActivity->huntingConcession->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Safari Operator</div>
                        <div class="fw-medium">{{ $huntingActivity->safariOperator->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Date Range</div>
                        <div class="fw-medium">{{ $huntingActivity->start_date->format('d M Y') }} - {{ $huntingActivity->end_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Duration</div>
                        <div class="fw-medium">{{ $huntingActivity->start_date->diffInDays($huntingActivity->end_date) + 1 }} days</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Hunter Licenses Card -->
        <div class="col-md-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header py-3" style="background-color: #2e7d32 !important; color: white !important;">
                    <div class="d-flex align-items-center">
                        <span class="icon-circle me-3">
                            <i class="fas fa-id-card" style="color: white !important;"></i>
                        </span>
                        <h5 class="mb-0 fw-bold" style="color: white !important;">Professional Hunter Licenses</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($huntingActivity->professionalHunterLicenses->count() > 0)
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            @foreach($huntingActivity->professionalHunterLicenses as $license)
                                <div class="col">
                                    <div class="card bg-warning h-100 border-0">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="icon-circle-sm bg-nature-green text-white me-2">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <h6 class="mb-0">{{ $license->hunter_name }}</h6>
                                            </div>
                                            <span class="badge bg-secondary text-white">
                                                License: {{ $license->license_number }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> No professional hunter licenses recorded.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Species and Quota Information -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header py-3" style="background-color: #2e7d32 !important; color: white !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="icon-circle me-3" style="background-color: white !important;">
                                <i class="fas fa-paw" style="color: #2e7d32 !important;"></i>
                            </span>
                            <h5 class="mb-0 fw-bold" style="color: white !important;">Species and Quota Information</h5>
                        </div>
                        <div class="bg-white px-3 py-1 rounded-pill" style="background-color: white !important;">
                            <span class="small me-2" style="color: #2e7d32 !important;">Total Off-take:</span>
                            <span class="fw-bold" style="color: #2e7d32 !important;">{{ $huntingActivity->species->sum(function($species) { return $species->pivot->off_take; }) }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($huntingActivity->species->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background-color: #2e7d32 !important;">
                                    <tr>
                                        <th class="ps-4" style="color: white !important;">Species</th>
                                        <th class="text-center" style="color: white !important;">Off-take</th>
                                        <th class="text-center" style="color: white !important;">Quota Status</th>
                                        <th class="text-end pe-4" style="color: white !important;">Quota Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($huntingActivity->species as $species)
                                        @php
                                            $quotaAllocation = \App\Models\Organisation\QuotaAllocation::where('organisation_id', $organisation->id)
                                                ->where('species_id', $species->id)
                                                ->where('period', $huntingActivity->period)
                                                ->first();
                                            
                                            $quotaBalance = $quotaAllocation ? \App\Models\Organisation\QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)
                                                ->first() : null;
                                                
                                            $badgeClass = $quotaBalance ? ($quotaBalance->remaining_quota >= 0 ? 'bg-success' : 'bg-danger') : 'bg-warning';
                                            $statusText = $quotaBalance ? ($quotaBalance->remaining_quota >= 0 ? 'Available' : 'Exceeded') : 'No Quota';
                                            $percentUsed = $quotaBalance && $quotaBalance->allocated_quota > 0 
                                                ? round(($quotaBalance->total_off_take / $quotaBalance->allocated_quota) * 100) 
                                                : 0;
                                            $progressClass = $percentUsed >= 90 ? 'danger' : ($percentUsed >= 70 ? 'warning' : 'success');
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="animal-icon me-2">
                                                        <i class="fas fa-paw text-success"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $species->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-dark fs-6 px-3">{{ $species->pivot->off_take }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $badgeClass }} px-3 py-2">{{ $statusText }}</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                @if($quotaBalance)
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <div class="text-end me-3">
                                                            <div class="small text-muted">Remaining / Allocated</div>
                                                            <div class="fw-medium">
                                                                <span class="{{ $quotaBalance->remaining_quota >= 0 ? 'text-success' : 'text-danger' }}">{{ $quotaBalance->remaining_quota }}</span> 
                                                                / {{ $quotaBalance->allocated_quota }}
                                                            </div>
                                                        </div>
                                                        <div style="width: 60px; height: 60px;" class="position-relative">
                                                            <div class="progress-circle progress-{{ $progressClass }}"
                                                                 style="--progress: {{ $percentUsed }}%"
                                                                 data-bs-toggle="tooltip"
                                                                 data-bs-placement="top"
                                                                 title="{{ $percentUsed }}% used">
                                                                <div class="progress-circle-inner">{{ $percentUsed }}%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-warning small">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> No quota allocated
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning m-3">
                            <i class="fas fa-exclamation-triangle me-2"></i> No species recorded.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --nature-green: #2e7d32;
    }
    
    .bg-nature-green {
        background-color: var(--nature-green) !important;
    }
    
    .text-nature-green {
        color: var(--nature-green) !important;
    }
    
    /* Make text white on green backgrounds */
    .bg-nature-green .text-white,
    .card-header.bg-nature-green h5,
    .card-header.bg-nature-green .icon-circle i,
    .card-header.bg-nature-green * {
        color: white !important;
    }
    
    /* Make green text on white backgrounds */
    .bg-white .text-nature-green,
    .icon-circle.bg-white i.text-nature-green {
        color: var(--nature-green) !important;
    }
    
    /* Ensure strong contrast for the table header */
    thead.bg-nature-green th {
        color: white !important;
        background-color: var(--nature-green) !important;
    }
    
    .icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-circle-sm {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    
    .animal-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: rgba(40, 167, 69, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .progress-circle {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: conic-gradient(var(--bs-primary) var(--progress), #e9ecef 0);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .progress-circle.progress-success {
        background: conic-gradient(var(--bs-success) var(--progress), #e9ecef 0);
    }
    
    .progress-circle.progress-warning {
        background: conic-gradient(var(--bs-warning) var(--progress), #e9ecef 0);
    }
    
    .progress-circle.progress-danger {
        background: conic-gradient(var(--bs-danger) var(--progress), #e9ecef 0);
    }
    
    .progress-circle-inner {
        width: 70%;
        height: 70%;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
@endsection 