<?php
    include  'includes/connection.php';
    include 'includes/auth_check.php';

    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? 'User';

    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? 'User';

    // Fetch all dashboard data
    include 'logic/fetch_dashboard_data.php';

    include 'logic/ai_analysis.php';
    include 'includes/header.php';


    // Include view helpers
    include 'includes/view_helpers.php';

    // Determine balance color (Visual logic using data from fetch)
    $balanceClass = $financialData['currentBalance'] >= 0 ? 'text-green-600' : 'text-red-600';
?>

<body class="min-h-screen bg-gradient-to-br from-[#f5f7fa] to-[#c3cfe2]">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-6 sm:py-8">
        <!-- Welcome Section -->
        <div class="mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Welcome back, <?php echo htmlspecialchars($username); ?></h2>
            <p class="text-xs sm:text-sm text-gray-600">Here's your financial snapshot for this month</p>
        </div>

        <!-- 3-Column Bento Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- LEFT COLUMN (3 cols) - Charts & Table -->
            <div class="lg:col-span-3 space-y-4">
                <!-- At-a-Glance Stats (Top Row) -->
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php
                        renderStatCard(
                            'Current Balance',
                            '₱' . number_format($financialData['currentBalance'], 2),
                            $balanceStatus,
                            'text-blue-700',
                            'fas fa-wallet text-blue-600',
                            'border-blue-200 hover:border-blue-400'
                        );
                        renderStatCard(
                            'Total Income',
                            '₱' . number_format($financialData['totalIncome'], 2),
                            'This month',
                            'text-green-700',
                            'fas fa-arrow-trend-up text-green-600',
                            'border-green-200 hover:border-green-400'
                        );
                        renderStatCard(
                            'Total Expenses',
                            '₱' . number_format($financialData['totalExpenses'], 2),
                            'This month',
                            'text-red-700',
                            'fas fa-arrow-trend-down text-red-600',
                            'border-red-200 hover:border-red-400'
                        );
                        renderStatCard(
                            'Total Utang',
                            '₱' . number_format($totalUtang, 2),
                            count($utangData) . ' active loans',
                            'text-orange-700',
                            'fas fa-file-invoice-dollar text-orange-600',
                            'border-orange-200 hover:border-orange-400'
                        );
                    ?>
                </div>

                <!-- Mood Meter Card (Full Width) -->
                <div class="bg-gradient-to-br <?php echo $mood['color']; ?> rounded-lg p-4 sm:p-6 shadow-lg border-2 <?php echo $mood['borderColor']; ?>">
                    <div class="flex flex-col sm:flex-row items-start justify-between mb-3 gap-3">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase mb-1">Your Spending Mood</p>
                            <div class="flex items-center gap-2 sm:gap-3">
                                <span class="text-4xl sm:text-5xl"><?php echo $mood['emoji']; ?></span>
                                <div>
                                    <p class="text-lg sm:text-2xl font-bold <?php echo $mood['textColor']; ?>"><?php echo $mood['text']; ?></p>
                                    <p class="text-xs sm:text-sm <?php echo $mood['textColor']; ?> opacity-75">
                                        <?php echo $mood['insight']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <span class="<?php echo $mood['badgeColor']; ?> px-2 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap"><?php echo round($spendingPercentage); ?>% Spent</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <?php 
                            $progressColor = $spendingPercentage <= 40 ? 'bg-green-500' : ($spendingPercentage <= 70 ? 'bg-amber-500' : 'bg-red-500');
                        ?>
                        <div class="<?php echo $progressColor; ?> h-3 rounded-full transition-all" style="width: <?php echo min($spendingPercentage, 100); ?>%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


                <div class="bg-white rounded-lg p-4 shadow-lg">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-4">Budget vs Actual</h3>
                        <div class="space-y-4">
                            <?php if (!empty($budgetData)): ?>
                                <?php foreach ($budgetData as $item): ?>
                                    <div class="group">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-semibold text-sm text-gray-900"><?php echo $item['category']; ?></span>
                                            <div class="flex items-center gap-3">
                                                <span class="text-xs font-semibold text-gray-700">₱<?php echo number_format($item['spent'], 2); ?> / ₱<?php echo number_format($item['budget'], 2); ?></span>
                                                <button class="edit-budget-btn text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 py-1 rounded text-xs font-medium transition" 
                                                        title="Edit"
                                                        data-category="<?php echo htmlspecialchars($item['category']); ?>" 
                                                        data-budget="<?php echo $item['budget']; ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </div>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="<?php echo $item['color']; ?> h-2 rounded-full" style="width: <?php echo min($item['percentage'], 100); ?>%"></div>
                                        </div>
                                        <div class="flex justify-between items-center mt-1 text-[11px] text-gray-600">
                                            <span class="<?php echo ($item['percentage'] >= 80 ? 'text-yellow-600' : 'text-green-600'); ?> font-semibold"><?php echo round($item['percentage']); ?>% used</span>
                                            <span>Remaining: ₱<?php echo number_format(max($item['budget'] - $item['spent'], 0), 2); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                                    <i class="fas fa-chart-pie text-3xl mb-2 opacity-50"></i>
                                    <p class="text-sm font-medium">No budgets set yet</p>
                                    <p class="text-xs text-gray-400 mt-1">Create a budget to track your spending</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-lg">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-4">Expense Tracker</h3>
                        
                        <div class="bg-gray-50 p-3 rounded-lg mb-4">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold text-gray-700">Range</label>
                                <select id="cashflowRange" class="bg-white text-gray-900 text-xs border border-gray-300 rounded px-2 py-1">
                                    <option value="weekly" selected>This Week</option>
                                    <option value="monthly">This Month</option>
                                </select>
                            </div>
                        </div>

                        <canvas id="cashflowChart" height="200"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-lg overflow-hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-base font-bold text-gray-900">Recent Transactions</h3>
                        <button id="exportTransactionsBtn" class="text-blue-600 hover:text-blue-700 font-medium text-xs flex items-center gap-1"><i class="fas fa-download"></i> Export</button>
                    </div>

                    <!-- Filter Bar -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-4 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Preset Range Filter -->
                            <div>
                                <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs font-semibold text-gray-700">Range</label>
                                        <select id="filterRange" class="bg-white text-gray-900 text-xs border border-gray-300 rounded px-2 py-1">
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly" selected>Monthly</option>
                                            <option value="yearly">Yearly</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="transactionCustomDates" class="hidden grid grid-cols-2 gap-2">
                                    <input type="date" id="filterStartDate" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white text-gray-900 placeholder-gray-500" placeholder="From">
                                    <input type="date" id="filterEndDate" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white text-gray-900 placeholder-gray-500" placeholder="To">
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-semibold text-gray-700">Categories</label>
                                <div class="flex gap-1 flex-wrap">
                                    <?php
                                        // Extract unique categories from transactions
                                        $categories = array_unique(array_column($recentTransactions, 'category'));
                                        $categories = array_map('trim', $categories);
                                        sort($categories);
                                        foreach ($categories as $category):
                                    ?>
                                        <button
                                            type="button"
                                            class="category-filter-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 transition hover:border-blue-500"
                                            data-category="<?php echo htmlspecialchars($category); ?>"
                                        >
                                            <?php echo htmlspecialchars($category); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>
                        <div class="flex items-center gap-3 pt-2 border-t border-gray-300">
                            <button id="resetTransactionBtn" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition">Reset</button>
                            <span class="text-[11px] text-gray-500">Select filters to apply</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-2 px-3 text-gray-600 font-semibold">Date</th>
                                    <th class="text-left py-2 px-3 text-gray-600 font-semibold">Description</th>
                                    <th class="text-left py-2 px-3 text-gray-600 font-semibold">Category</th>
                                    <th class="text-right py-2 px-3 text-gray-600 font-semibold">Amount</th>
                                    <th class="text-center py-2 px-3 text-gray-600 font-semibold">Type</th>
                                    <th class="text-center py-2 px-3 text-gray-600 font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="transactionTableBody">
                                <?php foreach (array_slice($recentTransactions, 0, 5) as $transaction): ?>
                                    <tr class="transaction-row border-b border-gray-100 hover:bg-gray-50 transition" data-date="<?php echo date('Y-m-d', strtotime($transaction['transaction_date'])); ?>" data-category="<?php echo trim($transaction['category']); ?>">
                                        <td class="py-2 px-3 text-gray-900"><?php echo date('M d', strtotime($transaction['transaction_date'])); ?></td>
                                        <td class="py-2 px-3 text-gray-900 font-medium"><?php echo htmlspecialchars($transaction['description'] ?? ''); ?></td>
                                        <td class="py-2 px-3">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium"><?php echo htmlspecialchars($transaction['category']); ?></span>
                                        </td>
                                        <td class="py-2 px-3 text-right font-semibold <?php echo $transaction['type'] === 'Income' ? 'text-green-600' : 'text-gray-900'; ?>">
                                            <?php echo ($transaction['type'] === 'Income' ? '+' : '-') . '₱' . number_format($transaction['amount'], 0); ?>
                                        </td>
                                        <td class="py-2 px-3 text-center text-xs text-gray-600"><?php echo htmlspecialchars($transaction['type']); ?></td>
                                        <td class="py-2 px-3 text-center">
                                            <div class="flex justify-center gap-2">
                                                <button class="edit-transaction-btn text-blue-600 hover:text-blue-700 text-xs font-medium" 
                                                        title="Edit"
                                                        data-transaction='<?php echo htmlspecialchars(json_encode($transaction)); ?>'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="delete-transaction-btn text-red-600 hover:text-red-700 text-xs font-medium" 
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
                    <div class="mt-3 text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-700 font-medium text-xs">View All →</a>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (1 col) - Sidebar -->
            <div class="space-y-4 flex flex-col">
                <!-- Safe Days Card (With Left Border) -->
                <div class="bg-gradient-to-br from-slate-50 to-white rounded-lg p-4 shadow-lg border-l-4 border-highlight order-1 lg:order-1 relative overflow-hidden">
                    <!-- Background accent circle -->
                    <div class="absolute top-0 right-0 w-16 h-16 bg-highlight/5 rounded-full -mr-8 -mt-8"></div>
                    
                    <div class="relative z-10">
                        <p class="text-gray-500 text-xs font-bold mb-3 uppercase flex items-center gap-1.5">
                            <i class="fas fa-calendar-days text-highlight text-sm"></i> Safe Days
                        </p>
                        
                        <div class="flex items-start gap-3">
                            <!-- Large circular display -->
                            <div class="flex flex-col items-center flex-shrink-0">
                                <div class="<?php echo $safeDays < 5 ? 'bg-gradient-to-br from-yellow-200 to-yellow-100 shadow-lg shadow-yellow-200/50' : 'bg-gradient-to-br from-green-200 to-green-100 shadow-lg shadow-green-200/50'; ?> rounded-full w-16 h-16 flex items-center justify-center ring-3 <?php echo $safeDays < 5 ? 'ring-yellow-100' : 'ring-green-100'; ?>">
                                    <span class="<?php echo $safeDays < 5 ? 'text-yellow-700' : 'text-green-700'; ?> text-3xl font-black"><?php echo $safeDays; ?></span>
                                </div>
                                <p class="text-[8px] font-bold text-gray-500 mt-1.5 uppercase tracking-wider">Days</p>
                            </div>
                            
                            <!-- Context information -->
                            <div class="flex flex-col justify-center flex-1 pt-0.5">
                                <p class="text-xs font-semibold text-gray-900 mb-0.5">Financial Runway</p>
                                <p class="text-[11px] text-gray-600 leading-tight mb-2">Until balance depletes</p>
                                
                                <!-- Status indicator -->
                                <div class="flex items-center gap-1.5">
                                    <?php if ($safeDays < 5): ?>
                                        <div class="flex items-center gap-1 bg-red-50 border border-red-200 rounded-full px-2 py-0.5">
                                            <i class="fas fa-exclamation-circle text-red-600 text-[10px]"></i>
                                            <span class="text-[10px] font-semibold text-red-700">Critical</span>
                                        </div>
                                    <?php elseif ($safeDays < 15): ?>
                                        <div class="flex items-center gap-1 bg-yellow-50 border border-yellow-200 rounded-full px-2 py-0.5">
                                            <i class="fas fa-triangle-exclamation text-yellow-600 text-[10px]"></i>
                                            <span class="text-[10px] font-semibold text-yellow-700">Warning</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex items-center gap-1 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
                                            <i class="fas fa-check-circle text-green-600 text-[10px]"></i>
                                            <span class="text-[10px] font-semibold text-green-700">Healthy</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Burn rate info -->
                        <?php if ($financialData['averageDailySpending'] > 0): ?>
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <p class="text-[8px] font-bold text-gray-500 uppercase mb-1">Burn Rate</p>
                                <p class="text-xs font-semibold text-gray-900">₱<?php echo number_format($financialData['averageDailySpending'], 0); ?><span class="text-[10px] font-normal text-gray-500">/day</span></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg p-4 shadow-lg order-3 lg:order-2">
                    <p class="text-gray-500 text-xs font-bold mb-3 uppercase">Quick Actions</p>
                    <div class="flex flex-col space-y-2">
                        <button class="open-modal-btn w-full border-2 border-blue-600 text-blue-600 hover:bg-blue-50 hover:shadow-lg hover:border-blue-700 hover:scale-105 py-2 rounded text-xs font-bold transition-all duration-200 ease-out flex items-center justify-center gap-1" data-modal="addExpenseModal"><i class="fas fa-plus"></i> Expense</button>
                        <button class="open-modal-btn w-full border-2 border-teal-600 text-teal-600 hover:bg-teal-50 hover:shadow-lg hover:border-teal-700 hover:scale-105 py-2 rounded text-xs font-bold transition-all duration-200 ease-out flex items-center justify-center gap-1" data-modal="addIncomeModal"><i class="fas fa-plus"></i> Income</button>
                        <button class="open-modal-btn w-full border-2 border-orange-600 text-orange-600 hover:bg-orange-50 hover:shadow-lg hover:border-orange-700 hover:scale-105 py-2 rounded text-xs font-bold transition-all duration-200 ease-out flex items-center justify-center gap-1" data-modal="addUtangModal"><i class="fas fa-hand-holding-heart"></i> Add Utang</button>
                        <button class="open-modal-btn w-full border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 hover:shadow-lg hover:border-indigo-700 hover:scale-105 py-2 rounded text-xs font-bold transition-all duration-200 ease-out flex items-center justify-center gap-1" data-modal="setBudgetModal"><i class="fas fa-sliders"></i> Budget</button>
                    </div>
                </div>

                <!-- Savings Goals Card (Tabbed Carousel) -->
                <div class="bg-white rounded-lg p-4 shadow-lg order-2 lg:order-3">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase">Goals</p>
                        </div>
                        <!-- Goal Navigation Arrows & Add Button -->
                        <div class="flex items-center gap-2">
                            <?php if (count($savingsGoals) > 1): ?>
                                <button id="goalPrevBtn" class="goal-nav-btn w-6 h-6 rounded-full border-2 border-gray-600 text-gray-600 bg-gray-100 hover:bg-transparent hover:shadow-lg hover:border-gray-700 hover:scale-105 flex items-center justify-center text-xs font-bold transition-all duration-200 ease-out" aria-label="Previous goal">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="goalNextBtn" class="goal-nav-btn w-6 h-6 rounded-full border-2 border-gray-600 text-gray-600 bg-gray-100 hover:bg-transparent hover:shadow-lg hover:border-gray-700 hover:scale-105 flex items-center justify-center text-xs font-bold transition-all duration-200 ease-out" aria-label="Next goal">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            <?php endif; ?>
                            <button class="open-modal-btn text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-full w-6 h-6 flex items-center justify-center transition text-xs font-bold" title="Add goal" data-modal="addGoalModal"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    
                    <!-- Goals Carousel Container -->
                    <div class="relative overflow-hidden">
                        <?php if (empty($savingsGoals)): ?>
                            <p class="text-xs text-gray-500">No goals yet.</p>
                        <?php else: ?>
                            <?php foreach ($savingsGoals as $idx => $goal): ?>
                                <?php
                                    // Guard against zero targets and clamp to sensible bounds
                                    $progress = ($goal['target'] > 0) ? ($goal['saved'] / $goal['target']) * 100 : 0;
                                    $progress = max(0, $progress);
                                    $progressWidth = min($progress, 100);
                                    $progressDisplay = round($progress);
                                ?>
                                <div class="goal-slide <?php echo $idx === 0 ? '' : 'hidden'; ?>" data-goal="<?php echo $idx; ?>">
                                    <div class="flex items-start justify-between mb-2">
                                        <p class="text-base font-bold text-gray-900"><?php echo $goal['name']; ?></p>
                                        <i class="fas fa-bullseye text-purple-600 text-lg"></i>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div class="h-2 rounded-full" style="width: <?php echo $progressWidth; ?>%; background: linear-gradient(90deg, #0f3460 0%, #1a1a2e 100%);"></div>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-900">₱<?php echo number_format($goal['saved'], 0); ?> / ₱<?php echo number_format($goal['target'], 0); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo $progressDisplay; ?>% Complete</p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recurring Subscriptions -->
                <div class="bg-white rounded-lg p-4 shadow-lg order-4 lg:order-4">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-gray-500 text-xs font-bold uppercase">Subscriptions</p>
                        <button class="open-modal-btn text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-full w-6 h-6 flex items-center justify-center transition text-xs font-bold" title="Add subscription" data-modal="addSubscriptionModal"><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="space-y-2">
                        <?php if (empty($subscriptions)): ?>
                            <p class="text-xs text-gray-500">No subscriptions yet.</p>
                        <?php else: ?>
                            <?php foreach ($subscriptions as $sub): ?>
                                <?php
                                    $serviceNameLower = strtolower($sub['name']);
                                    if (str_contains($serviceNameLower, 'spotify')) {
                                        $serviceIcon = 'fab fa-spotify text-green-600';
                                    } elseif (str_contains($serviceNameLower, 'netflix')) {
                                        $serviceIcon = 'fas fa-play text-red-600';
                                    } elseif (str_contains($serviceNameLower, 'youtube')) {
                                        $serviceIcon = 'fab fa-youtube text-red-600';
                                    } elseif (str_contains($serviceNameLower, 'amazon')) {
                                        $serviceIcon = 'fas fa-box text-amber-600';
                                    } elseif (str_contains($serviceNameLower, 'adobe')) {
                                        $serviceIcon = 'fas fa-palette text-rose-600';
                                    } elseif (str_contains($serviceNameLower, 'microsoft') || str_contains($serviceNameLower, '365')) {
                                        $serviceIcon = 'fas fa-window-maximize text-blue-600';
                                    } elseif (str_contains($serviceNameLower, 'dropbox')) {
                                        $serviceIcon = 'fab fa-dropbox text-blue-500';
                                    } elseif (str_contains($serviceNameLower, 'canva')) {
                                        $serviceIcon = 'fas fa-pen text-sky-500';
                                    } elseif (str_contains($serviceNameLower, 'chatgpt')) {
                                        $serviceIcon = 'fas fa-brain text-emerald-600';
                                    } elseif (str_contains($serviceNameLower, 'notion')) {
                                        $serviceIcon = 'fas fa-book text-gray-700';
                                    } else {
                                        $serviceIcon = 'fas fa-credit-card text-gray-500';
                                    }

                                    // Check if subscription is due soon (within 48 hours)
                                    $today = (int) date('d');
                                    $tomorrow = (int) date('d', strtotime('+1 day'));
                                    $dayAfter = (int) date('d', strtotime('+2 days'));
                                    $dueSoon = ($sub['dueDate'] == $today || $sub['dueDate'] == $tomorrow || $sub['dueDate'] == $dayAfter) && $sub['status'] === 'Active';
                                ?>
                                <div class="flex justify-between items-center py-1 border-b border-gray-100 last:border-b-0">
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <i class="<?php echo $serviceIcon; ?> text-base flex-shrink-0"></i>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-gray-900 truncate"><?php echo $sub['name']; ?></p>
                                            <p class="text-xs text-gray-500">₱<?php echo number_format($sub['amount'], 0); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0 ml-2">
                                        <?php if ($dueSoon): ?>
                                            <span class="relative inline-block group" title="Due Soon">
                                                <i class="fas fa-circle text-orange-500 text-[8px]"></i>
                                                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-orange-700 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Due Soon</span>
                                            </span>
                                        <?php endif; ?>
                                        <span class="<?php echo ($sub['status'] === 'Active') ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?> text-xs font-semibold px-1.5 py-0.5 rounded-full flex-shrink-0">
                                            <?php echo substr($sub['status'], 0, 3); ?>
                                        </span>
                                        <div class="relative group">
                                            <button class="text-blue-600 hover:text-blue-700 text-xs p-1" title="Actions">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="absolute right-0 mt-1 w-32 bg-white border border-gray-200 rounded-lg shadow-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                                <button class="edit-subscription-btn block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg"
                                                        data-name="<?php echo htmlspecialchars($sub['name']); ?>"
                                                        data-amount="<?php echo $sub['amount']; ?>"
                                                        data-due="<?php echo $sub['dueDate']; ?>"
                                                        data-status="<?php echo htmlspecialchars($sub['status']); ?>"
                                                        data-last-payment="<?php echo htmlspecialchars($sub['last_payment_date'] ?? ''); ?>">
                                                    <i class="fas fa-edit mr-2"></i>Edit
                                                </button>
                                                <button class="delete-subscription-btn block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-b-lg border-t border-gray-100"
                                                        data-name="<?php echo htmlspecialchars($sub['name']); ?>">
                                                    <i class="fas fa-trash mr-2"></i>Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <p class="text-xs text-gray-600 mt-2">Total: ₱<?php echo number_format(array_sum(array_column($subscriptions, 'amount')), 0); ?>/month</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tip Card -->
                <?php
                    // Determine card styling based on AI mood
                    $tipCardClass = match($ai_mood) {
                        'danger' => 'from-red-100 to-red-50 border-red-300',
                        'warning' => 'from-amber-100 to-amber-50 border-amber-300',
                        'good' => 'from-green-100 to-green-50 border-green-300',
                        default => 'from-blue-100 to-blue-50 border-blue-300',
                    };
                    $tipTextClass = match($ai_mood) {
                        'danger' => 'text-red-900',
                        'warning' => 'text-amber-900',
                        'good' => 'text-green-900',
                        default => 'text-blue-900',
                    };
                    $tipIconClass = match($ai_mood) {
                        'danger' => 'text-red-600',
                        'warning' => 'text-amber-600',
                        'good' => 'text-green-600',
                        default => 'text-blue-600',
                    };
                ?>
                <div class="bg-gradient-to-br <?php echo $tipCardClass; ?> rounded-lg p-4 border order-5 lg:order-5">
                    <h4 class="<?php echo $tipTextClass; ?> font-bold text-xs mb-2 flex items-center gap-1"><i class="fas fa-lightbulb <?php echo $tipIconClass; ?>"></i> AI Insight</h4>
                    <p class="<?php echo $tipTextClass; ?> text-xs leading-relaxed"><?php echo $ai_message; ?></p>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php include 'includes/modals.php'; ?>
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/dashboard.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/waisbot.js?v=<?php echo time(); ?>"></script>
    <button class="waisbot-fab" aria-label="Open WaisBot">
        <span class="waisbot-robot-icon">🤖</span>
    </button>

