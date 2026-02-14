<?php
    include 'includes/connection.php';
    include 'includes/auth_check.php';

    // Fetch transactions data
    include 'logic/fetch_transactions_data.php';

    include 'includes/header.php';
    include 'includes/modals.php';
?>

<body class="min-h-screen bg-gradient-to-br from-[#f5f7fa] to-[#c3cfe2]">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-6 sm:py-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Transaction History</h2>
            <p class="text-xs sm:text-sm text-gray-600">View, filter, and export your complete transaction history</p>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
            <!-- Total Income Card -->
            <div class="relative bg-white border-2 border-green-200 rounded-lg p-3 shadow-sm hover:shadow-md hover:border-green-400 transition-all duration-300 group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] text-green-700 font-semibold uppercase tracking-wide">Total Income</p>
                        <div class="bg-green-50 border border-green-200 rounded p-1.5 group-hover:bg-green-100 transition-colors">
                            <i class="fas fa-hand-holding-dollar text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-green-700">₱<?php echo number_format($totalIncome, 2); ?></p>
                </div>
            </div>

            <!-- Total Expenses Card -->
            <div class="relative bg-white border-2 border-red-200 rounded-lg p-3 shadow-sm hover:shadow-md hover:border-red-400 transition-all duration-300 group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] text-red-700 font-semibold uppercase tracking-wide">Total Expenses</p>
                        <div class="bg-red-50 border border-red-200 rounded p-1.5 group-hover:bg-red-100 transition-colors">
                            <i class="fas fa-receipt text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-red-700">₱<?php echo number_format($totalExpenses, 2); ?></p>
                </div>
            </div>

            <!-- Net Balance Card -->
            <div class="relative bg-white border-2 border-blue-200 rounded-lg p-3 shadow-sm hover:shadow-md hover:border-blue-400 transition-all duration-300 group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] text-blue-700 font-semibold uppercase tracking-wide">Net Balance</p>
                        <div class="bg-blue-50 border border-blue-200 rounded p-1.5 group-hover:bg-blue-100 transition-colors">
                            <i class="fas fa-piggy-bank text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-blue-700">₱<?php echo number_format($current_balance, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <!-- Category Breakdown Chart -->
            <div class="lg:col-span-1 bg-white rounded-lg p-4 shadow-lg">
                <h3 class="text-base font-bold text-gray-900 mb-4">Expense Breakdown by Category</h3>
                <canvas id="categoryChart" height="200"
                    data-labels='<?php echo json_encode(array_column($categoryBreakdown, 'category')); ?>' 
                    data-values='<?php echo json_encode(array_column($categoryBreakdown, 'total')); ?>'>
                </canvas>
                <div class="mt-4 space-y-2">
                    <?php foreach (array_slice($categoryBreakdown, 0, 5) as $cat): ?>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-700 font-medium"><?php echo $cat['category']; ?></span>
                            <span class="font-bold text-gray-900">₱<?php echo number_format($cat['total'], 0); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="lg:col-span-2 bg-white rounded-lg p-4 shadow-lg">
                <h3 class="text-base font-bold text-gray-900 mb-4">Filters & Export</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <!-- Date Range Filter -->
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 block">Date Range</label>
                        <div class="flex gap-1 flex-wrap">
                            <button id="rangeThisWeek" class="range-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:border-blue-600 hover:text-blue-600 transition active" data-range="thisWeek">
                                This Week
                            </button>
                            <button id="rangeThisMonth" class="range-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:border-blue-600 hover:text-blue-600 transition" data-range="thisMonth">
                                This Month
                            </button>
                            <button id="rangeCustom" class="range-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:border-blue-600 hover:text-blue-600 transition" data-range="custom">
                                Custom
                            </button>
                        </div>
                        
                        <!-- Custom Date Range (Hidden by default) -->
                        <div id="customDateRange" class="hidden mt-3 grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-semibold text-gray-700 mb-1 block">From</label>
                                <input type="date" id="startDate" class="w-full text-xs border border-gray-300 rounded px-2 py-1 bg-white text-gray-900">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-700 mb-1 block">To</label>
                                <input type="date" id="endDate" class="w-full text-xs border border-gray-300 rounded px-2 py-1 bg-white text-gray-900">
                            </div>
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 block">Transaction Type</label>
                        <div class="flex gap-1">
                            <button id="typeAll" class="type-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:border-blue-600 hover:text-blue-600 transition active" data-type="all">
                                All
                            </button>
                            <button id="typeIncome" class="type-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:border-green-600 hover:text-green-600 transition" data-type="Income">
                                <i class="fas fa-arrow-up-right text-green-600 mr-1"></i>Income
                            </button>
                            <button id="typeExpense" class="type-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:border-red-600 hover:text-red-600 transition" data-type="Expense">
                                <i class="fas fa-arrow-down-left text-red-600 mr-1"></i>Expense
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="mb-2">
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Categories</label>
                    <div class="flex gap-1 flex-wrap">
                        <button class="category-filter-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:border-blue-600 hover:text-blue-600 transition active" data-category="all">
                            All
                        </button>
                        <?php foreach ($categories as $category): ?>
                            <button class="category-filter-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:border-blue-600 hover:text-blue-600 transition" data-category="<?php echo htmlspecialchars($category); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 flex-wrap">
                    <button id="applyFilterBtn" class="hidden border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded text-xs font-bold transition">
                        <i class="fas fa-filter mr-1"></i>Apply Filter
                    </button>
                    <button id="resetFilterBtn" class="border-2 border-gray-400 text-gray-600 hover:bg-gray-400 hover:text-white px-4 py-2 rounded text-xs font-bold transition">
                        <i class="fas fa-redo mr-1"></i>Reset
                    </button>
                    <button id="exportCsvBtn" class="border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white px-4 py-2 rounded text-xs font-bold transition">
                        <i class="fas fa-download mr-1"></i>Export CSV
                    </button>
                    <button id="exportExcelBtn" class="border-2 border-green-700 text-green-700 hover:bg-green-700 hover:text-white px-4 py-2 rounded text-xs font-bold transition">
                        <i class="fas fa-file-excel mr-1"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg p-4 shadow-lg overflow-hidden">
            <h3 class="text-base font-bold text-gray-900 mb-4">All Transactions</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-3 text-gray-600 font-semibold">Date</th>
                            <th class="text-left py-3 px-3 text-gray-600 font-semibold">Description</th>
                            <th class="text-left py-3 px-3 text-gray-600 font-semibold">Category</th>
                            <th class="text-left py-3 px-3 text-gray-600 font-semibold">Type</th>
                            <th class="text-right py-3 px-3 text-gray-600 font-semibold">Amount</th>
                            <th class="text-center py-3 px-3 text-gray-600 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transactionTableBody">
                        <?php foreach ($allTransactions as $transaction): ?>
                            <tr class="transaction-row border-b border-gray-100 hover:bg-gray-50 transition" data-date="<?php echo date('Y-m-d', strtotime($transaction['transaction_date'])); ?>" data-category="<?php echo trim($transaction['category']); ?>" data-type="<?php echo $transaction['type']; ?>">
                                <td class="py-3 px-3 text-gray-900 font-medium"><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></td>
                                <td class="py-3 px-3 text-gray-900"><?php echo htmlspecialchars($transaction['description'] ?? ''); ?></td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium"><?php echo htmlspecialchars($transaction['category']); ?></span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="<?php echo $transaction['type'] === 'Income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> px-2 py-1 rounded text-xs font-semibold inline-flex items-center gap-1">
                                        <i class="fas <?php echo $transaction['type'] === 'Income' ? 'fa-arrow-up-right' : 'fa-arrow-down-left'; ?>"></i>
                                        <?php echo $transaction['type']; ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-bold <?php echo $transaction['type'] === 'Income' ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo ($transaction['type'] === 'Income' ? '+' : '-') . '₱' . number_format($transaction['amount'], 2); ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button type="button" class="edit-transaction-btn text-blue-600 hover:text-blue-700 text-xs font-medium" 
                                                title="Edit"
                                                data-transaction='<?php echo htmlspecialchars(json_encode($transaction)); ?>'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="delete-transaction-btn text-red-600 hover:text-red-700 text-xs font-medium" 
                                                title="Delete"
                                                data-id="<?php echo $transaction['transaction_id'] ?? 0; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <script src="assets/js/transactions.js"></script>
</body>
