@extends('layouts.organisation')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
@endsection

@section('title')
Regional CBNRM - {{ $organisation->name }} - Income Utilization Record Details
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-1 text-primary"></i>Income Utilization Record Details
                </h5>
                <p class="mb-0 text-muted small">Viewing income utilization record for {{ $organisation->name }} - {{ $incomeUseRecord->period }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('income_use_records.index', $organisation->slug) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
                <a href="{{ route('income_use_records.edit', [$organisation->slug, $incomeUseRecord->id]) }}" class="btn btn-primary">
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
                        <h6 class="mb-0">Income Utilization Details</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th class="bg-light" style="width: 40%">Year</th>
                                    <td>{{ $incomeUseRecord->period }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Administration</th>
                                    <td>${{ number_format($incomeUseRecord->administration_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Management Activities</th>
                                    <td>${{ number_format($incomeUseRecord->management_activities_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Social Services</th>
                                    <td>${{ number_format($incomeUseRecord->social_services_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Law Enforcement</th>
                                    <td>${{ number_format($incomeUseRecord->law_enforcement_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Other</th>
                                    <td>${{ number_format($incomeUseRecord->other_amount, 2) }}</td>
                                </tr>
                                @if($incomeUseRecord->other_description)
                                <tr>
                                    <th class="bg-light">Other Description</th>
                                    <td>{{ $incomeUseRecord->other_description }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th class="bg-light">Total Utilization</th>
                                    <td class="fw-bold">${{ number_format($incomeUseRecord->administration_amount + $incomeUseRecord->management_activities_amount + $incomeUseRecord->social_services_amount + $incomeUseRecord->law_enforcement_amount + $incomeUseRecord->other_amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Income Utilization Distribution</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div style="height: 300px; width: 100%;">
                            <canvas id="incomeUseChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Data -->
<script>
    var chartData = {
        admin: {{ $incomeUseRecord->administration_amount }},
        management: {{ $incomeUseRecord->management_activities_amount }},
        social: {{ $incomeUseRecord->social_services_amount }},
        law: {{ $incomeUseRecord->law_enforcement_amount }},
        other: {{ $incomeUseRecord->other_amount }}
    };
</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('incomeUseChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Administration', 'Management Activities', 'Social Services', 'Law Enforcement', 'Other'],
                datasets: [{
                    data: [
                        chartData.admin,
                        chartData.management,
                        chartData.social,
                        chartData.law,
                        chartData.other
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endpush