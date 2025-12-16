/**
 * Form Validation Library
 * Provides client-side validation for all forms with real-time feedback
 */

const FormValidator = {
    /**
     * Validation rules and their corresponding functions
     */
    rules: {
        required: (value) => {
            return value.trim().length > 0;
        },
        
        email: (value) => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(value);
        },
        
        minLength: (value, min) => {
            return value.length >= min;
        },
        
        maxLength: (value, max) => {
            return value.length <= max;
        },
        
        password: (value) => {
            // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
            const hasMinLength = value.length >= 8;
            const hasUpperCase = /[A-Z]/.test(value);
            const hasLowerCase = /[a-z]/.test(value);
            const hasNumber = /[0-9]/.test(value);
            
            return {
                isValid: hasMinLength && hasUpperCase && hasLowerCase && hasNumber,
                details: {
                    minLength: hasMinLength,
                    upperCase: hasUpperCase,
                    lowerCase: hasLowerCase,
                    number: hasNumber
                }
            };
        },
        
        alphaNumeric: (value) => {
            return /^[a-zA-Z0-9\s]+$/.test(value);
        },
        
        alpha: (value) => {
            return /^[a-zA-Z\s]+$/.test(value);
        },
        
        numeric: (value) => {
            return /^[0-9]+$/.test(value);
        },
        
        postalCode: (value) => {
            // Supports various formats: UK, US, Canada, Ireland
            return /^[A-Z0-9\s-]{3,10}$/i.test(value);
        }
    },

    /**
     * Error messages for each validation rule
     */
    messages: {
        required: 'This field is required',
        email: 'Please enter a valid email address',
        minLength: 'Must be at least {min} characters',
        maxLength: 'Must be no more than {max} characters',
        password: 'Password must be at least 8 characters with uppercase, lowercase, and number',
        alphaNumeric: 'Only letters and numbers are allowed',
        alpha: 'Only letters are allowed',
        numeric: 'Only numbers are allowed',
        postalCode: 'Please enter a valid postal code'
    },

    /**
     * Show error message for a field
     */
    showError: (input, message) => {
        // Remove any existing error
        FormValidator.clearError(input);
        
        // Add error class to input
        input.classList.add('input-error');
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '5px';
        
        // Insert error message after input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
        
        return false;
    },

    /**
     * Clear error message for a field
     */
    clearError: (input) => {
        input.classList.remove('input-error');
        
        // Remove error message if it exists
        const errorMsg = input.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
        
        return true;
    },

    /**
     * Validate a single field based on its validation rules
     */
    validateField: (input) => {
        const value = input.value;
        const rules = input.dataset.validate ? input.dataset.validate.split('|') : [];
        
        // Clear previous errors
        FormValidator.clearError(input);
        
        // Check each validation rule
        for (let rule of rules) {
            let [ruleName, ...params] = rule.split(':');
            params = params[0] ? params[0].split(',') : [];
            
            if (ruleName === 'required') {
                if (!FormValidator.rules.required(value)) {
                    return FormValidator.showError(input, FormValidator.messages.required);
                }
            }
            
            // Skip other validations if field is empty and not required
            if (value.trim() === '' && ruleName !== 'required') {
                continue;
            }
            
            if (ruleName === 'email') {
                if (!FormValidator.rules.email(value)) {
                    return FormValidator.showError(input, FormValidator.messages.email);
                }
            }
            
            if (ruleName === 'minLength') {
                const min = parseInt(params[0]);
                if (!FormValidator.rules.minLength(value, min)) {
                    const msg = FormValidator.messages.minLength.replace('{min}', min);
                    return FormValidator.showError(input, msg);
                }
            }
            
            if (ruleName === 'maxLength') {
                const max = parseInt(params[0]);
                if (!FormValidator.rules.maxLength(value, max)) {
                    const msg = FormValidator.messages.maxLength.replace('{max}', max);
                    return FormValidator.showError(input, msg);
                }
            }
            
            if (ruleName === 'password') {
                const result = FormValidator.rules.password(value);
                if (!result.isValid) {
                    return FormValidator.showError(input, FormValidator.messages.password);
                }
            }
            
            if (ruleName === 'alpha') {
                if (!FormValidator.rules.alpha(value)) {
                    return FormValidator.showError(input, FormValidator.messages.alpha);
                }
            }
            
            if (ruleName === 'alphaNumeric') {
                if (!FormValidator.rules.alphaNumeric(value)) {
                    return FormValidator.showError(input, FormValidator.messages.alphaNumeric);
                }
            }
            
            if (ruleName === 'numeric') {
                if (!FormValidator.rules.numeric(value)) {
                    return FormValidator.showError(input, FormValidator.messages.numeric);
                }
            }
            
            if (ruleName === 'postalCode') {
                if (!FormValidator.rules.postalCode(value)) {
                    return FormValidator.showError(input, FormValidator.messages.postalCode);
                }
            }
        }
        
        return true;
    },

    /**
     * Validate entire form
     */
    validateForm: (form) => {
        const inputs = form.querySelectorAll('[data-validate]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!FormValidator.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    },

    /**
     * Initialize validation for a form
     */
    init: (formSelector) => {
        const form = document.querySelector(formSelector);
        if (!form) return;
        
        // Get all inputs with validation rules
        const inputs = form.querySelectorAll('[data-validate]');
        
        // Add real-time validation on blur
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                FormValidator.validateField(input);
            });
            
            // Clear error on focus
            input.addEventListener('focus', () => {
                FormValidator.clearError(input);
            });
            
            // Real-time validation for password to show strength
            if (input.type === 'password' && input.dataset.validate.includes('password')) {
                input.addEventListener('input', () => {
                    FormValidator.showPasswordStrength(input);
                });
            }
        });
        
        // Validate on form submit
        form.addEventListener('submit', (e) => {
            if (!FormValidator.validateForm(form)) {
                e.preventDefault();
                
                // Focus on first error
                const firstError = form.querySelector('.input-error');
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    },

    /**
     * Show password strength indicator
     */
    showPasswordStrength: (input) => {
        const value = input.value;
        const result = FormValidator.rules.password(value);
        
        // Remove existing strength indicator
        let strengthDiv = input.parentNode.querySelector('.password-strength');
        if (!strengthDiv) {
            strengthDiv = document.createElement('div');
            strengthDiv.className = 'password-strength';
            strengthDiv.style.marginTop = '5px';
            strengthDiv.style.fontSize = '0.875rem';
            input.parentNode.insertBefore(strengthDiv, input.nextSibling);
        }
        
        if (value.length === 0) {
            strengthDiv.innerHTML = '';
            return;
        }
        
        let strengthHTML = '<div style="margin-bottom: 5px;">Password strength:</div>';
        strengthHTML += '<ul style="margin: 0; padding-left: 20px;">';
        
        const checks = [
            { key: 'minLength', label: 'At least 8 characters' },
            { key: 'upperCase', label: 'One uppercase letter' },
            { key: 'lowerCase', label: 'One lowercase letter' },
            { key: 'number', label: 'One number' }
        ];
        
        checks.forEach(check => {
            const isValid = result.details[check.key];
            const color = isValid ? '#28a745' : '#dc3545';
            const icon = isValid ? '✓' : '✗';
            strengthHTML += `<li style="color: ${color};">${icon} ${check.label}</li>`;
        });
        
        strengthHTML += '</ul>';
        strengthDiv.innerHTML = strengthHTML;
    },

    /**
     * Sanitize input to prevent XSS
     */
    sanitize: (value) => {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }
};

// Add CSS for error states
const style = document.createElement('style');
style.textContent = `
    .input-error {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
    }
    
    body.dark-theme .input-error {
        background-color: #2a1a1a !important;
    }
    
    .error-message {
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);