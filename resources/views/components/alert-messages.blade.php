{{-- Alert Messages Component --}}
{{-- Reusable component for success/error/info messages --}}

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script>
/**
 * Universal alert message management
 * Functions for creating and managing alert messages dynamically
 */

/**
 * Show alert message
 * @param {string} message - The message to display
 * @param {string} type - Alert type (success, error, warning, info)
 * @param {number} duration - Auto-hide duration in milliseconds (0 = no auto-hide)
 * @param {string} container - Container selector to append alert to (default: body)
 */
function showAlert(message, type = 'info', duration = 5000, container = 'body') {
    const alertId = 'alert-' + Date.now();
    const iconClass = getAlertIcon(type);
    
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const targetContainer = document.querySelector(container);
    if (targetContainer) {
        targetContainer.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-hide if duration is specified
        if (duration > 0) {
            setTimeout(() => {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    const bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }
            }, duration);
        }
    }
}

/**
 * Get appropriate icon for alert type
 * @param {string} type - Alert type
 * @returns {string} Icon class
 */
function getAlertIcon(type) {
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-triangle',
        'danger': 'fa-exclamation-triangle',
        'warning': 'fa-exclamation-circle',
        'info': 'fa-info-circle'
    };
    return icons[type] || 'fa-info-circle';
}

/**
 * Show success message
 * @param {string} message - Success message
 * @param {number} duration - Auto-hide duration (default: 5000ms)
 */
function showSuccess(message, duration = 5000) {
    showAlert(message, 'success', duration);
}

/**
 * Show error message
 * @param {string} message - Error message
 * @param {number} duration - Auto-hide duration (default: 0 = no auto-hide)
 */
function showError(message, duration = 0) {
    showAlert(message, 'error', duration);
}

/**
 * Show warning message
 * @param {string} message - Warning message
 * @param {number} duration - Auto-hide duration (default: 7000ms)
 */
function showWarning(message, duration = 7000) {
    showAlert(message, 'warning', duration);
}

/**
 * Show info message
 * @param {string} message - Info message
 * @param {number} duration - Auto-hide duration (default: 5000ms)
 */
function showInfo(message, duration = 5000) {
    showAlert(message, 'info', duration);
}

/**
 * Clear all alerts in a container
 * @param {string} container - Container selector (default: body)
 */
function clearAlerts(container = 'body') {
    const targetContainer = document.querySelector(container);
    if (targetContainer) {
        const alerts = targetContainer.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }
}

/**
 * Show loading alert
 * @param {string} message - Loading message
 * @param {string} container - Container selector
 * @returns {string} Alert ID for later removal
 */
function showLoadingAlert(message = 'Loading...', container = 'body') {
    const alertId = 'loading-alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-spinner fa-spin me-2"></i>
            ${message}
        </div>
    `;
    
    const targetContainer = document.querySelector(container);
    if (targetContainer) {
        targetContainer.insertAdjacentHTML('afterbegin', alertHtml);
    }
    
    return alertId;
}

/**
 * Hide loading alert
 * @param {string} alertId - Alert ID returned from showLoadingAlert
 */
function hideLoadingAlert(alertId) {
    const alertElement = document.getElementById(alertId);
    if (alertElement) {
        const bsAlert = new bootstrap.Alert(alertElement);
        bsAlert.close();
    }
}

// Make functions globally available
window.showAlert = showAlert;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
window.clearAlerts = clearAlerts;
window.showLoadingAlert = showLoadingAlert;
window.hideLoadingAlert = hideLoadingAlert;
</script>
