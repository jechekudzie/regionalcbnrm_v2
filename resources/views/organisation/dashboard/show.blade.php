@extends('layouts.organisation')

@section('title')
    Regional CBNRM - {{ $organisation->name }} Dashboard
@endsection

@push('head')
    <!-- Add jQuery (if not already in the layout) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <!-- Add DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">{{ $organisation->name }} Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    
    <!-- Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $huntingActivities->count() }}</h5>
                            <div>Hunting Activities</div>
                        </div>
                        <div>
                            <i class="fas fa-crosshairs fa-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('organisation.hunting-activities.index', $organisation) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $wildlifeConflicts->count() }}</h5>
                            <div>Wildlife Conflicts</div>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('organisation.wildlife-conflicts.index', $organisation) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $problemAnimalControls->count() }}</h5>
                            <div>Problem Animal Controls</div>
                        </div>
                        <div>
                            <i class="fas fa-shield-alt fa-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('organisation.problem-animal-controls.index', $organisation) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $poachingIncidents->count() }}</h5>
                            <div>Poaching Incidents</div>
                        </div>
                        <div>
                            <i class="fas fa-skull-crossbones fa-3x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('organisation.poaching-incidents.index', $organisation) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity Charts -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Monthly Activities ({{ date('Y') }})
                </div>
                <div class="card-body">
                    <canvas id="monthlyActivitiesChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Quota Utilization vs. Allocation
                </div>
                <div class="card-body">
                    <canvas id="quotaUtilizationChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wildlife Conflicts and Problem Animal Control -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Wildlife Conflicts by Type
                </div>
                <div class="card-body">
                    <canvas id="conflictsChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Wildlife Conflicts by Species
                </div>
                <div class="card-body">
                    <canvas id="conflictSpeciesChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hunting Concessions and Quota Allocations -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-map-marked-alt me-1"></i>
                    Hunting Concessions
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="huntingConcessionsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Area (Ha)</th>
                                    <th>District</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($huntingConcessions as $concession)
                                <tr>
                                    <td>{{ $concession->name }}</td>
                                    <td>{{ number_format($concession->area) }}</td>
                                    <td>{{ $concession->district }}</td>
                                    <td>
                                        @php
                                            $today = \Carbon\Carbon::today();
                                            $status = 'Inactive';
                                            $statusClass = 'text-danger';
                                            if ($concession->start_date <= $today && $concession->end_date >= $today) {
                                                $status = 'Active';
                                                $statusClass = 'text-success';
                                            } elseif ($concession->start_date > $today) {
                                                $status = 'Upcoming';
                                                $statusClass = 'text-warning';
                                            }
                                        @endphp
                                        <span class="{{ $statusClass }}">{{ $status }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list-alt me-1"></i>
                    Quota Allocations ({{ date('Y') }})
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="quotaAllocationsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Species</th>
                                    <th>Hunting Quota</th>
                                    <th>Rational Killing</th>
                                    <th>Utilization</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotaAllocations as $quota)
                                <tr>
                                    <td>{{ $quota->species->name }}</td>
                                    <td>{{ $quota->hunting_quota }}</td>
                                    <td>{{ $quota->rational_killing_quota }}</td>
                                    <td>
                                        @php
                                            $utilized = $quotaUtilization[$quota->species_id]['utilized'] ?? 0;
                                            $percentage = $quota->hunting_quota > 0 ? round(($utilized / $quota->hunting_quota) * 100) : 0;
                                            $progressClass = 'bg-success';
                                            if ($percentage > 90) {
                                                $progressClass = 'bg-danger';
                                            } elseif ($percentage > 70) {
                                                $progressClass = 'bg-warning';
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">{{ $utilized }}/{{ $quota->hunting_quota }}</div>
                                            <div class="progress flex-grow-1" style="height: 10px;">
                                                <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
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
    </div>
    
    <!-- Recent Activities -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Recent Activities
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="recentActivitiesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $activity)
                                <tr>
                                    <td>
                                        @if($activity['type'] == 'Hunting Activity')
                                            <span class="badge bg-primary">Hunting</span>
                                        @elseif($activity['type'] == 'Wildlife Conflict')
                                            <span class="badge bg-warning">Conflict</span>
                                        @elseif($activity['type'] == 'Poaching Incident')
                                            <span class="badge bg-danger">Poaching</span>
                                        @endif
                                    </td>
                                    <td>{{ $activity['title'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($activity['date'])->format('d M Y') }}</td>
                                    <td>{{ $activity['details'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Debug Information -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-bug me-1"></i>
                    Debug Information
                </div>
                <div class="card-body">
                    <h5>Check Canvas Elements:</h5>
                    <div id="debug-info"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
function loadChartsWhenReady() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded yet, retrying in 100ms');
        setTimeout(loadChartsWhenReady, 100);
        return;
    }
    
    console.log('Chart.js loaded, initializing charts');
    
    // Debug canvas elements
    const debugInfo = document.getElementById('debug-info');
    const canvases = document.querySelectorAll('canvas');
    
    debugInfo.innerHTML += '<p>Number of canvas elements found: ' + canvases.length + '</p>';
    
    canvases.forEach((canvas, index) => {
        const rect = canvas.getBoundingClientRect();
        debugInfo.innerHTML += `
            <p>Canvas #${index + 1}: ${canvas.id}</p>
            <ul>
                <li>Width: ${rect.width}px</li>
                <li>Height: ${rect.height}px</li>
                <li>Visible: ${rect.width > 0 && rect.height > 0 ? 'Yes' : 'No'}</li>
                <li>Style: ${window.getComputedStyle(canvas).display}</li>
            </ul>
        `;
    });
    
    // Monthly Activities Chart
    try {
        var monthlyCtx = document.getElementById('monthlyActivitiesChart').getContext('2d');
        var monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'Hunting Activities',
                        data: @json(array_values($monthlyHuntingData)),
                        borderColor: 'rgba(0, 123, 255, 1)',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Wildlife Conflicts',
                        data: @json(array_values($monthlyConflictData)),
                        borderColor: 'rgba(255, 193, 7, 1)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Poaching Incidents',
                        data: @json(array_values($monthlyPoachingData)),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Activities'
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        console.log('Monthly activities chart created');
    } catch (e) {
        console.error('Error creating monthly activities chart:', e);
    }

    // Quota Utilization Chart
    var quotaData = @json(array_values($quotaUtilization));
    var quotaCtx = document.getElementById('quotaUtilizationChart').getContext('2d');
    var quotaChart = new Chart(quotaCtx, {
        type: 'bar',
        data: {
            labels: quotaData.map(item => item.species),
            datasets: [
                {
                    label: 'Allocated',
                    data: quotaData.map(item => item.allocated),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderWidth: 0
                },
                {
                    label: 'Utilized',
                    data: quotaData.map(item => item.utilized),
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderWidth: 0
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Animals'
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Conflicts by Type Chart
    var conflictTypesData = @json($conflictTypesData);
    var conflictsCtx = document.getElementById('conflictsChart').getContext('2d');
    var conflictsChart = new Chart(conflictsCtx, {
        type: 'pie',
        data: {
            labels: conflictTypesData.map(item => item.type),
            datasets: [{
                data: conflictTypesData.map(item => item.count),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.raw || 0;
                            var total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            var percentage = Math.round((value / total) * 100);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Conflicts by Species Chart
    var conflictsBySpeciesData = @json(array_values($conflictsBySpecies));
    var conflictSpeciesCtx = document.getElementById('conflictSpeciesChart').getContext('2d');
    var conflictSpeciesChart = new Chart(conflictSpeciesCtx, {
        type: 'doughnut',
        data: {
            labels: conflictsBySpeciesData.map(item => item.species),
            datasets: [{
                data: conflictsBySpeciesData.map(item => item.count),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(201, 203, 207, 0.7)',
                    'rgba(255, 99, 132, 0.4)',
                    'rgba(54, 162, 235, 0.4)',
                    'rgba(255, 206, 86, 0.4)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.raw || 0;
                            var total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            var percentage = Math.round((value / total) * 100);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, loading charts');
    loadChartsWhenReady();
    
    // Initialize DataTables
    $('#huntingConcessionsTable').DataTable({
        responsive: true,
        order: [[3, 'desc']],
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        language: {
            search: '',
            searchPlaceholder: 'Search concessions...'
        }
    });
    
    $('#recentActivitiesTable').DataTable({
        responsive: true,
        order: [[2, 'desc']],
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        language: {
            search: '',
            searchPlaceholder: 'Search activities...'
        }
    });
});
</script>
@endpush