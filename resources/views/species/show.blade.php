@extends('layouts.organisation')

@section('title', 'Species Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Species Details</h5>
                        <div>
                            <a href="{{ route('species.edit', [$organisation->slug ?? '', $species]) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Species
                            </a>
                            <a href="{{ route('species.index', $organisation->slug ?? '') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 200px;">Name:</th>
                                    <td>{{ $species->name }}</td>
                                </tr>
                                <tr>
                                    <th>Scientific Name:</th>
                                    <td>{{ $species->scientific_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ ucfirst($species->category) ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $species->is_active ? 'success' : 'danger' }}">
                                            {{ $species->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $species->created_at->format('M d, Y H:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $species->updated_at->format('M d, Y H:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Description</h6>
                                </div>
                                <div class="card-body">
                                    {{ $species->description ?? 'No description available.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
