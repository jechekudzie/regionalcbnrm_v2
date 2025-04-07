@extends('layouts.organisation')

@section('title')
    Historical Dashboard - {{ $organisation->name }}
@endsection

@section('head')
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="content">
    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Historical Dashboard: {{ $organisation->name }}</h2>
            </div>
        </div>
        
        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard', $organisation) }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Current Dashboard</a></li>
                    <li class="breadcrumb-item active">Historical Dashboard (2019-2023)</li>
                </ol>
            </nav>
        </div>

        <!-- Year Range Selection -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Historical Data: 2019 - 2023</h5>
                <p class="card-text">This dashboard displays aggregated data from previous years.</p>
            </div>
        </div>

        <!-- Hunting Quota Allocation & Utilization -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Yearly Hunting Quota Allocation & Utilization</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <canvas id="huntingQuotaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Top Hunted Species (2019-2023)</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <canvas id="topHuntedSpeciesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Human Wildlife Conflict -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Yearly Human-Wildlife Conflicts</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <canvas id="yearlyConflictChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Top Conflict Species (2019-2023)</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <canvas id="topConflictSpeciesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Problem Animal Control & Poaching -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Yearly Problem Animal Control</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <canvas id="yearlyControlChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Yearly Poaching Incidents</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <canvas id="yearlyPoachingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Distribution -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Yearly Income Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <canvas id="yearlyIncomeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Sources of Income (2019-2023)</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <canvas id="incomeSourcesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart colors
        const colors = {
            blue: '#2c7be5',
            red: '#e63757',
            green: '#00d27a',
            cyan: '#27bcfd',
            yellow: '#f5803e',
            purple: '#6b5eae',
            gray: '#748194',
            grayLight: '#ededf0',
            grayLighter: '#f8f9fa'
        };
        
        // Year labels for charts
        const years = @json($years);
        
        // Hunting Quota Chart
        const huntingQuotaData = @json($yearlyQuotaData);
        const huntingQuotaCtx = document.getElementById('huntingQuotaChart').getContext('2d');
        new Chart(huntingQuotaCtx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Allocated',
                        data: years.map(year => huntingQuotaData[year].allocated),
                        backgroundColor: colors.blue,
                        borderColor: colors.blue,
                        borderWidth: 1
                    },
                    {
                        label: 'Utilized',
                        data: years.map(year => huntingQuotaData[year].utilised),
                        backgroundColor: colors.green,
                        borderColor: colors.green,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Animals'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
                        }
                    }
                }
            }
        });
        
        // Top Hunted Species Chart
        const topHuntingSpecies = @json($topHuntingSpecies);
        const topHuntingCtx = document.getElementById('topHuntedSpeciesChart').getContext('2d');
        new Chart(topHuntingCtx, {
            type: 'bar',
            data: {
                labels: topHuntingSpecies.map(item => item.species),
                datasets: [
                    {
                        label: 'Allocated',
                        data: topHuntingSpecies.map(item => item.allocated),
                        backgroundColor: colors.blue,
                        borderColor: colors.blue,
                        borderWidth: 1
                    },
                    {
                        label: 'Utilized',
                        data: topHuntingSpecies.map(item => item.utilised),
                        backgroundColor: colors.green,
                        borderColor: colors.green,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Animals'
                        }
                    }
                }
            }
        });
        
        // Yearly Conflict Chart
        const conflictData = @json($yearlyConflictData);
        const conflictCtx = document.getElementById('yearlyConflictChart').getContext('2d');
        new Chart(conflictCtx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Crop Damage',
                        data: years.map(year => conflictData[year].crop_damage),
                        backgroundColor: colors.green,
                        borderColor: colors.green,
                        borderWidth: 1
                    },
                    {
                        label: 'Human Injuries',
                        data: years.map(year => conflictData[year].human_injured),
                        backgroundColor: colors.yellow,
                        borderColor: colors.yellow,
                        borderWidth: 1
                    },
                    {
                        label: 'Human Deaths',
                        data: years.map(year => conflictData[year].human_death),
                        backgroundColor: colors.red,
                        borderColor: colors.red,
                        borderWidth: 1
                    },
                    {
                        label: 'Livestock Impact',
                        data: years.map(year => conflictData[year].livestock_killed_injured),
                        backgroundColor: colors.purple,
                        borderColor: colors.purple,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Incidents'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
                        }
                    }
                }
            }
        });
        
        // Top Conflict Species Chart
        const topConflictSpecies = @json($topConflictSpecies);
        const topConflictCtx = document.getElementById('topConflictSpeciesChart').getContext('2d');
        new Chart(topConflictCtx, {
            type: 'bar',
            data: {
                labels: topConflictSpecies.map(item => item.species),
                datasets: [
                    {
                        label: 'Crop Damage',
                        data: topConflictSpecies.map(item => item.crop_damage),
                        backgroundColor: colors.green,
                        borderColor: colors.green,
                        borderWidth: 1
                    },
                    {
                        label: 'Human Impact',
                        data: topConflictSpecies.map(item => item.human_impact),
                        backgroundColor: colors.red,
                        borderColor: colors.red,
                        borderWidth: 1
                    },
                    {
                        label: 'Livestock Impact',
                        data: topConflictSpecies.map(item => item.livestock_impact),
                        backgroundColor: colors.purple,
                        borderColor: colors.purple,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Incidents'
                        }
                    }
                }
            }
        });
        
        // Yearly Control Chart
        const controlData = @json($yearlyControlData);
        const controlCtx = document.getElementById('yearlyControlChart').getContext('2d');
        new Chart(controlCtx, {
            type: 'line',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Animal Control Cases',
                        data: years.map(year => controlData[year]),
                        backgroundColor: 'rgba(39, 188, 253, 0.2)',
                        borderColor: colors.cyan,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Animals'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
                        }
                    }
                }
            }
        });
        
        // Yearly Poaching Chart
        const poachingData = @json($yearlyPoachingData);
        const poachingCtx = document.getElementById('yearlyPoachingChart').getContext('2d');
        new Chart(poachingCtx, {
            type: 'line',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Poaching Incidents',
                        data: years.map(year => poachingData[year]),
                        backgroundColor: 'rgba(230, 55, 87, 0.2)',
                        borderColor: colors.red,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Incidents'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
                        }
                    }
                }
            }
        });
        
        // Yearly Income Chart
        const incomeData = @json($yearlyIncomeData);
        const incomeCtx = document.getElementById('yearlyIncomeChart').getContext('2d');
        new Chart(incomeCtx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'RDC Share',
                        data: years.map(year => incomeData[year].rdc_share),
                        backgroundColor: colors.blue,
                        borderColor: colors.blue,
                        borderWidth: 1
                    },
                    {
                        label: 'Community Share',
                        data: years.map(year => incomeData[year].community_share),
                        backgroundColor: colors.green,
                        borderColor: colors.green,
                        borderWidth: 1
                    },
                    {
                        label: 'CA Share',
                        data: years.map(year => incomeData[year].ca_share),
                        backgroundColor: colors.purple,
                        borderColor: colors.purple,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
                        }
                    }
                }
            }
        });
        
        // Income Sources Chart
        const incomeSourcesData = @json($incomeSourcesData);
        const incomeSourcesCtx = document.getElementById('incomeSourcesChart').getContext('2d');
        new Chart(incomeSourcesCtx, {
            type: 'pie',
            data: {
                labels: [
                    'Safari Hunting', 
                    'Tourism', 
                    'Fishing', 
                    'Problem Animal Control',
                    'Carbon Credits',
                    'Other'
                ],
                datasets: [
                    {
                        data: [
                            incomeSourcesData.safari_hunting,
                            incomeSourcesData.tourism,
                            incomeSourcesData.fishing,
                            incomeSourcesData.problem_animal_control,
                            incomeSourcesData.carbon_credits,
                            incomeSourcesData.other
                        ],
                        backgroundColor: [
                            colors.blue,
                            colors.green,
                            colors.cyan,
                            colors.yellow,
                            colors.purple,
                            colors.gray
                        ],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    });
</script>
@endsection 