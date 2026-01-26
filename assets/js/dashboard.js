document.addEventListener('DOMContentLoaded', () => {
    // Transaction Filter Logic
    function computeRangeDates(range) {
        const today = new Date();
        let start = null;
        const end = today;
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
        const range = document.getElementById('filterRange')?.value || 'weekly';
        const { start: presetStart, end: presetEnd } = computeRangeDates(range);
        const startDate = range === 'custom'
            ? document.getElementById('filterStartDate')?.value
            : (presetStart ? presetStart.toISOString().slice(0, 10) : '');
        const endDate = range === 'custom'
            ? document.getElementById('filterEndDate')?.value
            : (presetEnd ? presetEnd.toISOString().slice(0, 10) : '');
        const selectedCategories = Array.from(document.querySelectorAll('.category-filter-btn.active')).map(btn => btn.dataset.category);
        const selectedMethods = Array.from(document.querySelectorAll('.method-filter-btn.active')).map(btn => btn.dataset.method);
        const rows = document.querySelectorAll('.transaction-row');

        rows.forEach(row => {
            const rowDate = row.dataset.date;
            const rowCategory = row.dataset.category;
            const rowMethod = row.dataset.method;
            let showRow = true;
            if (startDate && rowDate < startDate) showRow = false;
            if (endDate && rowDate > endDate) showRow = false;
            if (selectedCategories.length > 0 && !selectedCategories.includes(rowCategory)) showRow = false;
            if (selectedMethods.length > 0 && !selectedMethods.includes(rowMethod)) showRow = false;
            row.style.display = showRow ? '' : 'none';
        });
    }

    function resetTransactionFilters() {
        const rangeEl = document.getElementById('filterRange');
        if (rangeEl) rangeEl.value = 'weekly';
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
    document.querySelectorAll('.method-filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            btn.classList.toggle('active');
            if (btn.classList.contains('active')) {
                btn.classList.add('bg-green-600', 'border-green-600', 'text-white');
                btn.classList.remove('border-gray-300', 'text-gray-700');
            } else {
                btn.classList.remove('bg-green-600', 'border-green-600', 'text-white');
                btn.classList.add('border-gray-300', 'text-gray-700');
            }
            filterTransactions();
        });
    });
    document.getElementById('resetTransactionBtn')?.addEventListener('click', resetTransactionFilters);

    // Cashflow Filter Logic
    let cashflowChart = null;
    const allCashflowData = Array.isArray(window.cashflowData) ? window.cashflowData : [];

    function filterCashflow() {
        const rangeEl = document.getElementById('cashflowRange');
        if (!rangeEl) return;
        const range = rangeEl.value;
        const { start: presetStart, end: presetEnd } = computeRangeDates(range);
        const startDate = range === 'custom'
            ? document.getElementById('cashflowStartDate')?.value
            : (presetStart ? presetStart.toISOString().slice(0, 10) : '');
        const endDate = range === 'custom'
            ? document.getElementById('cashflowEndDate')?.value
            : (presetEnd ? presetEnd.toISOString().slice(0, 10) : '');

        let filteredData = allCashflowData;
        if (startDate || endDate) {
            filteredData = allCashflowData.filter((item, index) => {
                const itemDate = new Date(2026, 0, 21 + index); // Approximate dates
                const start = startDate ? new Date(startDate) : new Date('2000-01-01');
                const end = endDate ? new Date(endDate) : new Date('2099-12-31');
                return itemDate >= start && itemDate <= end;
            });
        }

        if (cashflowChart) {
            cashflowChart.destroy();
        }

        const cashflowCtx = document.getElementById('cashflowChart')?.getContext('2d');
        if (!cashflowCtx) return;

        cashflowChart = new Chart(cashflowCtx, {
            type: 'bar',
            data: {
                labels: filteredData.map(d => d.day),
                datasets: [
                    { label: 'Income', data: filteredData.map(d => d.income), backgroundColor: '#10b981' },
                    { label: 'Expenses', data: filteredData.map(d => d.expense), backgroundColor: '#ef4444' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 } } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { size: 10 } } },
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
        cashflowRangeEl.addEventListener('change', () => {
            const isCustom = cashflowRangeEl.value === 'custom';
            const customDates = document.getElementById('cashflowCustomDates');
            if (customDates) customDates.classList.toggle('hidden', !isCustom);
            filterCashflow();
        });
    }
    document.getElementById('cashflowStartDate')?.addEventListener('change', filterCashflow);
    document.getElementById('cashflowEndDate')?.addEventListener('change', filterCashflow);
    document.getElementById('cashflowResetBtn')?.addEventListener('click', resetCashflowFilters);

    // Initial Chart.js setup
    filterCashflow();

    // Goals Carousel Logic with arrow buttons
    (function() {
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
});
