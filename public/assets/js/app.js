/**
 * BC Attendance System - Main JavaScript
 * Handles navigation, user menu, and general app functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize navigation
    initNavigation();
    
    // Initialize user menu
    initUserMenu();
    
    // Initialize global search
    initGlobalSearch();
    
    // Initialize keyboard shortcuts
    initKeyboardShortcuts();
    
    // Initialize unsaved changes warning
    initUnsavedChangesWarning();
});

/**
 * Initialize side navigation
 */
function initNavigation() {
    const menuToggle = document.getElementById('menuToggle');
    const sideNav = document.getElementById('sideNav');
    const navOverlay = document.getElementById('navOverlay');
    const navClose = document.getElementById('navClose');
    const mainContent = document.getElementById('mainContent');
    
    if (!menuToggle || !sideNav) return;
    
    // Toggle side navigation
    menuToggle.addEventListener('click', function() {
        sideNav.classList.add('open');
        navOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    });
    
    // Close side navigation
    function closeNav() {
        sideNav.classList.remove('open');
        navOverlay.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    navClose.addEventListener('click', closeNav);
    navOverlay.addEventListener('click', closeNav);
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sideNav.classList.contains('open')) {
            closeNav();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768 && sideNav.classList.contains('open')) {
            closeNav();
        }
    });
}

/**
 * Initialize user menu dropdown
 */
function initUserMenu() {
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userDropdown = document.getElementById('userDropdown');
    
    if (!userMenuToggle || !userDropdown) return;
    
    userMenuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!userMenuToggle.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('show');
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && userDropdown.classList.contains('show')) {
            userDropdown.classList.remove('show');
        }
    });
}

/**
 * Initialize global search functionality
 */
function initGlobalSearch() {
    const globalSearch = document.getElementById('globalSearch');
    
    if (!globalSearch) return;
    
    // Focus search on / key
    document.addEventListener('keydown', function(e) {
        if (e.key === '/' && !isInputFocused()) {
            e.preventDefault();
            globalSearch.focus();
        }
    });
    
    // Search functionality
    globalSearch.addEventListener('input', debounce(function() {
        const query = this.value.trim();
        if (query.length >= 2) {
            performGlobalSearch(query);
        }
    }, 300));
    
    // Clear search on escape
    globalSearch.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.blur();
        }
    });
}

/**
 * Perform global search
 */
function performGlobalSearch(query) {
    // This would typically make an AJAX request to search across all entities
    console.log('Searching for:', query);
    
    // For now, just show a placeholder
    // In a real implementation, this would search across:
    // - Constituencies
    // - Mandals
    // - Batches
    // - Candidates
    // - Attendance records
}

/**
 * Initialize keyboard shortcuts
 */
function initKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Only trigger shortcuts when not in input fields
        if (isInputFocused()) return;
        
        // Ctrl/Cmd + K: Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('globalSearch');
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        // Ctrl/Cmd + M: Toggle navigation
        if ((e.ctrlKey || e.metaKey) && e.key === 'm') {
            e.preventDefault();
            const menuToggle = document.getElementById('menuToggle');
            if (menuToggle) {
                menuToggle.click();
            }
        }
        
        // Ctrl/Cmd + N: New attendance
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            window.location.href = '/attendance/mark';
        }
        
        // Ctrl/Cmd + R: Reports
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            window.location.href = '/reports';
        }
    });
}

/**
 * Initialize unsaved changes warning
 */
function initUnsavedChangesWarning() {
    let hasUnsavedChanges = false;
    
    // Track form changes
    document.addEventListener('change', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
            hasUnsavedChanges = true;
        }
    });
    
    // Track form submissions
    document.addEventListener('submit', function() {
        hasUnsavedChanges = false;
    });
    
    // Warn before leaving page
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    // Warn before navigation
    document.addEventListener('click', function(e) {
        if (hasUnsavedChanges && e.target.tagName === 'A' && e.target.href) {
            const confirmed = confirm('You have unsaved changes. Are you sure you want to leave?');
            if (!confirmed) {
                e.preventDefault();
            }
        }
    });
}

/**
 * Check if any input field is focused
 */
function isInputFocused() {
    const activeElement = document.activeElement;
    return activeElement && (
        activeElement.tagName === 'INPUT' ||
        activeElement.tagName === 'TEXTAREA' ||
        activeElement.tagName === 'SELECT' ||
        activeElement.contentEditable === 'true'
    );
}

/**
 * Debounce function to limit function calls
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info', duration = 5000) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <span class="material-icons">close</span>
            </button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: var(--md-surface);
        border: 1px solid var(--md-outline-variant);
        border-radius: var(--md-radius-md);
        box-shadow: var(--md-elevation-3);
        padding: var(--md-spacing-md);
        z-index: 10000;
        max-width: 400px;
        animation: slideIn 0.3s ease-out;
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after duration
    if (duration > 0) {
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, duration);
    }
    
    // Add CSS animation
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
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
}

/**
 * Confirm action with custom message
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Format date for display
 */
function formatDate(date) {
    if (!date) return '';
    
    const d = new Date(date);
    const now = new Date();
    const diffTime = Math.abs(now - d);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) {
        return 'Today';
    } else if (diffDays === 2) {
        return 'Yesterday';
    } else if (diffDays <= 7) {
        return d.toLocaleDateString('en-US', { weekday: 'long' });
    } else {
        return d.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric',
            year: d.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
        });
    }
}

/**
 * Format time for display
 */
function formatTime(date) {
    if (!date) return '';
    
    const d = new Date(date);
    return d.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    });
}

/**
 * Get current date in YYYY-MM-DD format
 */
function getCurrentDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number format
 */
function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

/**
 * Copy text to clipboard
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showNotification('Copied to clipboard!', 'success', 2000);
    } catch (err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Copied to clipboard!', 'success', 2000);
    }
}

/**
 * Download file from blob
 */
function downloadFile(blob, filename) {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // Clean text content
            let text = cols[j].innerText.replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        
        csv.push(row.join(','));
    }
    
    // Download CSV file
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    downloadFile(blob, filename || 'export.csv');
}

/**
 * Print page or element
 */
function printPage(elementId = null) {
    if (elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print</title>
                        <link rel="stylesheet" href="/assets/css/style.css">
                        <style>
                            @media print {
                                body { margin: 0; }
                                .no-print { display: none !important; }
                            }
                        </style>
                    </head>
                    <body>${element.outerHTML}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    } else {
        window.print();
    }
}
