@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-3">
    <div class="col-md-12">
        <div class="form-group">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control @error('title') is-invalid @enderror" 
                   id="title" 
                   name="title" 
                   value="{{ old('title', $poachingIncident->title ?? '') }}" 
                   required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="period" class="form-label">Period (Year) <span class="text-danger">*</span></label>
            <select class="form-select @error('period') is-invalid @enderror" 
                    id="period" 
                    name="period" 
                    required>
                <option value="">Select Year</option>
                @for ($year = date('Y'); $year >= 1900; $year--)
                    <option value="{{ $year }}" 
                            {{ old('period', $poachingIncident->period ?? date('Y')) == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endfor
            </select>
            @error('period')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control datetimepicker @error('date') is-invalid @enderror" 
                   id="date" 
                   name="date" 
                   value="{{ old('date', isset($poachingIncident) ? $poachingIncident->date->format('Y-m-d') : date('Y-m-d')) }}" 
                   
                   required>
            @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="time" class="form-label">Time <span class="text-danger">*</span></label>
            
            <input type="text" 
                   class="form-control datetimepicker @error('time') is-invalid @enderror" 
                   id="time" 
                   name="time"  
                   value="{{ old('time', isset($poachingIncident) ? $poachingIncident->time->format('H:i') : date('H:i')) }}" 
                   data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true}'
                   required>
            @error('time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label for="location" class="form-label">Location Description</label>
            <input type="text" 
                   class="form-control @error('location') is-invalid @enderror" 
                   id="location" 
                   name="location" 
                   value="{{ old('location', $poachingIncident->location ?? '') }}">
            @error('location')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="latitude" class="form-label">Latitude</label>
            <input type="number" 
                   class="form-control @error('latitude') is-invalid @enderror" 
                   id="latitude" 
                   name="latitude" 
                   value="{{ old('latitude', $poachingIncident->latitude ?? '') }}" 
                   step="any">
            @error('latitude')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="longitude" class="form-label">Longitude</label>
            <input type="number" 
                   class="form-control @error('longitude') is-invalid @enderror" 
                   id="longitude" 
                   name="longitude" 
                   value="{{ old('longitude', $poachingIncident->longitude ?? '') }}" 
                   step="any">
            @error('longitude')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="docket_number" class="form-label">Docket Number</label>
            <input type="text" 
                   class="form-control @error('docket_number') is-invalid @enderror" 
                   id="docket_number" 
                   name="docket_number" 
                   value="{{ old('docket_number', $poachingIncident->docket_number ?? '') }}">
            @error('docket_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="docket_status" class="form-label">Docket Status</label>
            <select class="form-select @error('docket_status') is-invalid @enderror" 
                    id="docket_status" 
                    name="docket_status">
                <option value="">Select Docket Status</option>
                <option value="open" {{ old('docket_status', $poachingIncident->docket_status ?? '') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="under investigation" {{ old('docket_status', $poachingIncident->docket_status ?? '') == 'under investigation' ? 'selected' : '' }}>Under Investigation</option>
                <option value="pending court" {{ old('docket_status', $poachingIncident->docket_status ?? '') == 'pending court' ? 'selected' : '' }}>Pending Court</option>
                <option value="convicted" {{ old('docket_status', $poachingIncident->docket_status ?? '') == 'convicted' ? 'selected' : '' }}>Convicted</option>
                <option value="closed" {{ old('docket_status', $poachingIncident->docket_status ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            @error('docket_status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <hr>
        <h5>Species Information</h5>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <button type="button" class="btn btn-sm btn-success add-species-row">
                <i class="fas fa-plus"></i> Add Species
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="species-table">
                <thead>
                    <tr>
                        <th>Species</th>
                        <th>Estimated Number</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(old('species'))
                        @foreach(old('species') as $key => $speciesData)
                            <tr>
                                <td>
                                    <select name="species[{{ $key }}][id]" class="form-select" required>
                                        <option value="">Select Species</option>
                                        @foreach($species as $specie)
                                            <option value="{{ $specie->id }}" {{ $speciesData['id'] == $specie->id ? 'selected' : '' }}>
                                                {{ $specie->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="species[{{ $key }}][estimate_number]" class="form-control" value="{{ $speciesData['estimate_number'] ?? '' }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-species-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @elseif(isset($poachingIncident) && $poachingIncident->species->count() > 0)
                        @foreach($poachingIncident->species as $key => $speciesItem)
                            <tr>
                                <td>
                                    <select name="species[{{ $key }}][id]" class="form-select" required>
                                        <option value="">Select Species</option>
                                        @foreach($species as $specie)
                                            <option value="{{ $specie->id }}" {{ $speciesItem->id == $specie->id ? 'selected' : '' }}>
                                                {{ $specie->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="species[{{ $key }}][estimate_number]" class="form-control" value="{{ $speciesItem->pivot->estimate_number ?? '' }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-species-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <select name="species[0][id]" class="form-select" required>
                                    <option value="">Select Species</option>
                                    @foreach($species as $specie)
                                        <option value="{{ $specie->id }}">
                                            {{ $specie->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="species[0][estimate_number]" class="form-control">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-species-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12">
        <hr>
        <h5>Poaching Methods</h5>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label class="form-label">Select Poaching Methods <span class="text-danger">*</span></label>
            <div class="row">
                @foreach($poachingMethods as $method)
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input @error('poaching_methods') is-invalid @enderror" 
                                   type="checkbox" 
                                   name="poaching_methods[]" 
                                   value="{{ $method->id }}" 
                                   id="method_{{ $method->id }}"
                                   {{ (old('poaching_methods') && in_array($method->id, old('poaching_methods'))) || 
                                      (isset($poachingIncident) && $poachingIncident->methods->contains($method->id)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="method_{{ $method->id }}">
                                {{ $method->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('poaching_methods')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <hr>
        <h5>Poachers Information</h5>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <button type="button" class="btn btn-sm btn-success add-poacher-row">
                <i class="fas fa-plus"></i> Add Poacher
            </button>
        </div>

        <div id="poachers-container">
            @if(old('poachers'))
                @foreach(old('poachers') as $key => $poacherData)
                    <div class="card mb-3 poacher-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Poacher #{{ $key + 1 }}</h6>
                            <button type="button" class="btn btn-sm btn-danger remove-poacher">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            @if(isset($poacherData['id']))
                                <input type="hidden" name="poachers[{{ $key }}][id]" value="{{ $poacherData['id'] }}">
                            @endif
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="poachers[{{ $key }}][first_name]" class="form-control" value="{{ $poacherData['first_name'] }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="poachers[{{ $key }}][middle_name]" class="form-control" value="{{ $poacherData['middle_name'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="poachers[{{ $key }}][last_name]" class="form-control" value="{{ $poacherData['last_name'] }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="poachers[{{ $key }}][age]" class="form-control" value="{{ $poacherData['age'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select name="poachers[{{ $key }}][status]" class="form-select">
                                        <option value="">Select Status</option>
                                        <option value="suspected" {{ isset($poacherData['status']) && $poacherData['status'] == 'suspected' ? 'selected' : '' }}>Suspected</option>
                                        <option value="arrested" {{ isset($poacherData['status']) && $poacherData['status'] == 'arrested' ? 'selected' : '' }}>Arrested</option>
                                        <option value="bailed" {{ isset($poacherData['status']) && $poacherData['status'] == 'bailed' ? 'selected' : '' }}>Bailed</option>
                                        <option value="sentenced" {{ isset($poacherData['status']) && $poacherData['status'] == 'sentenced' ? 'selected' : '' }}>Sentenced</option>
                                        <option value="released" {{ isset($poacherData['status']) && $poacherData['status'] == 'released' ? 'selected' : '' }}>Released</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Offense Type</label>
                                    <select name="poachers[{{ $key }}][offence_type_id]" class="form-select">
                                        <option value="">Select Offense Type</option>
                                        @foreach($offenceTypes as $type)
                                            <option value="{{ $type->id }}" {{ isset($poacherData['offence_type_id']) && $poacherData['offence_type_id'] == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Poacher Type</label>
                                    <select name="poachers[{{ $key }}][poacher_type_id]" class="form-select">
                                        <option value="">Select Poacher Type</option>
                                        @foreach($poacherTypes as $type)
                                            <option value="{{ $type->id }}" {{ isset($poacherData['poacher_type_id']) && $poacherData['poacher_type_id'] == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Country</label>
                                    <select name="poachers[{{ $key }}][country_id]" class="form-select country-select">
                                        <option value="">Select Country</option>
                                        @foreach(\App\Models\Admin\Country::all() as $country)
                                            <option value="{{ $country->id }}" {{ isset($poacherData['country_id']) && $poacherData['country_id'] == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Province</label>
                                    <select name="poachers[{{ $key }}][province_id]" class="form-select province-select">
                                        <option value="">Select Province</option>
                                        @foreach(\App\Models\Admin\Province::all() as $province)
                                            <option value="{{ $province->id }}" {{ isset($poacherData['province_id']) && $poacherData['province_id'] == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <select name="poachers[{{ $key }}][city_id]" class="form-select city-select">
                                        <option value="">Select City</option>
                                        @foreach(\App\Models\Admin\City::all() as $city)
                                            <option value="{{ $city->id }}" {{ isset($poacherData['city_id']) && $poacherData['city_id'] == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">Poaching Reason</label>
                                    <select name="poachers[{{ $key }}][poaching_reason_id]" class="form-select">
                                        <option value="">Select Poaching Reason</option>
                                        @foreach($poachingReasons as $reason)
                                            <option value="{{ $reason->id }}" {{ isset($poacherData['poaching_reason_id']) && $poacherData['poaching_reason_id'] == $reason->id ? 'selected' : '' }}>
                                                {{ $reason->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @elseif(isset($poachingIncident) && $poachingIncident->poachers->count() > 0)
                @foreach($poachingIncident->poachers as $key => $poacher)
                    <div class="card mb-3 poacher-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Poacher #{{ $key + 1 }}</h6>
                            <button type="button" class="btn btn-sm btn-danger remove-poacher">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="poachers[{{ $key }}][id]" value="{{ $poacher->id }}">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="poachers[{{ $key }}][first_name]" class="form-control" value="{{ $poacher->first_name }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="poachers[{{ $key }}][middle_name]" class="form-control" value="{{ $poacher->middle_name ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="poachers[{{ $key }}][last_name]" class="form-control" value="{{ $poacher->last_name }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="poachers[{{ $key }}][age]" class="form-control" value="{{ $poacher->age ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select name="poachers[{{ $key }}][status]" class="form-select">
                                        <option value="">Select Status</option>
                                        <option value="suspected" {{ $poacher->status == 'suspected' ? 'selected' : '' }}>Suspected</option>
                                        <option value="arrested" {{ $poacher->status == 'arrested' ? 'selected' : '' }}>Arrested</option>
                                        <option value="bailed" {{ $poacher->status == 'bailed' ? 'selected' : '' }}>Bailed</option>
                                        <option value="sentenced" {{ $poacher->status == 'sentenced' ? 'selected' : '' }}>Sentenced</option>
                                        <option value="released" {{ $poacher->status == 'released' ? 'selected' : '' }}>Released</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Offense Type</label>
                                    <select name="poachers[{{ $key }}][offence_type_id]" class="form-select">
                                        <option value="">Select Offense Type</option>
                                        @foreach($offenceTypes as $type)
                                            <option value="{{ $type->id }}" {{ $poacher->offence_type_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Poacher Type</label>
                                    <select name="poachers[{{ $key }}][poacher_type_id]" class="form-select">
                                        <option value="">Select Poacher Type</option>
                                        @foreach($poacherTypes as $type)
                                            <option value="{{ $type->id }}" {{ $poacher->poacher_type_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Country</label>
                                    <select name="poachers[{{ $key }}][country_id]" class="form-select country-select">
                                        <option value="">Select Country</option>
                                        @foreach(\App\Models\Admin\Country::all() as $country)
                                            <option value="{{ $country->id }}" {{ $poacher->country_id == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Province</label>
                                    <select name="poachers[{{ $key }}][province_id]" class="form-select province-select">
                                        <option value="">Select Province</option>
                                        @foreach(\App\Models\Admin\Province::all() as $province)
                                            <option value="{{ $province->id }}" {{ $poacher->province_id == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <select name="poachers[{{ $key }}][city_id]" class="form-select city-select">
                                        <option value="">Select City</option>
                                        @foreach(\App\Models\Admin\City::all() as $city)
                                            <option value="{{ $city->id }}" {{ $poacher->city_id == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">Poaching Reason</label>
                                    <select name="poachers[{{ $key }}][poaching_reason_id]" class="form-select">
                                        <option value="">Select Poaching Reason</option>
                                        @foreach($poachingReasons as $reason)
                                            <option value="{{ $reason->id }}" {{ $poacher->poaching_reason_id == $reason->id ? 'selected' : '' }}>
                                                {{ $reason->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card mb-3 poacher-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Poacher #1</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-poacher">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="poachers[0][first_name]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="poachers[0][middle_name]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="poachers[0][last_name]" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Age</label>
                                <input type="number" name="poachers[0][age]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="poachers[0][status]" class="form-select">
                                    <option value="">Select Status</option>
                                    <option value="suspected">Suspected</option>
                                    <option value="arrested">Arrested</option>
                                    <option value="bailed">Bailed</option>
                                    <option value="sentenced">Sentenced</option>
                                    <option value="released">Released</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Offense Type</label>
                                <select name="poachers[0][offence_type_id]" class="form-select">
                                    <option value="">Select Offense Type</option>
                                    @foreach($offenceTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Poacher Type</label>
                                <select name="poachers[0][poacher_type_id]" class="form-select">
                                    <option value="">Select Poacher Type</option>
                                    @foreach($poacherTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Country</label>
                                <select name="poachers[0][country_id]" class="form-select country-select">
                                    <option value="">Select Country</option>
                                    @foreach(\App\Models\Admin\Country::all() as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Province</label>
                                <select name="poachers[0][province_id]" class="form-select province-select">
                                    <option value="">Select Province</option>
                                    @foreach(\App\Models\Admin\Province::all() as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <select name="poachers[0][city_id]" class="form-select city-select">
                                    <option value="">Select City</option>
                                    @foreach(\App\Models\Admin\City::all() as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Poaching Reason</label>
                                <select name="poachers[0][poaching_reason_id]" class="form-select">
                                    <option value="">Select Poaching Reason</option>
                                    @foreach($poachingReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to filter cities based on selected province
        function filterCitiesByProvince(provinceSelect) {
            const provinceId = provinceSelect.value;
            const poacherCard = provinceSelect.closest('.card-body');
            const citySelect = poacherCard.querySelector('[name$="[city_id]"]');
            
            // Clear all options except the first one
            while (citySelect.options.length > 1) {
                citySelect.remove(1);
            }
            
            if (provinceId) {
                // Use AJAX to fetch cities for the selected province
                fetch(`/api/provinces/${provinceId}/cities`)
                    .then(response => response.json())
                    .then(cities => {
                        cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            citySelect.appendChild(option);
                        });
                    });
            }
        }
        
        // Setup province change event handlers for existing forms
        document.querySelectorAll('[name$="[province_id]"]').forEach(provinceSelect => {
            provinceSelect.addEventListener('change', function() {
                filterCitiesByProvince(this);
            });
        });

        // Add species row
        document.querySelector('.add-species-row').addEventListener('click', function() {
            const tbody = document.querySelector('#species-table tbody');
            const rowCount = tbody.querySelectorAll('tr').length;
            const row = `
                <tr>
                    <td>
                        <select name="species[${rowCount}][id]" class="form-select" required>
                            <option value="">Select Species</option>
                            @foreach($species as $specie)
                                <option value="{{ $specie->id }}">{{ $specie->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="species[${rowCount}][estimate_number]" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-species-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        // Remove species row
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-species-row')) {
                const tbody = document.querySelector('#species-table tbody');
                if (tbody.querySelectorAll('tr').length > 1) {
                    e.target.closest('tr').remove();
                }
            }
        });

        // Add poacher
        document.querySelector('.add-poacher-row').addEventListener('click', function() {
            const container = document.querySelector('#poachers-container');
            const poacherCount = container.querySelectorAll('.poacher-card').length;
            const poacher = `
                <div class="card mb-3 poacher-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Poacher #${poacherCount + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-poacher">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="poachers[${poacherCount}][first_name]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="poachers[${poacherCount}][middle_name]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="poachers[${poacherCount}][last_name]" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Age</label>
                                <input type="number" name="poachers[${poacherCount}][age]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="poachers[${poacherCount}][status]" class="form-select">
                                    <option value="">Select Status</option>
                                    <option value="suspected">Suspected</option>
                                    <option value="arrested">Arrested</option>
                                    <option value="bailed">Bailed</option>
                                    <option value="sentenced">Sentenced</option>
                                    <option value="released">Released</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Offense Type</label>
                                <select name="poachers[${poacherCount}][offence_type_id]" class="form-select">
                                    <option value="">Select Offense Type</option>
                                    @foreach($offenceTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Poacher Type</label>
                                <select name="poachers[${poacherCount}][poacher_type_id]" class="form-select">
                                    <option value="">Select Poacher Type</option>
                                    @foreach($poacherTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Country</label>
                                <select name="poachers[${poacherCount}][country_id]" class="form-select country-select">
                                    <option value="">Select Country</option>
                                    @foreach(\App\Models\Admin\Country::all() as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Province</label>
                                <select name="poachers[${poacherCount}][province_id]" class="form-select province-select">
                                    <option value="">Select Province</option>
                                    @foreach(\App\Models\Admin\Province::all() as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <select name="poachers[${poacherCount}][city_id]" class="form-select city-select">
                                    <option value="">Select City</option>
                                    @foreach(\App\Models\Admin\City::all() as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Poaching Reason</label>
                                <select name="poachers[${poacherCount}][poaching_reason_id]" class="form-select">
                                    <option value="">Select Poaching Reason</option>
                                    @foreach($poachingReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', poacher);
            
            // Setup province-city filtering for the new poacher card
            setTimeout(function() {
                const newPoacherCard = document.querySelector('#poachers-container .poacher-card:last-child');
                const provinceSelect = newPoacherCard.querySelector('[name$="[province_id]"]');
                
                provinceSelect.addEventListener('change', function() {
                    filterCitiesByProvince(this);
                });
            }, 100);
        });

        // Remove poacher
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-poacher')) {
                const container = document.querySelector('#poachers-container');
                if (container.querySelectorAll('.poacher-card').length > 1) {
                    e.target.closest('.poacher-card').remove();
                }
            }
        });
    });
</script> 