/**
 * BC Attendance System - Table Utilities
 * Handles table search, pagination, and sorting functionality
 */

class DataTable {
    constructor(tableId, options = {}) {
        this.tableId = tableId;
        this.table = document.getElementById(tableId);
        this.options = {
            searchable: true,
            sortable: true,
            pagination: true,
            pageSize: 20,
            pageSizes: [10, 20, 50, 100, 'all'],
            ...options
        };
        
        this.currentPage = 1;
        this.currentPageSize = this.options.pageSize;
        this.currentSort = { column: null, direction: 'asc' };
        this.searchQuery = '';
        this.data = [];
        this.filteredData = [];
        
        this.init();
    }
    
    init() {
        if (!this.table) return;
        
        this.setupTable();
        this.setupSearch();
        this.setupPagination();
        this.setupSorting();
        this.render();
    }
    
    setupTable() {
        // Add table wrapper
        this.tableWrapper = document.createElement('div');
        this.tableWrapper.className = 'table-wrapper';
        this.table.parentNode.insertBefore(this.tableWrapper, this.table);
        this.tableWrapper.appendChild(this.table);
        
        // Add table controls
        this.controlsContainer = document.createElement('div');
        this.controlsContainer.className = 'table-controls';
        this.tableWrapper.insertBefore(this.controlsContainer, this.table);
        
        // Add table info
        this.infoContainer = document.createElement('div');
        this.infoContainer.className = 'table-info';
        this.tableWrapper.appendChild(this.infoContainer);
    }
    
    setupSearch() {
        if (!this.options.searchable) return;
        
        const searchContainer = document.createElement('div');
        searchContainer.className = 'search-container';
        
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Search...';
        searchInput.className = 'search-input';
        searchInput.addEventListener('input', (e) => {
            this.searchQuery = e.target.value;
            this.currentPage = 1;
            this.render();
        });
        
        searchContainer.appendChild(searchInput);
        this.controlsContainer.appendChild(searchContainer);
    }
    
    setupPagination() {
        if (!this.options.pagination) return;
        
        const paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-container';
        
        // Page size selector
        const pageSizeContainer = document.createElement('div');
        pageSizeContainer.className = 'page-size-container';
        
        const pageSizeLabel = document.createElement('label');
        pageSizeLabel.textContent = 'Show: ';
        
        const pageSizeSelect = document.createElement('select');
        pageSizeSelect.className = 'page-size-select';
        
        this.options.pageSizes.forEach(size => {
            const option = document.createElement('option');
            option.value = size;
            option.textContent = size === 'all' ? 'All' : size;
            if (size === this.currentPageSize) {
                option.selected = true;
            }
            pageSizeSelect.appendChild(option);
        });
        
        pageSizeSelect.addEventListener('change', (e) => {
            this.currentPageSize = e.target.value === 'all' ? 'all' : parseInt(e.target.value);
            this.currentPage = 1;
            this.render();
        });
        
        pageSizeContainer.appendChild(pageSizeLabel);
        pageSizeContainer.appendChild(pageSizeSelect);
        paginationContainer.appendChild(pageSizeContainer);
        
        // Pagination controls
        this.paginationContainer = document.createElement('div');
        this.paginationContainer.className = 'pagination-controls';
        paginationContainer.appendChild(this.paginationContainer);
        
        this.controlsContainer.appendChild(paginationContainer);
    }
    
    setupSorting() {
        if (!this.options.sortable) return;
        
        const headers = this.table.querySelectorAll('th[data-sortable]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                const column = header.dataset.sortable;
                this.sort(column);
            });
            
