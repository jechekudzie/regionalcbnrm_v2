@extends('layouts.organisation')

@section('title')
    Test Chart - {{ $organisation->name }} Dashboard
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Test Chart</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('organisation.dashboard.index', $organisation->slug) }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Test Chart</li>
    </ol>
    
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Simple Test Chart
                </div>
                <div class="card-body">
                    <canvas id="testChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-bug me-1"></i>
                    Debug Information
                </div>
                <div class="card-body">
                    <div id="debug-info"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('DOMContentLoaded fired');
        
        // Debug info
        const debugInfo = document.getElementById('debug-info');
        debugInfo.innerHTML = '<p>Testing chart rendering...</p>';
        
        // Log canvas element
        const canvas = document.getElementById('testChart');
        console.log('Canvas element:', canvas);
        debugInfo.innerHTML += '<p>Canvas found: ' + (canvas ? 'Yes' : 'No') + '</p>';
        
        if (!canvas) {
            console.error('Canvas element not found');
            return;
        }
        
        // Check if Chart is defined
        debugInfo.innerHTML += '<p>Chart.js loaded: ' + (typeof Chart !== 'undefined') + '</p>';
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded!');
            document.getElementById('testChart').insertAdjacentHTML('afterend', 
                '<div class="alert alert-danger">Chart.js library is not loaded!</div>');
            return;
        }
        
        console.log('Getting canvas context');
        const ctx = canvas.getContext('2d');
        console.log('Canvas context:', ctx);
        debugInfo.innerHTML += '<p>Canvas context created: ' + (ctx ? 'Yes' : 'No') + '</p>';
        
        // Create chart
        console.log('Creating chart');
        debugInfo.innerHTML += '<p>Attempting to create chart...</p>';
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Test Data',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        console.log('Chart created successfully');
        debugInfo.innerHTML += '<p>Chart created successfully!</p>';
    } catch (error) {
        console.error('Error creating chart:', error);
        const debugInfo = document.getElementById('debug-info');
        debugInfo.innerHTML += '<p class="text-danger">Error creating chart: ' + error.message + '</p>';
    }
});
</script>
@endpush 