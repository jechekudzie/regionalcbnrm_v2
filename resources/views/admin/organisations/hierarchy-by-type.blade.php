@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="block">
                <div class="block-header">
                    <h3 class="block-title">Organisation Hierarchy by Type</h3>
                </div>
                <div class="block-content">
                    <!-- Organization Type Selection -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <label class="form-label">Select Organisation Type</label>
                                    <select class="form-select" id="org-type-select">
                                        <option value="">Choose Organisation Type</option>
                                        @foreach($orgTypes as $type)
                                            <option value="{{ $type['id'] }}">{{ $type['text'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Hierarchy -->
                    <div class="row" id="organisation-hierarchy">
                        <!-- Dynamic dropdowns will be added here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hierarchyContainer = document.getElementById('organisation-hierarchy');
    const orgTypeSelect = document.getElementById('org-type-select');

    // Function to create a new dropdown
    function createDropdown(level, type) {
        const col = document.createElement('div');
        col.className = 'col-md-3 mb-3';
        
        const card = document.createElement('div');
        card.className = 'card';
        
        const cardBody = document.createElement('div');
        cardBody.className = 'card-body';
        
        const label = document.createElement('label');
        label.className = 'form-label';
        label.textContent = `Select ${type}`;
        
        const select = document.createElement('select');
        select.className = 'form-select';
        select.id = `level-${level}`;
        select.innerHTML = `<option value="">Choose ${type}</option>`;
        
        cardBody.appendChild(label);
        cardBody.appendChild(select);
        card.appendChild(cardBody);
        col.appendChild(card);
        hierarchyContainer.appendChild(col);
        
        return select;
    }

    // Function to remove all dropdowns
    function removeAllDropdowns() {
        while (hierarchyContainer.firstChild) {
            hierarchyContainer.removeChild(hierarchyContainer.firstChild);
        }
    }

    // Function to remove all dropdowns after a specific level
    function removeSubsequentDropdowns(level) {
        while (document.getElementById(`level-${level + 1}`)) {
            const nextLevel = document.getElementById(`level-${level + 1}`).closest('.col-md-3');
            if (nextLevel) {
                hierarchyContainer.removeChild(nextLevel);
            }
        }
    }

    // Function to load children
    async function loadChildren(parentId, level) {
        try {
            if (!parentId) {
                removeSubsequentDropdowns(level);
                return;
            }

            const response = await fetch(`/api/admin/organisations/get-children/${parentId}`);
            const data = await response.json();

            removeSubsequentDropdowns(level);

            if (data.children && data.children.length > 0) {
                const select = createDropdown(level + 1, data.nextType);

                data.children.forEach(child => {
                    const option = document.createElement('option');
                    option.value = child.id;
                    option.textContent = child.text;
                    select.appendChild(option);
                });

                select.addEventListener('change', function() {
                    loadChildren(this.value, level + 1);
                });
            }
        } catch (error) {
            console.error('Error loading children:', error);
        }
    }

    // Function to load initial organizations by type
    async function loadOrganisationsByType(typeId) {
        try {
            removeAllDropdowns();
            
            if (!typeId) return;

            const response = await fetch(`/api/admin/organisations/by-type/${typeId}`);
            const data = await response.json();

            if (data.organisations && data.organisations.length > 0) {
                const type = data.organisations[0].type;
                const select = createDropdown(0, type);

                data.organisations.forEach(org => {
                    const option = document.createElement('option');
                    option.value = org.id;
                    option.textContent = org.text;
                    select.appendChild(option);
                });

                select.addEventListener('change', function() {
                    loadChildren(this.value, 0);
                });
            }
        } catch (error) {
            console.error('Error loading organisations:', error);
        }
    }

    // Add change event to organization type select
    orgTypeSelect.addEventListener('change', function() {
        loadOrganisationsByType(this.value);
    });
});
</script>

<style>
.form-select {
    width: 100%;
    padding: 0.375rem 0.75rem;
    margin-top: 0.5rem;
}
.card {
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.card-body {
    padding: 1rem;
}
.form-label {
    font-weight: 600;
    color: #444;
    margin-bottom: 0.5rem;
}
#organisation-hierarchy {
    display: flex;
    flex-wrap: wrap;
}
@media (max-width: 768px) {
    .col-md-3 {
        width: 100%;
    }
}
</style>
@endpush
@endsection 