<div id="waisBotWindow" class="hidden waisbot-window" role="dialog" aria-label="WaisBot">
    
    <div class="waisbot-header">
        <div class="waisbot-title-wrap">
            <div class="waisbot-icon">
                <span class="waisbot-robot-icon">🤖</span>
            </div>
            <div>
                <h3 class="waisbot-title">WaisBot</h3>
                <p class="waisbot-subtitle">Tutulungan ka maging WAIS</p>
            </div>
        </div>
        <button class="waisbot-close" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div id="chatBody" class="waisbot-body scrollbar-hide">
        <div class="chat-message chat-message--bot">
            <div class="chat-bubble chat-bubble--bot">
                <p class="chat-text">Hello! I'm WaisBot, your AI financial assistant. How can I help you today? 🤖💰</p>
                <div class="waisbot-recommended">
                    <button class="waisbot-chip" data-question="How can I save money effectively?">
                        <span class="waisbot-chip-icon">💰</span>
                        <span>Save Money</span>
                    </button>
                    <button class="waisbot-chip" data-question="Analyze my spending patterns">
                        <span class="waisbot-chip-icon">📊</span>
                        <span>Analyze Spending</span>
                    </button>
                    <button class="waisbot-chip" data-question="Give me budgeting tips">
                        <span class="waisbot-chip-icon">📝</span>
                        <span>Budget Tips</span>
                    </button>
                    <button class="waisbot-chip" data-question="How to pay off loans faster?">
                        <span class="waisbot-chip-icon">🎯</span>
                        <span>Pay Loans</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="waisbot-input-row font-color-white">
        <input type="text" id="userMessage" class="waisbot-input" placeholder="Ask for advice...">
        <button class="waisbot-send" aria-label="Send">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

</body>
