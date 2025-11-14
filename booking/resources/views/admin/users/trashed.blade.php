@extends('layouts.app')

@section('title', 'Trashed Users')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-trash me-3"></i>Trashed Users
            </h1>
            <p class="page-subtitle">Manage soft-deleted users</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>
</div>

<!-- Trashed Users Table -->
<div class="modern-card">
    <div class="modern-card-body">
        @if($users->count() > 0)
            <div class="modern-table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->profile_picture)
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                                 alt="{{ $user->name }}" 
                                                 class="rounded-circle me-2" 
                                                 width="32" height="32">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <small class="text-muted">ID: {{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'teacher' ? 'primary' : 'success') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $user->deleted_at->format('M d, Y H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info action-btn" 
                                                title="Restore User"
                                                onclick="showRestoreConfirmation(
                                                    '{{ $user->name }} ({{ ucfirst($user->role) }})',
                                                    'User',
                                                    '{{ route('admin.users.restore') }}',
                                                    '{{ $user->deleted_at->format('Y-m-d H:i:s') }}',
                                                    { id: {{ $user->id }} }
                                                )">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger action-btn" 
                                                title="Permanently Delete User"
                                                onclick="showDeleteConfirmation(
                                                    '{{ $user->name }} ({{ ucfirst($user->role) }})',
                                                    'User',
                                                    '{{ route('admin.users.delete') }}',
                                                    [],
                                                    { id: {{ $user->id }}, force_delete: true }
                                                )">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No trashed users found</h5>
                <p class="text-muted">All users are currently active.</p>
            </div>
        @endif
    </div>
</div>

<!-- Include Delete Confirmation Modal -->
@include('components.delete-confirmation-modal')

@endsection

@section('scripts')
<script>
// Global message display function
function showMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Enhanced delete confirmation function
function showDeleteConfirmation(itemName, itemType, deleteUrl, warnings = [], data = {}) {
    // Update modal content
    document.getElementById('deleteItemName').textContent = itemName;
    document.getElementById('deleteItemType').textContent = itemType;
    
    // Show warnings if any
    const warningsDiv = document.getElementById('deleteWarnings');
    if (warnings.length > 0) {
        warningsDiv.innerHTML = '<div class="alert alert-danger"><strong>Related Records:</strong><ul>' + 
            warnings.map(warning => `<li>${warning}</li>`).join('') + 
            '</ul></div>';
    } else {
        warningsDiv.innerHTML = '';
    }
    
    // Reset checkboxes
    document.getElementById('confirmDeleteCheckbox').checked = false;
    document.getElementById('forceDeleteCheckbox').checked = false;
    document.getElementById('confirmDeleteBtn').disabled = true;
    
    // Store current delete data
    window.currentDeleteUrl = deleteUrl;
    window.currentDeleteData = data;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    modal.show();
}

// Enhanced restore confirmation function
function showRestoreConfirmation(itemName, itemType, restoreUrl, deletedAt, data = {}) {
    // Update modal content
    document.getElementById('restoreItemName').textContent = itemName;
    document.getElementById('restoreItemType').textContent = itemType;
    document.getElementById('restoreDeletedAt').textContent = deletedAt;
    
    // Store current restore data (use global variables expected by modal component)
    currentRestoreUrl = restoreUrl;
    currentRestoreData = data;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('restoreConfirmationModal'));
    modal.show();
}
</script>
@endsection

@section('styles')
<style>
    /* Modern Table */
    .modern-table-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }
    
    .modern-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .modern-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #e9ecef;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .modern-table td {
        padding: 1rem;
        border-bottom: 1px solid #f8f9fa;
        vertical-align: middle;
    }
    
    .modern-table tbody tr:hover {
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
</style>
@endsection
