/**
 * App Layout Optimized JavaScript
 */

// Font Awesome Check
document.addEventListener('DOMContentLoaded', function() {
    const testIcon = document.createElement('i');
    testIcon.className = 'fas fa-check';
    testIcon.style.display = 'none';
    document.body.appendChild(testIcon);
    const computed = window.getComputedStyle(testIcon, '::before');
    if (!computed || computed.getPropertyValue('content') === '' || computed.getPropertyValue('content') === 'none') {
        console.warn('Font Awesome may not be loaded correctly — icons might not display as expected.');
    }
    testIcon.remove();
});

// Real-time Notifications
class NotificationManager {
    constructor() {
        this.pusher = null;
        this.notificationsChannel = null;
        this.init();
    }

    init() {
        const pusherKey = document.querySelector('meta[name="pusher-key"]')?.content;
        const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content;
        
        if (pusherKey && pusherKey !== '' && pusherKey !== 'null') {
            this.initializePusher(pusherKey, pusherCluster);
        } else {
            console.log('Real-time notifications disabled - Pusher credentials not configured');
        }
    }

    initializePusher(key, cluster) {
        try {
            this.pusher = new Pusher(key, {
                cluster: cluster || 'mt1',
                encrypted: true,
                enabledTransports: ['ws', 'wss']
            });

            this.notificationsChannel = this.pusher.subscribe('notifications');
            this.setupEventListeners();
            console.log('Pusher initialized successfully');
        } catch (error) {
            console.warn('Pusher initialization failed:', error);
        }
    }

    setupEventListeners() {
        this.notificationsChannel.bind('new-notification', (data) => {
            console.log('New notification received:', data);
            if (Notification.permission === 'granted') {
                this.showBrowserNotification(data);
            }
            this.updateNotificationCount();
            this.showToastNotification(data);
        });

        this.notificationsChannel.bind('booking-updated', (data) => {
            this.showToastNotification({
                title: 'Booking Updated',
                message: data.message || 'A booking has been updated',
                type: 'info'
            });
        });

        this.notificationsChannel.bind('new-booking', (data) => {
            this.showToastNotification({
                title: 'New Booking',
                message: data.message || 'A new booking has been created',
                type: 'success'
            });
        });

        this.notificationsChannel.bind('payment-received', (data) => {
            this.showToastNotification({
                title: 'Payment Received',
                message: data.message || 'A payment has been received',
                type: 'success'
            });
        });
    }

