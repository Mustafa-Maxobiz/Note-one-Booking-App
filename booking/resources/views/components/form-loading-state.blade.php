{{-- Form Loading State Component --}}
{{-- Reusable component for form submission loading states --}}

<script>
/**
 * Universal form loading state management
 * Usage: Add data-loading-text="Custom Loading Text" to submit buttons
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form loading states
    initializeFormLoadingStates();
});

function initializeFormLoadingStates() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                setLoadingState(submitBtn);
            }
        });
    });
}

/**
 * Set loading state for a button
 * @param {HTMLElement} button - The button element
 * @param {string} customText - Custom loading text (optional)
 */
function setLoadingState(button, customText = null) {
    if (!button) return;
    
    // Store original state
    if (!button.dataset.originalText) {
        button.dataset.originalText = button.innerHTML;
    }
    if (!button.dataset.originalDisabled) {
        button.dataset.originalDisabled = button.disabled;
    }
    
    // Set loading state
    const loadingText = customText || button.dataset.loadingText || 'Loading...';
    button.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${loadingText}`;
    button.disabled = true;
    
    // Add loading class if it exists
    if (button.classList.contains('btn')) {
        button.classList.add('loading');
    }
}

/**
 * Reset loading state for a button
 * @param {HTMLElement} button - The button element
 */
function resetLoadingState(button) {
    if (!button) return;
    
    // Restore original state
    if (button.dataset.originalText) {
        button.innerHTML = button.dataset.originalText;
    }
    if (button.dataset.originalDisabled !== undefined) {
        button.disabled = button.dataset.originalDisabled === 'true';
    }
    
    // Remove loading class
    button.classList.remove('loading');
}

/**
 * Set loading state for multiple buttons
 * @param {string} selector - CSS selector for buttons
 * @param {string} customText - Custom loading text (optional)
 */
function setLoadingStateForButtons(selector, customText = null) {
    const buttons = document.querySelectorAll(selector);
    buttons.forEach(button => setLoadingState(button, customText));
}

/**
 * Reset loading state for multiple buttons
 * @param {string} selector - CSS selector for buttons
 */
function resetLoadingStateForButtons(selector) {
    const buttons = document.querySelectorAll(selector);
    buttons.forEach(button => resetLoadingState(button));
}

/**
 * Auto-reset loading state after timeout
 * @param {HTMLElement} button - The button element
 * @param {number} timeout - Timeout in milliseconds (default: 5000)
 */
function autoResetLoadingState(button, timeout = 5000) {
    setTimeout(() => {
        resetLoadingState(button);
    }, timeout);
}

// Make functions globally available
window.setLoadingState = setLoadingState;
window.resetLoadingState = resetLoadingState;
window.setLoadingStateForButtons = setLoadingStateForButtons;
window.resetLoadingStateForButtons = resetLoadingStateForButtons;
window.autoResetLoadingState = autoResetLoadingState;
</script>

<style>
.btn.loading {
    pointer-events: none;
    opacity: 0.8;
    position: relative;
}

.btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
