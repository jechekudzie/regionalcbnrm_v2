@extends('layouts.organisation')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('title')
    Regional CBNRM - {{ $organisation->name }} - Human Resource Record Details
@endsection

@section('content')
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-users me-1"></i>
                    Human Resource Record Details - {{ $humanResourceRecord->period }}
                </div>
                <div>
                    <a href="{{ route('human-resource-records.index', $organisation) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Basic Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Year</th>
                                <td>{{ $humanResourceRecord->period }}</td>
                            </tr>
                            <tr>
                                <th>Employed By</th>
                                <td>
                                    @if($humanResourceRecord->employed_by == 'community')
                                        Community
                                    @else
                                        {{ $organisation->name }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td>{{ $humanResourceRecord->notes ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Staff Summary</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Wildlife Managers</th>
                                <td>{{ $humanResourceRecord->wildlife_managers }}</td>
                            </tr>
                            <tr>
                                <th>Game Scouts</th>
                                <td>{{ $humanResourceRecord->game_scouts }}</td>
                            </tr>
                            <tr>
                                <th>Rangers</th>
                                <td>{{ $humanResourceRecord->rangers }}</td>
                            </tr>
                            <tr>
                                <th>Total Staff</th>
                                <td>{{ $humanResourceRecord->total_staff }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>Staff Distribution</h5>
                        <div class="chart-container" style="position: relative; height:50vh; width:100%">
                            <canvas id="staffDistributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('human-resource-records.edit', [$organisation, $humanResourceRecord]) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('human-resource-records.destroy', [$organisation, $humanResourceRecord]) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Staff Distribution Chart
        const staffCtx = document.getElementById('staffDistributionChart').getContext('2d');
        const staffData = {
            labels: ['Wildlife Managers', 'Game Scouts', 'Rangers'],
            datasets: [{
                label: 'Number of Staff',
                data: [
                    {{ $humanResourceRecord->wildlife_managers }},
                    {{ $humanResourceRecord->game_scouts }},
                    {{ $humanResourceRecord->rangers }}
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        };
        
        const staffChart = new Chart(staffCtx, {
            type: 'pie',
            data: staffData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Staff Distribution by Type'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Delete confirmation
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection 