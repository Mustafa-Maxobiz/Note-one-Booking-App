@extends('layouts.app')



@section('title', 'Notifications')



@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-bell me-3"></i>Notifications
            </h1>
            <p class="page-subtitle">Stay updated with your latest notifications</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <button type="button" class="btn btn-outline-light" onclick="markAllAsRead()">
                    <i class="fas fa-check-double me-2"></i>Mark All as Read
                </button>
            </div>
        </div>
    </div>
</div>



<div class="modern-card">

    <div class="modern-card-header">

        <h5 class="modern-card-title">

            <i class="fas fa-list me-2"></i>All Notifications

        </h5>

        <p class="modern-card-subtitle">Your recent notifications and updates</p>

    </div>

    <div class="modern-card-body">

        @if($notifications->count() > 0)

            <div class="list-group list-group-flush">

                @foreach($notifications as $notification)

                    <div class="list-group-item list-group-item-action {{ $notification->is_read ? '' : 'list-group-item-primary' }}" 

                         id="notification-{{ $notification->id }}">

                        <div class="d-flex w-100 justify-content-between align-items-start">

                            <div class="flex-grow-1">

                                <div class="d-flex align-items-center mb-1">

                                    <h6 class="mb-1">{{ $notification->title }}</h6>

                                    @if(!$notification->is_read)

                                        <span class="badge bg-primary ms-2">New</span>

                                    @endif

                                </div>

                                <p class="mb-1 text-muted">{{ $notification->message }}</p>

                                <small class="text-muted">

                                    <i class="fas fa-clock me-1"></i>

                                    {{ $notification->created_at->diffForHumans() }}

                                </small>

                            </div>

                            <div class="ms-3">

                                @if(!$notification->is_read)

                                    <button class="btn btn-sm btn-outline-success me-1" 

                                            onclick="markAsRead({{ $notification->id }})" 

                                            title="Mark as Read">

                                        <i class="fas fa-check"></i>

                                    </button>

                                @endif

                                <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="d-inline">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger" 

                                            onclick="return confirm('Delete this notification?')" 

                                            title="Delete">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

            

            <div class="pagination-container">

                {{ $notifications->links() }}

            </div>

        @else

            <div class="text-center py-4">

                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>

                <h5 class="text-muted">No notifications</h5>

                <p class="text-muted">You don't have any notifications yet.</p>

            </div>

        @endif

    </div>

</div>

@endsection



@section('scripts')

<script>

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the page
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

function markAsRead(notificationId) {
    console.log('Marking notification as read:', notificationId);
    
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            const notification = document.getElementById(`notification-${notificationId}`);

            notification.classList.remove('list-group-item-primary');

            const badge = notification.querySelector('.badge');

            if (badge) badge.remove();

            const readButton = notification.querySelector('.btn-outline-success');

            if (readButton) readButton.remove();
            
            // Show success message
            showAlert('success', 'Notification marked as read!');

        } else {

            showAlert('error', 'Failed to mark notification as read');

        }

    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while marking notification as read: ' + error.message);
    });

}



function markAllAsRead() {

    fetch('{{ route("notifications.markAllRead") }}', {

        method: 'POST',

        headers: {

            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',

            'Content-Type': 'application/json',

        },

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            // Update UI without reload
            document.querySelectorAll('.list-group-item-primary').forEach(item => {
                item.classList.remove('list-group-item-primary');
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
                const readButton = item.querySelector('.btn-outline-success');
                if (readButton) readButton.remove();
            });
            
            // Show success message
            showAlert('success', 'All notifications marked as read!');
        } else {
            showAlert('error', 'Failed to mark notifications as read');
        }

    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while marking notifications as read');
    });

}

</script>

@endsection

@section('styles')
<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0.5rem 0 0 0;
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    /* Modern Card */
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .modern-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .modern-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .modern-card-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0.25rem 0 0 0;
    }
    
    .modern-card-body {
        padding: 1.5rem;
    }
    
    .btn {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);
        border: none;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(239, 71, 62, 0.3);
        color: white;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .header-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
    
    @media (max-width: 576px) {
        .page-header {
            padding: 1.5rem;
        }
        
        .modern-card-header,
        .modern-card-body {
            padding: 1rem;
        }
    }
</style>
@endsection

