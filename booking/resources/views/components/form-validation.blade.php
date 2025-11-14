{{-- Form Validation Component --}}
{{-- Reusable component for common form validation patterns --}}

<script>
/**
 * Universal form validation utilities
 * Common validation patterns used across the application
 */

/**
 * Validate required fields
 * @param {Array} fields - Array of field selectors or elements
 * @param {string} errorMessage - Custom error message
 * @returns {boolean} - True if all fields are valid
 */
function validateRequiredFields(fields, errorMessage = 'Please fill in all required fields.') {
    let isValid = true;
    const emptyFields = [];
    
    fields.forEach(field => {
        const element = typeof field === 'string' ? document.querySelector(field) : field;
        if (element && (!element.value || element.value.trim() === '')) {
            emptyFields.push(element);
            isValid = false;
        }
    });
    
    if (!isValid) {
        showError(errorMessage);
        // Focus on first empty field
        if (emptyFields.length > 0) {
            emptyFields[0].focus();
        }
    }
    
    return isValid;
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} - True if email is valid
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number format
 * @param {string} phone - Phone number to validate
 * @returns {boolean} - True if phone is valid
 */
function validatePhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

/**
 * Validate file size
 * @param {File} file - File to validate
 * @param {number} maxSizeMB - Maximum size in MB
 * @returns {boolean} - True if file size is valid
 */
function validateFileSize(file, maxSizeMB = 2) {
    const maxSizeBytes = maxSizeMB * 1024 * 1024;
    return file.size <= maxSizeBytes;
}

/**
 * Validate file type
 * @param {File} file - File to validate
 * @param {Array} allowedTypes - Array of allowed MIME types
 * @returns {boolean} - True if file type is valid
 */
function validateFileType(file, allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']) {
    return allowedTypes.includes(file.type);
}

/**
 * Validate date is in the future
 * @param {string} dateString - Date string to validate
 * @param {string} timeString - Time string (optional)
 * @returns {boolean} - True if date is in the future
 */
function validateFutureDate(dateString, timeString = null) {
    const dateTime = timeString ? `${dateString} ${timeString}` : dateString;
    const selectedDate = new Date(dateTime);
    const now = new Date();
    return selectedDate > now;
}

/**
 * Validate password strength
 * @param {string} password - Password to validate
 * @param {number} minLength - Minimum length (default: 8)
 * @returns {Object} - Validation result with isValid and message
 */
function validatePassword(password, minLength = 8) {
    const result = {
        isValid: true,
        message: ''
    };
    
    if (password.length < minLength) {
        result.isValid = false;
        result.message = `Password must be at least ${minLength} characters long.`;
        return result;
    }
    
    if (!/(?=.*[a-z])/.test(password)) {
        result.isValid = false;
        result.message = 'Password must contain at least one lowercase letter.';
        return result;
    }
    
    if (!/(?=.*[A-Z])/.test(password)) {
        result.isValid = false;
        result.message = 'Password must contain at least one uppercase letter.';
        return result;
    }
    
    if (!/(?=.*\d)/.test(password)) {
        result.isValid = false;
        result.message = 'Password must contain at least one number.';
        return result;
    }
    
    return result;
}

/**
 * Validate form with custom rules
 * @param {HTMLElement} form - Form element
 * @param {Object} rules - Validation rules object
 * @returns {boolean} - True if form is valid
 */
function validateForm(form, rules = {}) {
    let isValid = true;
    
    // Default validation rules
    const defaultRules = {
        required: [],
        email: [],
        phone: [],
        futureDate: [],
        fileSize: {},
        fileType: {}
    };
    
    const validationRules = { ...defaultRules, ...rules };
    
    // Validate required fields
    if (validationRules.required.length > 0) {
        if (!validateRequiredFields(validationRules.required)) {
            isValid = false;
        }
    }
    
    // Validate email fields
    validationRules.email.forEach(selector => {
        const element = form.querySelector(selector);
        if (element && element.value && !validateEmail(element.value)) {
            showError('Please enter a valid email address.');
            element.focus();
            isValid = false;
        }
    });
    
    // Validate phone fields
    validationRules.phone.forEach(selector => {
        const element = form.querySelector(selector);
        if (element && element.value && !validatePhone(element.value)) {
            showError('Please enter a valid phone number.');
            element.focus();
            isValid = false;
        }
    });
    
    // Validate future dates
    validationRules.futureDate.forEach(selector => {
        const element = form.querySelector(selector);
        if (element && element.value) {
            const timeElement = form.querySelector(selector.replace('date', 'time'));
            const timeValue = timeElement ? timeElement.value : null;
            
            if (!validateFutureDate(element.value, timeValue)) {
                showError('Please select a future date and time.');
                element.focus();
                isValid = false;
            }
        }
    });
    
    // Validate file size
    Object.entries(validationRules.fileSize).forEach(([selector, maxSize]) => {
        const element = form.querySelector(selector);
        if (element && element.files && element.files[0]) {
            if (!validateFileSize(element.files[0], maxSize)) {
                showError(`File size must be less than ${maxSize}MB.`);
                element.focus();
                isValid = false;
            }
        }
    });
    
    // Validate file type
    Object.entries(validationRules.fileType).forEach(([selector, allowedTypes]) => {
        const element = form.querySelector(selector);
        if (element && element.files && element.files[0]) {
            if (!validateFileType(element.files[0], allowedTypes)) {
                showError('Please select a valid file type.');
                element.focus();
                isValid = false;
            }
        }
    });
    
    return isValid;
}

/**
 * Initialize form validation for a form
 * @param {string} formSelector - Form selector
 * @param {Object} rules - Validation rules
 */
function initializeFormValidation(formSelector, rules = {}) {
    const form = document.querySelector(formSelector);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        if (!validateForm(form, rules)) {
            e.preventDefault();
            return false;
        }
    });
}

/**
 * Real-time validation for input fields
 * @param {string} fieldSelector - Field selector
 * @param {Function} validator - Validation function
 * @param {string} errorMessage - Error message
 */
function addRealTimeValidation(fieldSelector, validator, errorMessage) {
    const field = document.querySelector(fieldSelector);
    if (!field) return;
    
    field.addEventListener('blur', function() {
        if (this.value && !validator(this.value)) {
            this.classList.add('is-invalid');
            showError(errorMessage);
        } else {
            this.classList.remove('is-invalid');
        }
    });
}

// Make functions globally available
window.validateRequiredFields = validateRequiredFields;
window.validateEmail = validateEmail;
window.validatePhone = validatePhone;
window.validateFileSize = validateFileSize;
window.validateFileType = validateFileType;
window.validateFutureDate = validateFutureDate;
window.validatePassword = validatePassword;
window.validateForm = validateForm;
window.initializeFormValidation = initializeFormValidation;
window.addRealTimeValidation = addRealTimeValidation;
</script>
