@extends('layouts.organisation')

@section('title', 'Conflict Outcome Details')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <h1 class="h3 mb-2">Conflict Outcome Details</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('organisation.wildlife-conflicts.index', $organisation->slug) }}">Wildlife Conflicts</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('organisation.wildlife-conflicts.show', [$organisation->slug, $wildlifeConflictIncident->id]) }}">{{ Str::limit($wildlifeConflictIncident->title, 30) }}</a></li>
                        <li class="breadcrumb-item active">{{ $outcome->conflictOutCome->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="mt-3 mt-md-0">
                <div class="btn-group">
                    <a href="{{ route('organisation.wildlife-conflicts.outcomes.edit', [$organisation->slug, $wildlifeConflictIncident->id, $outcome->id]) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit Outcome
                    </a>
                    <a href="{{ route('organisation.wildlife-conflicts.show', [$organisation->slug, $wildlifeConflictIncident->id]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Incident
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Outcome Details Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>
                        {{ $outcome->conflictOutCome->name }}
                    </h5>
                    <span class="badge bg-light text-success">ID: {{ $outcome->id }}</span>
                </div>
                
                @if($outcome->conflictOutCome->description)
                <div class="card-body bg-light border-bottom">
                    <p class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i> {{ $outcome->conflictOutCome->description }}</p>
                </div>
                @endif
                
                <div class="card-body">
                    @if($outcome->dynamicValues->count() > 0)
                        <div class="row g-4">
                            @foreach($outcome->dynamicValues as $value)
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 bg-light rounded">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-2 text-muted small">{{ $value->dynamicField->label }}</h6>
                                            @if($value->dynamicField->field_type == 'textarea')
                                                <div class="py-2 text-break" style="white-space: pre-wrap;">{{ $value->field_value }}</div>
                                            @else
                                                <p class="card-text fw-semibold">{{ $value->field_value }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fa-lg"></i>
                            <div>No additional details recorded for this outcome.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar with Meta Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Outcome Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Related Incident</label>
                        <p class="fw-semibold">{{ $wildlifeConflictIncident->title }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Outcome Type</label>
                        <p><span class="badge bg-success">{{ $outcome->conflictOutCome->name }}</span></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Created</label>
                        <p class="mb-0">{{ $outcome->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Last Updated</label>
                        <p class="mb-0">{{ $outcome->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Reference</label>
                        <p class="mb-0 d-flex align-items-center">
                            <span class="badge bg-secondary me-2">ID: {{ $outcome->id }}</span>
                            <span class="text-muted small">{{ $outcome->created_at->format('Y/m') }}/{{ str_pad($outcome->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </p>
                    </div>
                    <div>
                        <label class="text-muted small mb-1">Category</label>
                        <p class="mb-0 d-flex align-items-center">
                            <span class="badge bg-info me-2">Type: {{ $outcome->conflictOutCome->name }}</span>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-cog me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('organisation.wildlife-conflicts.outcomes.edit', [$organisation->slug, $wildlifeConflictIncident->id, $outcome->id]) }}" 
                            class="btn btn-warning w-100">
                            <i class="fas fa-edit me-1"></i> Edit Outcome
                        </a>
                        <button type="button" class="btn btn-outline-danger w-100" 
                                onclick="if(confirm('Are you sure you want to delete this outcome? This action cannot be undone.')) { document.getElementById('delete-outcome-form').submit(); }">
                            <i class="fas fa-trash me-1"></i> Delete Outcome
                        </button>
                        <a href="{{ route('organisation.wildlife-conflicts.show', [$organisation->slug, $wildlifeConflictIncident->id]) }}" 
                            class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-1"></i> Back to Incident
                        </a>
                    </div>
                    <form id="delete-outcome-form" action="{{ route('organisation.wildlife-conflicts.outcomes.destroy', [$organisation->slug, $wildlifeConflictIncident->id, $outcome->id]) }}" 
                          method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Modern styling for detail cards */
    .card {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card-header.bg-success {
        background: linear-gradient(45deg, #2E7D32, #43A047) !important;
    }
    
    .badge.bg-success {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,128,0,0.2);
    }
    
    .btn-warning {
        background: linear-gradient(45deg, #F57C00, #FF9800);
        border-color: #F57C00;
    }
    
    .btn-outline-danger:hover {
        background: linear-gradient(45deg, #D32F2F, #F44336);
        border-color: #D32F2F;
    }
    
    .text-success {
        color: #2E7D32 !important;
    }
    
    /* Field value cards styling */
    .col-md-6 .card {
        transition: all 0.2s ease;
    }
    
    .col-md-6 .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    /* Breadcrumb styling */
    .breadcrumb {
        background: transparent;
        padding: 0.5rem 0;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        font-size: 1.2rem;
        line-height: 1;
        color: #6c757d;
    }
    
    /* Action buttons animation */
    .btn {
        transition: all 0.2s;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
    }
    
    /* Fade-in animation for content */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .row.g-4 {
        animation: fadeIn 0.5s ease-out forwards;
    }
    
    .col-md-6 {
        opacity: 0;
        animation: fadeIn 0.5s ease-out forwards;
    }
    
    /* Individual animation delays for sequential effect */
    .col-md-6:nth-child(1) { animation-delay: 0.05s; }
    .col-md-6:nth-child(2) { animation-delay: 0.1s; }
    .col-md-6:nth-child(3) { animation-delay: 0.15s; }
    .col-md-6:nth-child(4) { animation-delay: 0.2s; }
    .col-md-6:nth-child(5) { animation-delay: 0.25s; }
    .col-md-6:nth-child(6) { animation-delay: 0.3s; }
    .col-md-6:nth-child(7) { animation-delay: 0.35s; }
    .col-md-6:nth-child(8) { animation-delay: 0.4s; }
    .col-md-6:nth-child(9) { animation-delay: 0.45s; }
    .col-md-6:nth-child(10) { animation-delay: 0.5s; }
    .col-md-6:nth-child(11) { animation-delay: 0.55s; }
    .col-md-6:nth-child(12) { animation-delay: 0.6s; }
    .col-md-6:nth-child(13) { animation-delay: 0.65s; }
    .col-md-6:nth-child(14) { animation-delay: 0.7s; }
    .col-md-6:nth-child(15) { animation-delay: 0.75s; }
    .col-md-6:nth-child(16) { animation-delay: 0.8s; }
    .col-md-6:nth-child(17) { animation-delay: 0.85s; }
    .col-md-6:nth-child(18) { animation-delay: 0.9s; }
    .col-md-6:nth-child(19) { animation-delay: 0.95s; }
    .col-md-6:nth-child(20) { animation-delay: 1s; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apply sequential fade-in animation for field cards
        const fieldCards = document.querySelectorAll('.col-md-6 .card');
        fieldCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 50));
        });
        
        // Enhance buttons with hover effects
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 0.25rem 0.5rem rgba(0, 0, 0, 0.15)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = '';
                this.style.boxShadow = '';
            });
        });
        
        // Add clipboard functionality for field values if needed
        const fieldValues = document.querySelectorAll('.card-text');
        fieldValues.forEach(value => {
            value.addEventListener('click', function() {
                const textToCopy = this.textContent;
                navigator.clipboard.writeText(textToCopy).then(() => {
                    // Create and show a temporary tooltip
                    const tooltip = document.createElement('div');
                    tooltip.textContent = 'Copied!';
                    tooltip.style.position = 'absolute';
                    tooltip.style.background = 'rgba(0,0,0,0.7)';
                    tooltip.style.color = 'white';
                    tooltip.style.padding = '5px 10px';
                    tooltip.style.borderRadius = '4px';
                    tooltip.style.fontSize = '12px';
                    tooltip.style.zIndex = '1000';
                    tooltip.style.transition = 'opacity 0.5s';
                    
                    // Position the tooltip near the clicked text
                    const rect = this.getBoundingClientRect();
                    tooltip.style.top = `${rect.top - 30}px`;
                    tooltip.style.left = `${rect.left + rect.width / 2 - 25}px`;
                    
                    // Add to DOM, then fade out and remove
                    document.body.appendChild(tooltip);
                    setTimeout(() => {
                        tooltip.style.opacity = '0';
                        setTimeout(() => {
                            document.body.removeChild(tooltip);
                        }, 500);
                    }, 1500);
                });
            });
            
            // Add cursor pointer to indicate it's clickable
            value.style.cursor = 'pointer';
            
            // Add a subtle hint about copyability on hover
            value.addEventListener('mouseenter', function() {
                this.setAttribute('title', 'Click to copy');
                this.style.background = 'rgba(0,0,0,0.05)';
                this.style.borderRadius = '4px';
                this.style.padding = '2px 4px';
                this.style.margin = '-2px -4px';
            });
            
            value.addEventListener('mouseleave', function() {
                this.style.background = '';
                this.style.padding = '';
                this.style.margin = '';
            });
        });
    });
</script>
@endpush
@endsection 