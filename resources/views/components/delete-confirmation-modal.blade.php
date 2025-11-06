<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-warning me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone!
                </div>
                
                <div id="deleteDetails">
                    <p><strong>Item:</strong> <span id="deleteItemName"></span></p>
                    <p><strong>Type:</strong> <span id="deleteItemType"></span></p>
                    <div id="deleteWarnings" class="mt-3"></div>
                </div>
                
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmDeleteCheckbox">
                    <label class="form-check-label" for="confirmDeleteCheckbox">
                        I understand this action cannot be undone
                    </label>
                </div>
                
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="forceDeleteCheckbox">
                    <label class="form-check-label" for="forceDeleteCheckbox">
                        <strong>Force Delete</strong> - Permanently remove from database (cannot be restored)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreConfirmationModal" tabindex="-1" aria-labelledby="restoreConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="restoreConfirmationModalLabel">
                    <i class="fas fa-undo me-2"></i>
                    Restore Item
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This will restore the item and make it available again.
                </div>
                
                <div id="restoreDetails">
                    <p><strong>Item:</strong> <span id="restoreItemName"></span></p>
                    <p><strong>Type:</strong> <span id="restoreItemType"></span></p>
                    <p><strong>Deleted:</strong> <span id="restoreDeletedAt"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmRestoreBtn">
                    <i class="fas fa-undo me-1"></i>Restore
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for delete/restore functionality
let currentDeleteUrl = '';
let currentRestoreUrl = '';
let currentDeleteMethod = 'DELETE';
let currentDeleteData = {};
let currentRestoreData = {};

// Initialize delete confirmation modal
function initDeleteConfirmation() {
    const modal = document.getElementById('deleteConfirmationModal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const confirmCheckbox = document.getElementById('confirmDeleteCheckbox');
    const forceCheckbox = document.getElementById('forceDeleteCheckbox');
    
    // Reset form when modal is shown
    modal.addEventListener('show.bs.modal', function() {
        confirmCheckbox.checked = false;
        forceCheckbox.checked = false;
        confirmBtn.disabled = true;
        confirmBtn.classList.remove('btn-danger', 'btn-warning');
        confirmBtn.classList.add('btn-danger');
        confirmBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
    });
    
    // Enable/disable delete button based on checkbox
    confirmCheckbox.addEventListener('change', function() {
        confirmBtn.disabled = !this.checked;
    });
    
    // Change button style for force delete
    forceCheckbox.addEventListener('change', function() {
        if (this.checked) {
            confirmBtn.classList.remove('btn-danger');
            confirmBtn.classList.add('btn-warning');
            confirmBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Force Delete';
        } else {
            confirmBtn.classList.remove('btn-warning');
            confirmBtn.classList.add('btn-danger');
            confirmBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
        }
    });
    
    // Handle delete confirmation
    confirmBtn.addEventListener('click', function() {
        if (!confirmCheckbox.checked) return;
        
        const isForceDelete = forceCheckbox.checked;
        const method = 'POST';
        const url = currentDeleteUrl;
        
        // Add force delete parameter if needed
        const data = { ...currentDeleteData };
        if (isForceDelete) {
            data.force_delete = true;
        }
        
        // Show loading state
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';
        
        // Perform delete request
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message || 'Item deleted successfully', 'success');
                // Reload page or update table
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                showMessage(data.message || 'Failed to delete item', 'error');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showMessage('An error occurred while deleting', 'error');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
        });
    });
}

// Initialize restore confirmation modal
function initRestoreConfirmation() {
    const modal = document.getElementById('restoreConfirmationModal');
    const confirmBtn = document.getElementById('confirmRestoreBtn');
    
    // Handle restore confirmation
    confirmBtn.addEventListener('click', function() {
        const url = currentRestoreUrl;
        
        // Show loading state
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Restoring...';
        
        // Perform restore request
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(currentRestoreData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message || 'Item restored successfully', 'success');
                // Reload page or update table
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                showMessage(data.message || 'Failed to restore item', 'error');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Restore';
            }
        })
        .catch(error => {
            console.error('Restore error:', error);
            showMessage('An error occurred while restoring', 'error');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Restore';
        });
    });
}

// Show delete confirmation modal
function showDeleteConfirmation(itemName, itemType, deleteUrl, warnings = [], data = {}) {
    currentDeleteUrl = deleteUrl;
    currentDeleteData = data;
    
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
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    modal.show();
}

// Show restore confirmation modal
function showRestoreConfirmation(itemName, itemType, restoreUrl, deletedAt, data = {}) {
    currentRestoreUrl = restoreUrl;
    currentRestoreData = data;
    
    // Update modal content
    document.getElementById('restoreItemName').textContent = itemName;
    document.getElementById('restoreItemType').textContent = itemType;
    document.getElementById('restoreDeletedAt').textContent = deletedAt;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('restoreConfirmationModal'));
    modal.show();
}

// Global message display function
function showMessage(message, type = 'info') {
    // Create alert element
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
    
    // Add to page
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Initialize modals when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initDeleteConfirmation();
    initRestoreConfirmation();
});
</script>
