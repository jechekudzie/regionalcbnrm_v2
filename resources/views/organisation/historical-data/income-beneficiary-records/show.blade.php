@extends('layouts.organisation')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
@endsection

@section('title')
Regional CBNRM - {{ $organisation->name }} - Income Beneficiary Record Details
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-users me-1 text-primary"></i>Income Beneficiary Record Details
                </h5>
                <p class="mb-0 text-muted small">Viewing income beneficiary record for {{ $organisation->name }} - {{ $incomeBeneficiaryRecord->period }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('income_beneficiary_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
                <a href="{{ route('income_beneficiary_records.edit', [$organisation->slug, $incomeBeneficiaryRecord->id]) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Edit Record
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Beneficiary Details</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th class="bg-light" style="width: 40%">Year</th>
                                    <td>{{ $incomeBeneficiaryRecord->period }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Households</th>
                                    <td>{{ number_format($incomeBeneficiaryRecord->households) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Males</th>
                                    <td>{{ number_format($incomeBeneficiaryRecord->males) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Females</th>
                                    <td>{{ number_format($incomeBeneficiaryRecord->females) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Total Beneficiaries</th>
                                    <td class="fw-bold">{{ number_format($incomeBeneficiaryRecord->total_beneficiaries) }}</td>
                                </tr>
                                @if($incomeBeneficiaryRecord->notes)
                                <tr>
                                    <th class="bg-light">Notes</th>
                                    <td>{{ $incomeBeneficiaryRecord->notes }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Gender Distribution</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="height: 300px; width: 100%;">
                            <canvas id="genderDistributionChart" 
                                data-males="{{ $incomeBeneficiaryRecord->males }}"
                                data-females="{{ $incomeBeneficiaryRecord->females }}">
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the canvas element
        var ctx = document.getElementById('genderDistributionChart');
        
        // Get data from data attributes
        var males = parseFloat(ctx.getAttribute('data-males') || 0);
        var females = parseFloat(ctx.getAttribute('data-females') || 0);
        
        // Create the chart
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Males', 'Females'],
                datasets: [{
                    data: [males, females],
                    backgroundColor: [
                        '#4e73df', // Blue for males
                        '#e83e8c'  // Pink for females
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                var total = males + females;
                                var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush 