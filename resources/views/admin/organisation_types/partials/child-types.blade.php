@foreach($types as $childType)
    <div class="org-item level-{{ $level }}">
        <div class="org-content">
            <div class="org-main">
                @if($childType->children->count() > 0)
                    <button class="toggle-btn" data-bs-toggle="collapse" 
                            data-bs-target="#type-{{ $childType->id }}">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
                <div class="org-info">
                    <div class="org-title-section">
                        <span class="org-type-badge">Child Type</span>
                        <h6 class="org-title mb-0">{{ $childType->name }}</h6>
                    </div>
                    <div class="org-meta">
                        <span class="org-parent">
                            <i class="fas fa-level-up-alt me-1"></i> 
                            Parent Type: {{ $childType->parent->name }}
                        </span>
                        <span class="org-count">
                            <i class="fas fa-building me-1"></i> 
                            Organizations: {{ $childType->organisations->count() }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.organisation-types.edit', $childType->slug) }}" 
                   class="btn btn-soft-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <button type="button" class="btn btn-soft-danger btn-sm" 
                        onclick="deleteType('{{ $childType->slug }}')">
                    <i class="fas fa-trash-alt me-1"></i> Delete
                </button>
            </div>
        </div>

        @if($childType->children->count() > 0)
            <div class="collapse show children-container" id="type-{{ $childType->id }}">
                @include('admin.organisation_types.partials.child-types', ['types' => $childType->children, 'level' => $level + 1])
            </div>
        @endif
    </div>
@endforeach 