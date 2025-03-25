@extends('layouts.organisation')

@section('title')
Regional CBNRM - {{ $organisation->name }} - View Income Distribution Record
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-1 text-primary"></i>Income Distribution Details
                </h5>
                <p class="mb-0 text-muted small">Viewing income distribution record for {{ $organisation->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('income_records.edit', [$organisation->slug, $incomeRecord->id]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>Edit Record
                </a>
                <a href="{{ route('income_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body bg-soft-primary rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">Year</h6>
                            <i class="fas fa-calendar-alt text-primary"></i>
                        </div>
                        <h3 class="card-text fw-bold">{{ $incomeRecord->period }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body bg-soft-success rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">RDC Share</h6>
                            <i class="fas fa-building text-success"></i>
                        </div>
                        <h3 class="card-text fw-bold">${{ number_format($incomeRecord->rdc_share, 2) }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body bg-soft-info rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">Community Share</h6>
                            <i class="fas fa-users text-info"></i>
                        </div>
                        <h3 class="card-text fw-bold">${{ number_format($incomeRecord->community_share, 2) }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body bg-soft-warning rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">CAMPFIRE Association Share</h6>
                            <i class="fas fa-fire text-warning"></i>
                        </div>
                        <h3 class="card-text fw-bold">${{ number_format($incomeRecord->ca_share, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body bg-soft-secondary rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">Total Income</h6>
                            <i class="fas fa-dollar-sign text-secondary"></i>
                        </div>
                        <h3 class="card-text fw-bold">${{ number_format($incomeRecord->rdc_share + $incomeRecord->community_share + $incomeRecord->ca_share, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Income Distribution Chart</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 400px;">
                            <canvas id="incomeDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the canvas element
        var ctx = document.getElementById('incomeDistributionChart').getContext('2d');
        
        // Define the data
        var chartData = {
            labels: ['RDC Share', 'Community Share', 'CAMPFIRE Association Share'],
            datasets: [{
                data: [
                    {{ $incomeRecord->rdc_share }}, 
                    {{ $incomeRecord->community_share }}, 
                    {{ $incomeRecord->ca_share }}
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',  // success
                    'rgba(23, 162, 184, 0.7)',  // info
                    'rgba(255, 193, 7, 0.7)'   // warning
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(255, 193, 7, 1)'
                ],
                borderWidth: 1
            }]
        };
        
        // Chart configuration
        var config = {
            type: 'pie',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                var total = 0;
                                
                                // Calculate total
                                for (var i = 0; i < context.dataset.data.length; i++) {
                                    total += context.dataset.data[i];
                                }
                                
                                var percentage = Math.round((value / total) * 100);
                                return label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        };
        
        // Create the chart
        new Chart(ctx, config);
    });
</script>
@endpush
@endsection 