@extends('layouts.organisation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Record New Poaching Incident</h1>
                <a href="{{ route('organisation.poaching-incidents.index', $organisation->slug) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('organisation.poaching-incidents.store', $organisation->slug) }}" method="POST">
                        @csrf
                        
                        @include('organisation.poaching-incidents.form')
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Poaching Incident
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize flatpickr for date input
        flatpickr("#date", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        
        // Initialize flatpickr for time input
        flatpickr("#time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    });
</script>
@endsection 