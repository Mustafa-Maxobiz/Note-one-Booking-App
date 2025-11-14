/**
 * International Phone Number Auto-Formatter
 * Automatically formats phone numbers as users type
 * Supports international formats with country codes
 */

(function() {
    'use strict';

    /**
     * Format phone number with spaces for better readability
     * @param {string} value - The phone number to format
     * @returns {string} - Formatted phone number
     */
    function formatPhoneNumber(value) {
        // Remove all non-digit and non-plus characters for processing
        let cleaned = value.replace(/[^\d+]/g, '');
        
        // Don't format if empty
        if (!cleaned) return '';
        
        // Check if it starts with + (international format)
        const hasPlus = cleaned.startsWith('+');
        
        if (hasPlus) {
            // Remove the + temporarily for processing
            let digits = cleaned.substring(1);
            
            // Format based on length
            if (digits.length === 0) {
                return '+';
            } else if (digits.length <= 2) {
                // Country code only: +92
                return '+' + digits;
            } else if (digits.length <= 5) {
                // Country code + part of number: +92 315
                return '+' + digits.substring(0, 2) + ' ' + digits.substring(2);
            } else if (digits.length <= 8) {
                // Country code + more digits: +92 315 243
                return '+' + digits.substring(0, 2) + ' ' + 
                       digits.substring(2, 5) + ' ' + 
                       digits.substring(5);
            } else {
                // Full number: +92 315 243 3074
                return '+' + digits.substring(0, 2) + ' ' + 
                       digits.substring(2, 5) + ' ' + 
                       digits.substring(5, 8) + ' ' + 
                       digits.substring(8, 12);
            }
        } else {
            // No country code - format as groups
            if (cleaned.length <= 3) {
                return cleaned;
            } else if (cleaned.length <= 6) {
                return cleaned.substring(0, 3) + ' ' + cleaned.substring(3);
            } else if (cleaned.length <= 10) {
                return cleaned.substring(0, 3) + ' ' + 
                       cleaned.substring(3, 6) + ' ' + 
                       cleaned.substring(6);
            } else {
                return cleaned.substring(0, 3) + ' ' + 
                       cleaned.substring(3, 6) + ' ' + 
                       cleaned.substring(6, 10) + ' ' + 
                       cleaned.substring(10, 14);
            }
        }
    }

    /**
     * Initialize phone formatter on input fields
     */
    function initPhoneFormatter() {
        // Find all phone input fields
        const phoneInputs = document.querySelectorAll('input[name="phone"], input[type="tel"], input#phone');
        
        phoneInputs.forEach(function(input) {
            // Format on input
            input.addEventListener('input', function(e) {
                const cursorPosition = this.selectionStart;
                const oldValue = this.value;
                const oldLength = oldValue.length;
                
                // Format the value
                const formatted = formatPhoneNumber(this.value);
                this.value = formatted;
                
                // Adjust cursor position
                const newLength = formatted.length;
                const diff = newLength - oldLength;
                
                // If a space was added right before cursor, move cursor forward
                if (diff > 0 && formatted.charAt(cursorPosition) === ' ') {
                    this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
                } else if (diff > 0) {
                    this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
                } else {
                    this.setSelectionRange(cursorPosition, cursorPosition);
                }
            });

            // Format on paste
            input.addEventListener('paste', function(e) {
                // Small delay to allow paste to complete
                setTimeout(() => {
                    this.value = formatPhoneNumber(this.value);
                }, 10);
            });

            // Format on blur (when user leaves the field)
            input.addEventListener('blur', function() {
                this.value = formatPhoneNumber(this.value);
            });

            // Format initial value if exists
            if (input.value) {
                input.value = formatPhoneNumber(input.value);
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPhoneFormatter);
    } else {
        initPhoneFormatter();
    }

    // Export for manual initialization if needed
    window.initPhoneFormatter = initPhoneFormatter;
    window.formatPhoneNumber = formatPhoneNumber;
})();

