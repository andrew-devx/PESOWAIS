document.addEventListener('DOMContentLoaded', function() {
    // Chart.js - Category Breakdown
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        // Data is passed via a global variable or data attribute. 
        // For cleaner separation, let's use data attributes on the canvas or a config object if possible.
        // But since the PHP echoes specific data, I might need to keep the data initialization in the PHP file 
        // or pass it via a global object.
        // For now, I will assume the variables `categoryLabels` and `categoryData` are defined in the PHP file 
        // before this script is loaded, or I can parse them from a data attribute on the canvas.
        
        // Let's use data attributes on the canvas for better encapsulation
        const labels = JSON.parse(categoryCtx.dataset.labels || '[]');
        const data = JSON.parse(categoryCtx.dataset.values || '[]');

        const categoryChart = new Chart(categoryCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#0f3460',
                        '#16213e',
                        '#e94560',
                        '#f39c12',
                        '#27ae60',
                        '#3498db',
                        '#9b59b6',
                        '#1abc9c'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const selectedCategory = labels[index].trim();
                        
                        // Update filter
                        currentFilters.category = selectedCategory;
                        
                        // Update button styles
                        document.querySelectorAll('.category-filter-btn').forEach(btn => {
                            btn.classList.remove('border-blue-600', 'text-blue-600', 'bg-blue-50', 'active');
                            btn.classList.add('border-gray-300', 'text-gray-700', 'bg-white');
                        });
                        
                        // Highlight the clicked category button
                        document.querySelectorAll('.category-filter-btn').forEach(btn => {
                            if (btn.dataset.category.trim() === selectedCategory) {
                                btn.classList.add('border-blue-600', 'text-blue-600', 'bg-blue-50', 'active');
                                btn.classList.remove('border-gray-300', 'text-gray-700', 'bg-white');
                            }
                        });
                        
                        // Filter the table
                        filterTransactions();
                        
                        // Scroll to table with null check
                        const firstRow = document.querySelector('.transaction-row');
                        if (firstRow) {
                            firstRow.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                return 'Click to filter';
                            }
                        }
                    }
                }
            }
        });
        
        // Make chart cursor pointer to indicate interactivity
        categoryCtx.style.cursor = 'pointer';
    }

    // Filter Variables
    let currentFilters = {
        startDate: null,
        endDate: null,
        type: 'all',
        category: 'all'
    };

    // Date Range Buttons
    document.querySelectorAll('.range-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.range-btn').forEach(b => {
                b.classList.remove('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
                b.classList.add('border-gray-300', 'text-gray-700', 'bg-white');
            });
            this.classList.add('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
            this.classList.remove('border-gray-300', 'text-gray-700', 'bg-white');

            const range = this.dataset.range;
            const today = new Date();

            if (range === 'thisWeek') {
                const firstDay = new Date(today);
                firstDay.setDate(today.getDate() - 6); // Last 7 days
                currentFilters.startDate = firstDay.toISOString().split('T')[0];
                currentFilters.endDate = today.toISOString().split('T')[0];
                document.getElementById('customDateRange').classList.add('hidden');
                document.getElementById('applyFilterBtn').classList.add('hidden');
                filterTransactions();
            } else if (range === 'thisMonth') {
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                currentFilters.startDate = firstDay.toISOString().split('T')[0];
                currentFilters.endDate = today.toISOString().split('T')[0];
                document.getElementById('customDateRange').classList.add('hidden');
                document.getElementById('applyFilterBtn').classList.add('hidden');
                filterTransactions();
            } else if (range === 'custom') {
                document.getElementById('customDateRange').classList.remove('hidden');
                document.getElementById('applyFilterBtn').classList.remove('hidden');
            }
        });
    });

    // Custom Date Input Change
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            currentFilters.startDate = this.value;
        });
    }
    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            currentFilters.endDate = this.value;
        });
    }

    // Type Buttons
    document.querySelectorAll('.type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.type-btn').forEach(b => {
                b.classList.remove('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
                b.classList.add('border-gray-300', 'text-gray-700', 'bg-white');
            });
            this.classList.add('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
            this.classList.remove('border-gray-300', 'text-gray-700', 'bg-white');
            currentFilters.type = this.dataset.type;
            filterTransactions();
        });
    });

    // Category Buttons
    document.querySelectorAll('.category-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-filter-btn').forEach(b => {
                b.classList.remove('border-blue-600', 'text-blue-600', 'bg-blue-50', 'active');
                b.classList.add('border-gray-300', 'text-gray-700', 'bg-white');
            });
            this.classList.add('border-blue-600', 'text-blue-600', 'bg-blue-50', 'active');
            this.classList.remove('border-gray-300', 'text-gray-700', 'bg-white');
            currentFilters.category = this.dataset.category;
            filterTransactions();
        });
    });

    // Apply Filter
    const applyFilterBtn = document.getElementById('applyFilterBtn');
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', function() {
            filterTransactions();
        });
    }

    // Reset Filters
    const resetFilterBtn = document.getElementById('resetFilterBtn');
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            currentFilters = {
                startDate: null,
                endDate: null,
                type: 'all',
                category: 'all'
            };
            
            document.querySelectorAll('.range-btn, .type-btn, .category-filter-btn').forEach(btn => {
                btn.classList.remove('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
                btn.classList.add('border-gray-300', 'text-gray-700', 'bg-white');
            });
            const typeAll = document.getElementById('typeAll');
            if (typeAll) typeAll.classList.add('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
            
            document.getElementById('customDateRange').classList.add('hidden');
            document.getElementById('applyFilterBtn').classList.add('hidden');
            
            filterTransactions();
        });
    }

    // Filter Logic
    function filterTransactions() {
        const tbody = document.getElementById('transactionTableBody');
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('.transaction-row');
        let visibleCount = 0;

        // Remove any "no transactions" message first
        const noResultsRow = tbody.querySelector('.no-results');
        if (noResultsRow) {
            noResultsRow.remove();
        }

        rows.forEach(row => {
            let show = true;

            // Date filter
            if (currentFilters.startDate && currentFilters.endDate) {
                const rowDate = row.dataset.date;
                if (rowDate < currentFilters.startDate || rowDate > currentFilters.endDate) {
                    show = false;
                }
            }

            // Type filter
            if (currentFilters.type !== 'all' && row.dataset.type !== currentFilters.type) {
                show = false;
            }

            // Category filter
            const rowCategory = row.dataset.category ? row.dataset.category.trim() : '';
            const filterCategory = currentFilters.category ? currentFilters.category.trim() : 'all';
            
            if (filterCategory !== 'all' && rowCategory !== filterCategory) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        if (visibleCount === 0) {
            tbody.insertAdjacentHTML('beforeend', '<tr class="no-results"><td colspan="6" class="py-6 px-3 text-center text-gray-500">No transactions found</td></tr>');
        }
    }

    // Export Functions
    const exportCsvBtn = document.getElementById('exportCsvBtn');
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    
    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', function() {
            exportData('csv');
        });
    }

    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            exportData('excel');
        });
    }

    function exportData(format) {
        const rows = [];
        const headers = ['Date', 'Description', 'Category', 'Type', 'Amount'];
        rows.push(headers.join(','));

        document.querySelectorAll('.transaction-row').forEach(row => {
            if (row.style.display !== 'none') {
                const cells = row.querySelectorAll('td');
                const date = cells[0].textContent.trim();
                const description = '"' + cells[1].textContent.trim().replace(/"/g, '""') + '"';
                const category = cells[2].textContent.trim();
                const type = cells[3].textContent.trim();
                const amount = cells[4].textContent.trim();
                rows.push([date, description, category, type, amount].join(','));
            }
        });

        const csv = rows.join('\n');
        
        if (format === 'csv') {
            downloadFile(csv, 'transactions.csv', 'text/csv');
        } else {
            // For Excel, create proper Excel format with BOM for UTF-8
            const BOM = '\uFEFF';
            downloadFile(BOM + csv, 'transactions.xls', 'application/vnd.ms-excel');
        }
    }

    function downloadFile(content, filename, type) {
        const blob = new Blob([content], { type });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }

    // Initial filter
    filterTransactions();

    // Event Delegation for Edit/Delete Buttons in the table
    const tableBody = document.getElementById('transactionTableBody');
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            // Edit Button
            const editBtn = e.target.closest('.edit-transaction-btn');
            if (editBtn) {
                const transaction = JSON.parse(editBtn.dataset.transaction);
                if (typeof openEditTransactionModal === 'function') {
                    openEditTransactionModal(transaction);
                } else {
                    console.error('openEditTransactionModal is not defined');
                }
            }

            // Delete Button
            const deleteBtn = e.target.closest('.delete-transaction-btn');
            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                if (typeof deleteTransaction === 'function') {
                    deleteTransaction(id);
                } else {
                    console.error('deleteTransaction is not defined');
                }
            }
        });
    }
});
