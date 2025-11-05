/**
 * Theme Toggle with Cookies
 * Stores user's theme preference in a cookie that lasts 365 days
 */

// Cookie helper functions
const CookieManager = {
    /**
     * Set a cookie
     * @param {string} name - Cookie name
     * @param {string} value - Cookie value
     * @param {number} days - Expiration in days
     */
    setCookie: function(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    },

    /**
     * Get a cookie value
     * @param {string} name - Cookie name
     * @returns {string|null} Cookie value or null if not found
     */
    getCookie: function(name) {
        const cookieName = name + "=";
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if (cookie.indexOf(cookieName) === 0) {
                return cookie.substring(cookieName.length);
            }
        }
        return null;
    },

    /**
     * Delete a cookie
     * @param {string} name - Cookie name
     */
    deleteCookie: function(name) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
};

// Theme toggle functionality
const ThemeToggle = {
    /**
     * Initialize theme on page load
     */
    init: function() {
        // Check if user has a saved theme preference
        const savedTheme = CookieManager.getCookie('theme');
        
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
            this.updateToggleIcon(true);
        } else {
            // Default to light theme
            document.body.classList.remove('dark-theme');
            this.updateToggleIcon(false);
        }
    },

    /**
     * Toggle between light and dark theme
     */
    toggle: function() {
        const isDark = document.body.classList.toggle('dark-theme');
        
        // Save preference to cookie (expires in 365 days)
        if (isDark) {
            CookieManager.setCookie('theme', 'dark', 365);
        } else {
            CookieManager.setCookie('theme', 'light', 365);
        }
        
        this.updateToggleIcon(isDark);
    },

    /**
     * Update the toggle button icon
     * @param {boolean} isDark - Whether dark theme is active
     */
    updateToggleIcon: function(isDark) {
        const toggleBtn = document.getElementById('themeToggle');
        if (toggleBtn) {
            toggleBtn.innerHTML = isDark ? '☀️' : '🌙';
            toggleBtn.title = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    }
};

// Initialize theme when page loads
document.addEventListener('DOMContentLoaded', function() {
    ThemeToggle.init();
});