    showToastNotification(data) {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${data.type || 'info'}`;
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-icon">
                    <i class="fas fa-${this.getIconForType(data.type || 'info')}"></i>
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
        
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    getIconForType(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    updateNotificationCount() {
        const countElement = document.getElementById('notification-count');
        if (countElement) {
            const currentCount = parseInt(countElement.textContent) || 0;
            countElement.textContent = currentCount + 1;
            countElement.style.display = currentCount === 0 ? 'inline' : 'inline';
        }
    }

    showBrowserNotification(data) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(data.title || 'New Notification', {
                body: data.message || 'You have a new notification',
                icon: '/favicon.ico',
                badge: '/favicon.ico'
            });
            
            setTimeout(() => {
                notification.close();
            }, 5000);
            
            notification.onclick = function() {
                window.focus();
                if (data.url) {
                    window.location.href = data.url;
                }
                notification.close();
            };
        }
    }

    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    console.log('Notification permission granted');
                }
            });
        }
    }
}

// Sidebar Manager
class SidebarManager {
    constructor() {
        this.sidebar = document.querySelector('#sidebar-menu');
        this.toggle = document.querySelector('#sidebar-toggle');
        this.overlay = document.querySelector('#sidebar-overlay');
        this.mainContent = document.querySelector('.main-content');
        this.navbarToggler = document.querySelector('.navbar-toggler');
        this.init();
    }

    init() {
        this.restoreSidebarState();
        this.setupEventListeners();
        this.addToastStyles();
    }

    restoreSidebarState() {
        if (window.innerWidth > 767.98) {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            console.log('Restoring sidebar state - collapsed:', isCollapsed);

            if (isCollapsed) {
                this.sidebar.classList.add('collapsed');
                if (this.mainContent) this.mainContent.classList.add('sidebar-collapsed');
            } else {
                if (this.mainContent) this.mainContent.classList.remove('sidebar-collapsed');
            }

            const icon = this.toggle?.querySelector('i');
            if (icon) {
                icon.className = isCollapsed ? 'fas fa-angle-right' : 'fas fa-bars';
                console.log('Setting sidebar toggle icon to:', icon.className);
            }
        }
    }

    setupEventListeners() {
        // Desktop toggle
        if (this.toggle && this.sidebar) {
            this.toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (window.innerWidth > 767.98) {
                    this.toggleSidebar();
                }
            });
        }

        // Mobile toggle
        if (this.navbarToggler && this.sidebar) {
            this.navbarToggler.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (window.innerWidth <= 767.98) {
                    this.toggleMobileSidebar();
                }
            });

            // Overlay click
            if (this.overlay) {
                this.overlay.addEventListener('click', () => {
                    this.closeMobileSidebar();
                });
            }

            // Click outside
            document.addEventListener('click', (event) => {
                if (window.innerWidth <= 767.98) {
                    if (!this.sidebar.contains(event.target) && 
                        !this.navbarToggler.contains(event.target) && 
                        !this.overlay.contains(event.target)) {
                        this.closeMobileSidebar();
                    }
                }
            });

            // Window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 767.98) {
                    this.sidebar.classList.remove('show');
                    if (this.overlay) {
                        this.overlay.classList.remove('show');
                    }
                    this.restoreSidebarState();
                } else {
                    this.sidebar.classList.remove('collapsed');
                }
            });

            // Nav links click
            const navLinks = this.sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 767.98) {
                        this.closeMobileSidebar();
                    }
                });
            });
        }
    }

    toggleSidebar() {
        console.log('Toggling sidebar collapse...');
        this.sidebar.classList.toggle('collapsed');
        
        const isCollapsed = this.sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
        console.log('Sidebar collapsed:', isCollapsed);

        if (this.mainContent) this.mainContent.classList.toggle('sidebar-collapsed');

        const icon = this.toggle.querySelector('i');
        if (icon) {
            icon.className = isCollapsed ? 'fas fa-angle-right' : 'fas fa-bars';
            console.log('Setting icon to:', icon.className);
        }
    }

    toggleMobileSidebar() {
        this.sidebar.classList.toggle('show');
        if (this.overlay) {
            this.overlay.classList.toggle('show');
        }
    }

    closeMobileSidebar() {
        this.sidebar.classList.remove('show');
        if (this.overlay) {
            this.overlay.classList.remove('show');
        }
    }

    addToastStyles() {
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
                
                .toast-success .toast-icon { color: #28a745; }
                .toast-error .toast-icon { color: #dc3545; }
                .toast-warning .toast-icon { color: #ffc107; }
                .toast-info .toast-icon { color: #17a2b8; }
                
                .toast-body { flex: 1; }
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
                .toast-close:hover { color: #666; }
                
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }
    }
}

// Session Manager
class SessionManager {
    constructor() {
        this.init();
    }

    init() {
        this.checkSessionExpired();
        this.setupFetchInterceptor();
        this.setupAjaxInterceptor();
    }

    checkSessionExpired() {
        if (document.querySelector('[data-session-expired]')) {
            this.showSessionExpiredModal();
        }
    }

    setupFetchInterceptor() {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args)
                .then(response => {
                    if (response.status === 419) {
                        this.handleSessionExpired();
                        return Promise.reject(new Error('Session expired'));
                    }
                    return response;
                })
                .catch(error => {
                    if (error.message === 'Session expired') {
                        this.handleSessionExpired();
                    }
                    throw error;
                });
        };
    }

    setupAjaxInterceptor() {
        if (typeof $ !== 'undefined') {
            $(document).ajaxError(function(event, xhr, settings, thrownError) {
                if (xhr.status === 419) {
                    this.handleSessionExpired();
                }
            });
        }
    }

    handleSessionExpired() {
        this.showSessionExpiredModal();
    }

    showSessionExpiredModal() {
        let modal = document.getElementById('sessionExpiredModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'sessionExpiredModal';
            modal.className = 'modal fade';
            modal.setAttribute('data-bs-backdrop', 'static');
            modal.setAttribute('data-bs-keyboard', 'false');
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle me-2"></i>Session Expired
                            </h5>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            </div>
                            <h6>Your session has expired</h6>
                            <p class="text-muted">For security reasons, you need to log in again to continue.</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" onclick="window.location.href='${window.location.origin}/login'">
                                <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new NotificationManager();
    new SidebarManager();
    new SessionManager();
});

// Export for global use
window.RealTimeNotifications = NotificationManager;