            // Add sort indicator
            const indicator = document.createElement('span');
            indicator.className = 'sort-indicator';
            indicator.innerHTML = '↕';
            header.appendChild(indicator);
        });
    }
    
    setData(data) {
        this.data = data;
        this.filteredData = [...data];
        this.render();
    }
    
    search() {
        if (!this.searchQuery) {
            this.filteredData = [...this.data];
            return;
        }
        
        const query = this.searchQuery.toLowerCase();
        this.filteredData = this.data.filter(row => {
            return Object.values(row).some(value => 
                String(value).toLowerCase().includes(query)
            );
        });
    }
    
    sort(column) {
        if (this.currentSort.column === column) {
            this.currentSort.direction = this.currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            this.currentSort.column = column;
            this.currentSort.direction = 'asc';
        }
        
        this.filteredData.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];
            
            // Handle null/undefined values
            if (aVal === null || aVal === undefined) aVal = '';
            if (bVal === null || bVal === undefined) bVal = '';
            
            // Convert to string for comparison
            aVal = String(aVal).toLowerCase();
            bVal = String(bVal).toLowerCase();
            
            if (this.currentSort.direction === 'asc') {
                return aVal.localeCompare(bVal);
            } else {
                return bVal.localeCompare(aVal);
            }
        });
        
        this.updateSortIndicators();
        this.render();
    }
    
    updateSortIndicators() {
        const headers = this.table.querySelectorAll('th[data-sortable]');
        headers.forEach(header => {
            const indicator = header.querySelector('.sort-indicator');
            const column = header.dataset.sortable;
            
            if (this.currentSort.column === column) {
                indicator.innerHTML = this.currentSort.direction === 'asc' ? '↑' : '↓';
                indicator.style.color = '#6750A4';
            } else {
                indicator.innerHTML = '↕';
                indicator.style.color = '#999';
            }
        });
    }
    
    getPaginatedData() {
        if (this.currentPageSize === 'all') {
            return this.filteredData;
        }
        
        const start = (this.currentPage - 1) * this.currentPageSize;
        const end = start + this.currentPageSize;
        return this.filteredData.slice(start, end);
    }
    
    renderPagination() {
        if (!this.options.pagination || this.currentPageSize === 'all') {
            this.paginationContainer.innerHTML = '';
            return;
        }
        
        const totalPages = Math.ceil(this.filteredData.length / this.currentPageSize);
        const currentPage = this.currentPage;
        
        if (totalPages <= 1) {
            this.paginationContainer.innerHTML = '';
            return;
        }
        
        let paginationHTML = '';
        
        // Previous button
        if (currentPage > 1) {
            paginationHTML += `<button class="page-btn" data-page="${currentPage - 1}">Previous</button>`;
        }
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            paginationHTML += `<button class="page-btn" data-page="1">1</button>`;
            if (startPage > 2) {
                paginationHTML += `<span class="page-ellipsis">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                paginationHTML += `<button class="page-btn active" disabled>${i}</button>`;
            } else {
                paginationHTML += `<button class="page-btn" data-page="${i}">${i}</button>`;
            }
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<span class="page-ellipsis">...</span>`;
            }
            paginationHTML += `<button class="page-btn" data-page="${totalPages}">${totalPages}</button>`;
        }
        
        // Next button
        if (currentPage < totalPages) {
            paginationHTML += `<button class="page-btn" data-page="${currentPage + 1}">Next</button>`;
        }
        
        this.paginationContainer.innerHTML = paginationHTML;
        
        // Add event listeners
        this.paginationContainer.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const page = parseInt(e.target.dataset.page);
                if (page && page !== currentPage) {
                    this.currentPage = page;
                    this.render();
                }
            });
        });
    }
    
    renderTable() {
        const paginatedData = this.getPaginatedData();
        
        // Update table body
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        paginatedData.forEach((row, index) => {
            const tr = document.createElement('tr');
            
            // Add serial number
            const serialCell = document.createElement('td');
            const startIndex = this.currentPageSize === 'all' ? 0 : (this.currentPage - 1) * this.currentPageSize;
            serialCell.textContent = startIndex + index + 1;
            tr.appendChild(serialCell);
            
            // Add data cells
            Object.values(row).forEach(value => {
                const td = document.createElement('td');
                td.textContent = value || '';
                tr.appendChild(td);
            });
            
            tbody.appendChild(tr);
        });
    }
    
    renderInfo() {
        if (!this.options.pagination) return;
        
        const total = this.filteredData.length;
        let info = `Showing `;
        
        if (this.currentPageSize === 'all') {
            info += `all ${total} records`;
        } else {
            const start = (this.currentPage - 1) * this.currentPageSize + 1;
            const end = Math.min(this.currentPage * this.currentPageSize, total);
            info += `${start} to ${end} of ${total} records`;
        }
        
        this.infoContainer.textContent = info;
    }
    
    render() {
        this.search();
        this.renderTable();
        this.renderPagination();
        this.renderInfo();
    }
    
    // Public methods
    refresh() {
        this.render();
    }
    
    setPage(page) {
        this.currentPage = page;
        this.render();
    }
    
    setPageSize(size) {
        this.currentPageSize = size;
        this.currentPage = 1;
        this.render();
    }
    
    clearSearch() {
        this.searchQuery = '';
        this.currentPage = 1;
        this.render();
    }
}

// Utility functions
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

function formatDate(date) {
    if (!date) return '';
    
    const d = new Date(date);
    return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatDateTime(date) {
    if (!date) return '';
    
    const d = new Date(date);
    return d.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatNumber(num) {
    if (num === null || num === undefined) return '';
    return num.toLocaleString();
}

function formatCurrency(amount, currency = 'INR') {
    if (amount === null || amount === undefined) return '';
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

function formatPercentage(value, decimals = 2) {
    if (value === null || value === undefined) return '';
    return `${(value * 100).toFixed(decimals)}%`;
}

// Export functions for global use
window.DataTable = DataTable;
window.formatDate = formatDate;
window.formatDateTime = formatDateTime;
window.formatNumber = formatNumber;
window.formatCurrency = formatCurrency;
window.formatPercentage = formatPercentage;
