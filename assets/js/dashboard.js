document.addEventListener('DOMContentLoaded', () => {
    // Transaction Filter Logic
    function computeRangeDates(range) {
        const today = new Date();
        let start = null;
        const end = new Date(today);
        end.setDate(today.getDate() + 30); // Include future transactions (next 30 days)
        if (range === 'weekly') {
            start = new Date(today);
            start.setDate(today.getDate() - 6);
        } else if (range === 'monthly') {
            start = new Date(today);
            start.setDate(today.getDate() - 29);
        } else if (range === 'yearly') {
            start = new Date(today);
            start.setDate(today.getDate() - 364);
        }
        return { start, end };
    }

    function filterTransactions() {
        const selectedCategories = Array.from(document.querySelectorAll('.category-filter-btn.active')).map(btn => btn.dataset.category.trim());
        const rows = document.querySelectorAll('.transaction-row');

        console.log('Selected Categories:', selectedCategories);

        rows.forEach(row => {
            const rowCategory = row.dataset.category.trim();
            let showRow = true;
            // Only filter by category - ignore date range for Recent Transactions (always show last 5)
            if (selectedCategories.length > 0 && !selectedCategories.includes(rowCategory)) showRow = false;
            console.log('Row:', rowCategory, 'Show:', showRow);
            row.style.display = showRow ? '' : 'none';
        });
    }

    function resetTransactionFilters() {
        const rangeEl = document.getElementById('filterRange');
        if (rangeEl) rangeEl.value = 'monthly';
        const startEl = document.getElementById('filterStartDate');
        const endEl = document.getElementById('filterEndDate');
        if (startEl) startEl.value = '';
        if (endEl) endEl.value = '';
        document.querySelectorAll('.category-filter-btn, .method-filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'border-blue-600', 'text-white', 'bg-green-600', 'border-green-600');
            btn.classList.add('border-gray-300', 'text-gray-700');
        });
        const customDates = document.getElementById('transactionCustomDates');
        if (customDates) customDates.classList.add('hidden');
        filterTransactions();
    }

    // Wire transaction filter listeners
    const filterRangeEl = document.getElementById('filterRange');
    if (filterRangeEl) {
        filterRangeEl.addEventListener('change', () => {
            const isCustom = filterRangeEl.value === 'custom';
            const customDates = document.getElementById('transactionCustomDates');
            if (customDates) customDates.classList.toggle('hidden', !isCustom);
            filterTransactions();
        });
    }
    document.getElementById('filterStartDate')?.addEventListener('change', filterTransactions);
    document.getElementById('filterEndDate')?.addEventListener('change', filterTransactions);
    document.querySelectorAll('.category-filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            btn.classList.toggle('active');
            if (btn.classList.contains('active')) {
                btn.classList.add('bg-blue-600', 'border-blue-600', 'text-white');
                btn.classList.remove('border-gray-300', 'text-gray-700');
            } else {
                btn.classList.remove('bg-blue-600', 'border-blue-600', 'text-white');
                btn.classList.add('border-gray-300', 'text-gray-700');
            }
            filterTransactions();
        });
    });
    document.getElementById('resetTransactionBtn')?.addEventListener('click', resetTransactionFilters);

    // Cashflow Filter Logic (Expense Only)
    let cashflowChart = null;
    async function filterCashflow() {
        const rangeEl = document.getElementById('cashflowRange');
        if (!rangeEl) return;
        const range = rangeEl.value;

        let filteredData = [];
        try {
            const response = await fetch(`logic/fetch_chart_data.php?timeframe=${encodeURIComponent(range)}`);
            const json = await response.json();
            if (json?.status === 'success' && Array.isArray(json.data)) {
                filteredData = json.data;
            }
        } catch (err) {
            console.error('Failed to load chart data', err);
        }

        if (cashflowChart) {
            cashflowChart.destroy();
        }

        const cashflowCtx = document.getElementById('cashflowChart')?.getContext('2d');
        if (!cashflowCtx) return;

        cashflowChart = new Chart(cashflowCtx, {
            type: 'bar',
            data: {
                labels: filteredData.map(d => d.label ?? d.day),
                datasets: [
                    {
                        label: 'Expenses',
                        data: filteredData.map(d => d.expense),
                        backgroundColor: '#ef4444',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 1000,
                        ticks: {
                            font: { size: 10 },
                            callback: function (value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    function resetCashflowFilters() {
        const rangeEl = document.getElementById('cashflowRange');
        if (!rangeEl) return;
        rangeEl.value = 'weekly';
        const startEl = document.getElementById('cashflowStartDate');
        const endEl = document.getElementById('cashflowEndDate');
        if (startEl) startEl.value = '';
        if (endEl) endEl.value = '';
        filterCashflow();
    }

    // Wire cashflow listeners
    const cashflowRangeEl = document.getElementById('cashflowRange');
    if (cashflowRangeEl) {
        // Set default to weekly
        cashflowRangeEl.value = 'weekly';
        cashflowRangeEl.addEventListener('change', () => {
            const isCustom = cashflowRangeEl.value === 'custom';
            const customDates = document.getElementById('cashflowCustomDates');
            if (customDates) customDates.classList.toggle('hidden', !isCustom);
            filterCashflow();
        });
    }
    // Cashflow range selector
    document.getElementById('cashflowRange')?.addEventListener('change', filterCashflow);

    // Initial Chart.js setup - Load with weekly (7 days) by default
    filterCashflow();

    // Goals Carousel Logic with arrow buttons
    (function () {
        const slides = Array.from(document.querySelectorAll('.goal-slide'));
        const prevBtn = document.getElementById('goalPrevBtn');
        const nextBtn = document.getElementById('goalNextBtn');
        if (!slides.length || !prevBtn || !nextBtn) return;

        let current = 0;

        function showSlide(index) {
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, idx) => {
                slide.classList.toggle('hidden', idx !== current);
            });
        }

        prevBtn.addEventListener('click', () => showSlide(current - 1));
        nextBtn.addEventListener('click', () => showSlide(current + 1));
    })();

    // Mobile Sidebar Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileSidebar = document.getElementById('mobileSidebar');
    if (mobileMenuBtn && mobileSidebar) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileSidebar.classList.toggle('hidden');
        });
        document.querySelectorAll('#mobileSidebar a').forEach(link => {
            link.addEventListener('click', () => {
                mobileSidebar.classList.add('hidden');
            });
        });
    }

    // Modal Event Listeners
    // Open various modals
    document.querySelectorAll('.open-modal-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const modalId = btn.dataset.modal;
            if (openModal) openModal(modalId);
        });
    });

    // Edit Budget
    document.querySelectorAll('.edit-budget-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const category = btn.dataset.category;
            const budget = btn.dataset.budget;
            if (openEditBudgetModal) openEditBudgetModal(category, budget);
        });
    });

    // Edit Transaction
    document.querySelectorAll('.edit-transaction-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const transaction = JSON.parse(btn.dataset.transaction);
            if (openEditTransactionModal) openEditTransactionModal(transaction);
        });
    });

    // Delete Transaction
    document.querySelectorAll('.delete-transaction-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            if (deleteTransaction) deleteTransaction(id);
        });
    });

    // Edit Subscription
    document.querySelectorAll('.edit-subscription-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const name = btn.dataset.name;
            const amount = btn.dataset.amount;
            const due = btn.dataset.due;
            const status = btn.dataset.status;
            const lastPayment = btn.dataset.lastPayment;
            if (openEditSubscriptionModal) openEditSubscriptionModal(name, amount, due, status, lastPayment);
        });
    });

    // Delete Subscription
    document.querySelectorAll('.delete-subscription-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const name = e.target.closest('.delete-subscription-btn').dataset.name;
            if (deleteSubscription) deleteSubscription(name);
        });
    });

    // Export Transactions
    const exportBtn = document.getElementById('exportTransactionsBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            const range = document.getElementById('filterRange').value;
            const startDate = document.getElementById('filterStartDate').value;
            const endDate = document.getElementById('filterEndDate').value;

            // Get active category
            let category = '';
            const activeCategoryBtn = document.querySelector('.category-filter-btn.bg-blue-100'); // Check for active class
            // Note: In resetTransactionFilters, active class is removed. In click handler, it's toggled.
            // The active classes are: 'bg-blue-600', 'border-blue-600', 'text-white'
            // Wait, looking at line 70, active classes are bg-blue-600.

            const activeBtn = document.querySelector('.category-filter-btn.active');
            if (activeBtn) {
                category = activeBtn.dataset.category;
            }

            let url = `logic/export_transactions.php?range=${range}`;
            if (range === 'custom') {
                url += `&start=${startDate}&end=${endDate}`;
            }
            if (category) {
                url += `&category=${encodeURIComponent(category)}`;
            }

            window.location.href = url;
        });
    }

});
