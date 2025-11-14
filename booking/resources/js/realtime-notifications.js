/**
 * Real-time Notifications JavaScript
 * Handles Pusher-based real-time notifications
 */

// Initialize Pusher
const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
    encrypted: true
});

// Subscribe to notifications channel
const notificationsChannel = pusher.subscribe('notifications');

// Handle new notifications
notificationsChannel.bind('new-notification', function(data) {
    console.log('New notification received:', data);
    
    // Show browser notification if permission granted
    if (Notification.permission === 'granted') {
        showBrowserNotification(data);
    }
    
    // Update notification count in UI
    updateNotificationCount();
    
    // Show toast notification
    showToastNotification(data);
});

// Handle booking updates
notificationsChannel.bind('booking-updated', function(data) {
    console.log('Booking updated:', data);
    showToastNotification({
        title: 'Booking Updated',
        message: data.message || 'A booking has been updated',
        type: 'info'
    });
});

// Handle new bookings
notificationsChannel.bind('new-booking', function(data) {
    console.log('New booking:', data);
    showToastNotification({
        title: 'New Booking',
        message: data.message || 'A new booking has been created',
        type: 'success'
    });
});

// Handle payment notifications
notificationsChannel.bind('payment-received', function(data) {
    console.log('Payment received:', data);
    showToastNotification({
        title: 'Payment Received',
        message: data.message || 'A payment has been received',
        type: 'success'
    });
});

// Request notification permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                console.log('Notification permission granted');
            }
        });
    }
}

// Show browser notification
function showBrowserNotification(data) {
    const notification = new Notification(data.title || 'New Notification', {
        body: data.message || 'You have a new notification',
        icon: '/favicon.ico',
        badge: '/favicon.ico'
    });
    
    // Auto-close after 5 seconds
    setTimeout(() => {
        notification.close();
    }, 5000);
    
    // Handle click
    notification.onclick = function() {
        window.focus();
        if (data.url) {
            window.location.href = data.url;
        }
        notification.close();
    };
}

// Show toast notification
function showToastNotification(data) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${data.type || 'info'}`;
    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-icon">
                <i class="fas fa-${getIconForType(data.type || 'info')}"></i>
            </div>
            <div class="toast-body">
                <div class="toast-title">${data.title || 'Notification'}</div>
                <div class="toast-message">${data.message || 'You have a new notification'}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add to page
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Get icon for notification type
function getIconForType(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Update notification count in UI
function updateNotificationCount() {
    const countElement = document.getElementById('notification-count');
    if (countElement) {
        const currentCount = parseInt(countElement.textContent) || 0;
        countElement.textContent = currentCount + 1;
        countElement.style.display = currentCount === 0 ? 'inline' : 'inline';
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission
    requestNotificationPermission();
    
    // Add toast styles if not already present
    if (!document.getElementById('toast-styles')) {
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            .toast-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
            }
            
            .toast-notification {
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                margin-bottom: 10px;
                overflow: hidden;
                animation: slideIn 0.3s ease-out;
            }
            
            .toast-content {
                display: flex;
                align-items: center;
                padding: 15px;
            }
            
            .toast-icon {
                margin-right: 12px;
                font-size: 20px;
            }
            
            .toast-success .toast-icon {
                color: #28a745;
            }
            
            .toast-error .toast-icon {
                color: #dc3545;
            }
            
            .toast-warning .toast-icon {
                color: #ffc107;
            }
            
            .toast-info .toast-icon {
                color: #17a2b8;
            }
            
            .toast-body {
                flex: 1;
            }
            
            .toast-title {
                font-weight: 600;
                margin-bottom: 4px;
                color: #333;
            }
            
            .toast-message {
                color: #666;
                font-size: 14px;
            }
            
            .toast-close {
                background: none;
                border: none;
                color: #999;
                cursor: pointer;
                padding: 4px;
                margin-left: 10px;
            }
            
            .toast-close:hover {
                color: #666;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
});

// Export functions for global use
window.RealTimeNotifications = {
    showToast: showToastNotification,
    updateCount: updateNotificationCount,
    requestPermission: requestNotificationPermission
};
