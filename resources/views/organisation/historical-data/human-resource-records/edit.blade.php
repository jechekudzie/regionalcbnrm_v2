@extends('layouts.organisation')

@section('title')
    Regional CBNRM - {{ $organisation->name }} - Edit Human Resource Record
@endsection

@section('content')
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-users me-1"></i>
                    Edit Human Resource Record - {{ $humanResourceRecord->period }}
                </div>
                <div>
                    <a href="{{ route('human-resource-records.index', $organisation) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('human-resource-records.update', [$organisation, $humanResourceRecord]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="period" class="form-label">Year <span class="text-danger">*</span></label>
                                <select name="period" id="period" class="form-select @error('period') is-invalid @enderror" required>
                                    <option value="">Select Year</option>
                                    @for ($year = date('Y'); $year >= 2000; $year--)
                                        <option value="{{ $year }}" {{ old('period', $humanResourceRecord->period) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                                @error('period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="employed_by" class="form-label">Employed By <span class="text-danger">*</span></label>
                                <select name="employed_by" id="employed_by" class="form-select @error('employed_by') is-invalid @enderror" required>
                                    <option value="community" {{ old('employed_by', $humanResourceRecord->employed_by) == 'community' ? 'selected' : '' }}>Community</option>
                                    <option value="organisation" {{ old('employed_by', $humanResourceRecord->employed_by) == 'organisation' ? 'selected' : '' }}>{{ $organisation->name }}</option>
                                </select>
                                @error('employed_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Who employs the staff</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="wildlife_managers" class="form-label">Wildlife Managers <span class="text-danger">*</span></label>
                                <input type="number" name="wildlife_managers" id="wildlife_managers" class="form-control @error('wildlife_managers') is-invalid @enderror" value="{{ old('wildlife_managers', $humanResourceRecord->wildlife_managers) }}" min="0" required>
                                @error('wildlife_managers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of wildlife managers</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="game_scouts" class="form-label">Game Scouts <span class="text-danger">*</span></label>
                                <input type="number" name="game_scouts" id="game_scouts" class="form-control @error('game_scouts') is-invalid @enderror" value="{{ old('game_scouts', $humanResourceRecord->game_scouts) }}" min="0" required>
                                @error('game_scouts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of game scouts</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="rangers" class="form-label">Rangers <span class="text-danger">*</span></label>
                                <input type="number" name="rangers" id="rangers" class="form-control @error('rangers') is-invalid @enderror" value="{{ old('rangers', $humanResourceRecord->rangers) }}" min="0" required>
                                @error('rangers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of rangers</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $humanResourceRecord->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Additional notes about the human resources</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('human-resource-records.index', $organisation) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Record</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 