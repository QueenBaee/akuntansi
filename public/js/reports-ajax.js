/**
 * Reports AJAX Utility Functions
 * Provides common functionality for AJAX-based reports
 */

class ReportsAjax {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.authToken = localStorage.getItem('auth_token') || '';
    }

    /**
     * Make AJAX request with proper headers
     */
    async makeRequest(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Authorization': `Bearer ${this.authToken}`
            }
        };

        const mergedOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, mergedOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('AJAX Request Error:', error);
            throw error;
        }
    }

    /**
     * Load Trial Balance data
     */
    async loadTrialBalance(year) {
        const url = `/api/reports/trial-balance/get-data?year=${year}`;
        return await this.makeRequest(url);
    }

    /**
     * Load Cashflow data
     */
    async loadCashflow(year) {
        const url = `/api/reports/cashflow/get-data?year=${year}`;
        return await this.makeRequest(url);
    }

    /**
     * Format accounting numbers
     */
    formatAccounting(value) {
        if (value === 0 || value === null || value === undefined) {
            return '';
        }
        
        const absValue = Math.abs(value);
        const formatted = new Intl.NumberFormat('id-ID').format(absValue);
        
        return value < 0 ? `(${formatted})` : formatted;
    }

    /**
     * Format cashflow numbers
     */
    formatCashflow(value) {
        if (value === 0 || value === null || value === undefined) {
            return '';
        }
        
        return new Intl.NumberFormat('id-ID').format(value);
    }

    /**
     * Show loading state
     */
    showLoading(elements) {
        elements.spinner?.classList.remove('d-none');
        elements.container?.classList.add('loading-overlay');
    }

    /**
     * Hide loading state
     */
    hideLoading(elements) {
        elements.spinner?.classList.add('d-none');
        elements.container?.classList.remove('loading-overlay');
    }

    /**
     * Show error message
     */
    showError(message, elements) {
        if (elements.errorMessage) {
            elements.errorMessage.textContent = message;
        }
        elements.errorContainer?.classList.remove('d-none');
        elements.reportContainer?.classList.add('d-none');
    }

    /**
     * Hide error message
     */
    hideError(elements) {
        elements.errorContainer?.classList.add('d-none');
        elements.reportContainer?.classList.remove('d-none');
    }

    /**
     * Validate year input
     */
    validateYear(year, minYear = 2020, maxYear = new Date().getFullYear() + 5) {
        const numYear = parseInt(year);
        return numYear >= minYear && numYear <= maxYear;
    }

    /**
     * Generate month names for table headers
     */
    getMonthNames(year) {
        const months = [];
        for (let m = 1; m <= 12; m++) {
            const monthName = new Date(year, m - 1, 1).toLocaleDateString('id-ID', { month: 'short' });
            months.push(`${monthName} ${year.toString().substr(-2)}`);
        }
        return months;
    }

    /**
     * Debounce function for input events
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Handle form submission with validation
     */
    handleFormSubmit(form, callback) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const year = formData.get('year');
            
            if (!this.validateYear(year)) {
                alert(`Please enter a valid year between 2020 and ${new Date().getFullYear() + 5}`);
                return;
            }
            
            callback(year);
        });
    }

    /**
     * Auto-refresh data when page loads
     */
    autoRefresh(callback, defaultYear = new Date().getFullYear()) {
        document.addEventListener('DOMContentLoaded', () => {
            callback(defaultYear);
        });
    }
}

// Export for use in other scripts
window.ReportsAjax = ReportsAjax;