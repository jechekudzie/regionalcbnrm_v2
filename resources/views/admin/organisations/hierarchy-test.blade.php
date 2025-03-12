@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="block">
                <div class="block-header">
                    <h3 class="block-title">Organisation Hierarchy</h3>
                </div>
                <div class="block-content">
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
    const initialData = @json($initialData);

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
            // If no value is selected, remove subsequent dropdowns
            if (!parentId) {
                removeSubsequentDropdowns(level);
                return;
            }

            const response = await fetch(`/api/admin/organisations/get-children/${parentId}`);
            const data = await response.json();

            // Remove any existing dropdowns after this level
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

    // Create initial RDC dropdown
    const initialSelect = createDropdown(0, 'Rural District Council');
    
    // Populate initial RDCs
    initialData.forEach(rdc => {
        const option = document.createElement('option');
        option.value = rdc.id;
        option.textContent = rdc.text;
        initialSelect.appendChild(option);
    });

    // Add change event to initial dropdown
    initialSelect.addEventListener('change', function() {
        loadChildren(this.value, 0);
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