<?php
    include  'includes/connection.php';
    include 'includes/auth_check.php';

    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? 'User';

    // Financial totals
    $incomeStmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'Income'");
    $incomeStmt->bind_param("i", $user_id);
    $incomeStmt->execute();
    $incomeResult = $incomeStmt->get_result()->fetch_assoc();
    $totalIncome = (float) ($incomeResult['total'] ?? 0);
    $incomeStmt->close();

    $expenseStmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'Expense'");
    $expenseStmt->bind_param("i", $user_id);
    $expenseStmt->execute();
    $expenseResult = $expenseStmt->get_result()->fetch_assoc();
    $totalExpenses = (float) ($expenseResult['total'] ?? 0);
    $expenseStmt->close();

    $current_balance = $totalIncome - $totalExpenses;

    // Average daily spending (last 30 days)
    $avgStmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'Expense' AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $avgStmt->bind_param("i", $user_id);
    $avgStmt->execute();
    $avgResult = $avgStmt->get_result()->fetch_assoc();
    $last30Expenses = (float) ($avgResult['total'] ?? 0);
    $avgStmt->close();

    $averageDailySpending = $last30Expenses > 0 ? ($last30Expenses / 30) : 0;

    $financialData = [
        'currentBalance' => $current_balance,
        'totalIncome' => $totalIncome,
        'totalExpenses' => $totalExpenses,
        'averageDailySpending' => $averageDailySpending,
    ];

    // Calculate safe days
    $safeDays = ($financialData['currentBalance'] > 0 && $financialData['averageDailySpending'] > 0)
        ? floor($financialData['currentBalance'] / $financialData['averageDailySpending'])
        : 0;

    // Recent transactions (DB)
    $recentTransactions = [];
    $txnStmt = $connection->prepare("SELECT transaction_id, transaction_date, description, category, amount, type FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC LIMIT 8");
    $txnStmt->bind_param("i", $user_id);
    $txnStmt->execute();
    $txnResult = $txnStmt->get_result();
    while ($row = $txnResult->fetch_assoc()) {
        $recentTransactions[] = $row;
    }
    $txnStmt->close();

    // Utang Tracker (Debts/Loans)
    $utangData = [];
    $utangStmt = $connection->prepare("SELECT person_name, amount, due_date FROM loans WHERE user_id = ? AND type = 'Payable' AND status = 'Pending' ORDER BY due_date ASC");
    $utangStmt->bind_param("i", $user_id);
    $utangStmt->execute();
    $utangResult = $utangStmt->get_result();
    while ($row = $utangResult->fetch_assoc()) {
        $utangData[] = [
            'creditor' => $row['person_name'],
            'amount' => (float) $row['amount'],
            'dueDate' => $row['due_date'],
        ];
    }
    $utangStmt->close();
    $totalUtang = array_sum(array_column($utangData, 'amount'));

    // Savings Goals (DB)
    $savingsGoals = [];
    $goalStmt = $connection->prepare("SELECT goal_name, current_amount, target_amount, status FROM goals WHERE user_id = ? AND status IN ('Active', 'Achieved') ORDER BY created_at DESC");
    $goalStmt->bind_param("i", $user_id);
    $goalStmt->execute();
    $goalResult = $goalStmt->get_result();
    while ($row = $goalResult->fetch_assoc()) {
        $savingsGoals[] = [
            'name' => $row['goal_name'],
            'saved' => (float) $row['current_amount'],
            'target' => (float) $row['target_amount'],
            'status' => $row['status'],
        ];
    }
    $goalStmt->close();

    // Budget vs Actual (DB)
    $budgetData = [];
    $budgetStmt = $connection->prepare("SELECT category, amount FROM budgets WHERE user_id = ? ORDER BY category ASC");
    $budgetStmt->bind_param("i", $user_id);
    $budgetStmt->execute();
    $budgetResult = $budgetStmt->get_result();
    
    while ($row = $budgetResult->fetch_assoc()) {
        $category = $row['category'];
        $budgetAmount = (float) $row['amount'];
        
        // Get spending for this category (this month)
        $spendingStmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND category = ? AND type = 'Expense' AND MONTH(transaction_date) = MONTH(CURDATE()) AND YEAR(transaction_date) = YEAR(CURDATE())");
        $spendingStmt->bind_param("is", $user_id, $category);
        $spendingStmt->execute();
        $spendingResult = $spendingStmt->get_result()->fetch_assoc();
        $spent = (float) ($spendingResult['total'] ?? 0);
        $spendingStmt->close();
        
        $percentage = $budgetAmount > 0 ? ($spent / $budgetAmount) * 100 : 0;
        $color = $percentage >= 80 ? 'bg-red-400' : 'bg-green-400';
        
        $budgetData[] = [
            'category' => $category,
            'spent' => $spent,
            'budget' => $budgetAmount,
            'percentage' => $percentage,
            'color' => $color,
        ];
    }
    $budgetStmt->close();

    // Recurring subscriptions (not implemented in DB yet)
    $subscriptions = [];

    include 'logic/ai_analysis.php';
    include 'includes/header.php';
    include 'includes/modals.php';

    // Reusable stat card function
    function renderStatCard($title, $value, $subtitle, $bgColor, $iconClass) {
        echo '<div class="bg-white rounded-lg p-4 shadow-lg card-hover">';
        echo '<div class="flex items-start justify-between">';
        echo '<div>';
        echo "<p class='text-gray-500 text-xs font-semibold mb-2 uppercase'>$title</p>";
        echo "<p class='text-lg sm:text-2xl font-bold $bgColor mb-1'>$value</p>";
        echo "<p class='text-xs text-gray-500'>$subtitle</p>";
        echo '</div>';
        echo "<i class='$iconClass text-lg sm:text-2xl -mt-1'></i>";
        echo '</div></div>';
    }

    // Determine balance color
    $balanceClass = $financialData['currentBalance'] >= 0 ? 'text-green-600' : 'text-red-600';
    $balanceStatus = $financialData['currentBalance'] >= 0 ? 'Positive' : 'Negative';

    // Mood Meter Logic - Based on spending percentage
    $spendingPercentage = $financialData['totalIncome'] > 0 
        ? ($financialData['totalExpenses'] / $financialData['totalIncome']) * 100 
        : 0;
    
    if ($spendingPercentage <= 40) {
        // Zone A: Safe
        $moodEmoji = '😎';
        $moodText = 'Chill lang.';
        $moodColor = 'from-green-100 to-green-50';
        $moodBorderColor = 'border-green-300';
        $moodTextColor = 'text-green-900';
        $moodBadgeColor = 'bg-green-200 text-green-800';
    } elseif ($spendingPercentage <= 70) {
        // Zone B: Warning
        $moodEmoji = '🤨';
        $moodText = 'Huy, dahan-dahan.';
        $moodColor = 'from-amber-100 to-amber-50';
        $moodBorderColor = 'border-amber-300';
        $moodTextColor = 'text-amber-900';
        $moodBadgeColor = 'bg-amber-200 text-amber-800';
    } else {
        // Zone C: Danger
        $moodEmoji = '😱';
        $moodText = 'Tigil na, please.';
        $moodColor = 'from-red-100 to-red-50';
        $moodBorderColor = 'border-red-300';
        $moodTextColor = 'text-red-900';
        $moodBadgeColor = 'bg-red-200 text-red-800';
    }
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
                            $balanceClass,
                            'fas fa-wallet text-highlight'
                        );
                        renderStatCard(
                            'Total Income',
                            '₱' . number_format($financialData['totalIncome'], 2),
                            'This month',
                            'text-green-600',
                            'fas fa-arrow-trend-up text-highlight'
                        );
                        renderStatCard(
                            'Total Expenses',
                            '₱' . number_format($financialData['totalExpenses'], 2),
                            'This month',
                            'text-red-600',
                            'fas fa-arrow-trend-down text-red-600'
                        );
                        renderStatCard(
                            'Total Utang',
                            '₱' . number_format($totalUtang, 2),
                            count($utangData) . ' active loans',
                            'text-orange-600',
                            'fas fa-file-invoice-dollar text-orange-600'
                        );
                    ?>
                </div>

                <!-- Mood Meter Card (Full Width) -->
                <div class="bg-gradient-to-br <?php echo $moodColor; ?> rounded-lg p-4 sm:p-6 shadow-lg border-2 <?php echo $moodBorderColor; ?>">
                    <div class="flex flex-col sm:flex-row items-start justify-between mb-3 gap-3">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase mb-1">Your Spending Mood</p>
                            <div class="flex items-center gap-2 sm:gap-3">
                                <span class="text-4xl sm:text-5xl"><?php echo $moodEmoji; ?></span>
                                <div>
                                    <p class="text-lg sm:text-2xl font-bold <?php echo $moodTextColor; ?>"><?php echo $moodText; ?></p>
                                    <p class="text-xs sm:text-sm <?php echo $moodTextColor; ?> opacity-75">
                                        <?php 
                                            if ($spendingPercentage <= 40) {
                                                echo 'You\'re spending smart! Keep it up.';
                                            } elseif ($spendingPercentage <= 70) {
                                                echo 'Monitor your spending closely.';
                                            } else {
                                                echo 'You\'re overspending! Cut back now.';
                                            }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <span class="<?php echo $moodBadgeColor; ?> px-2 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold whitespace-nowrap"><?php echo round($spendingPercentage); ?>% Spent</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <?php 
                            $progressColor = $spendingPercentage <= 40 ? 'bg-green-500' : ($spendingPercentage <= 70 ? 'bg-amber-500' : 'bg-red-500');
                        ?>
                        <div class="<?php echo $progressColor; ?> h-3 rounded-full transition-all" style="width: <?php echo min($spendingPercentage, 100); ?>%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Budget vs Actual -->
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
                                                <button onclick="openEditBudgetModal('<?php echo htmlspecialchars($item['category']); ?>', <?php echo $item['budget']; ?>)" class="text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 py-1 rounded text-xs font-medium transition" title="Edit">
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
                        <button class="text-blue-600 hover:text-blue-700 font-medium text-xs flex items-center gap-1"><i class="fas fa-download"></i> Export</button>
                    </div>

                    <!-- Filter Bar -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-4 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Preset Range Filter -->
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-semibold text-gray-700">Range</label>
                                <select id="filterRange" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="custom">Custom</option>
                                </select>
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
                                    <tr class="transaction-row border-b border-gray-100 hover:bg-gray-50 transition" data-date="<?php echo date('Y-m-d', strtotime($transaction['transaction_date'])); ?>" data-category="<?php echo $transaction['category']; ?>">
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
                                                <button onclick="openEditTransactionModal(<?php echo htmlspecialchars(json_encode($transaction)); ?>)" class="text-blue-600 hover:text-blue-700 text-xs font-medium" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteTransaction(<?php echo $transaction['transaction_id'] ?? 0; ?>)" class="text-red-600 hover:text-red-700 text-xs font-medium" title="Delete">
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
            <div class="space-y-4">
                <!-- Safe Days Card (With Left Border) -->
                <div class="bg-white rounded-lg p-4 shadow-lg border-l-4 border-highlight">
                    <p class="text-gray-500 text-xs font-bold mb-1 uppercase">Safe Days</p>
                    <p class="text-3xl font-bold text-highlight"><?php echo $safeDays; ?></p>
                    <p class="text-xs text-gray-600 mt-1">Days until depleted</p>
                </div>

                <!-- Savings Goals Card (Tabbed Carousel) -->
                <div class="bg-white rounded-lg p-4 shadow-lg">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-gray-500 text-xs font-bold uppercase">Goals</p>
                        </div>
                        <!-- Goal Navigation Arrows -->
                        <div class="flex items-center gap-2">
                            <button id="goalPrevBtn" class="goal-nav-btn w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold" aria-label="Previous goal">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button id="goalNextBtn" class="goal-nav-btn w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold" aria-label="Next goal">
                                <i class="fas fa-chevron-right"></i>
                            </button>
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

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg p-4 shadow-lg">
                    <p class="text-gray-500 text-xs font-bold mb-3 uppercase">Quick Actions</p>
                    <div class="flex flex-col space-y-2">
                        <button onclick="openModal('addExpenseModal')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded text-xs font-medium transition flex items-center justify-center gap-1"><i class="fas fa-plus"></i> Expense</button>
                        <button onclick="openModal('addIncomeModal')" class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2 rounded text-xs font-medium transition flex items-center justify-center gap-1"><i class="fas fa-plus"></i> Income</button>
                        <button onclick="openModal('addUtangModal')" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded text-xs font-medium transition flex items-center justify-center gap-1"><i class="fas fa-hand-holding-heart"></i> Add Utang</button>
                        <button onclick="openModal('setBudgetModal')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded text-xs font-medium transition flex items-center justify-center gap-1"><i class="fas fa-sliders"></i> Budget</button>
                    </div>
                </div>

                <!-- Recurring Subscriptions -->
                <div class="bg-white rounded-lg p-4 shadow-lg">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-gray-500 text-xs font-bold uppercase">Subscriptions</p>
                        <button class="text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-full w-6 h-6 flex items-center justify-center transition text-xs font-bold" title="Add subscription" onclick="openModal('addSubscriptionModal')"><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="space-y-2">
                        <?php if (empty($subscriptions)): ?>
                            <p class="text-xs text-gray-500">No subscriptions yet.</p>
                        <?php else: ?>
                            <?php foreach ($subscriptions as $sub): ?>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                    <div class="flex items-center gap-2">
                                        <i class="<?php echo ($sub['name'] === 'Spotify') ? 'fab fa-spotify text-green-600' : 'fas fa-play text-red-600'; ?> text-base"></i>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-900"><?php echo $sub['name']; ?></p>
                                            <p class="text-xs text-gray-500">Due: <?php echo $sub['dueDate']; ?>th</p>
                                        </div>
                                    </div>
                                    <p class="text-xs font-bold text-gray-900">₱<?php echo number_format($sub['amount'], 0); ?></p>
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
                <div class="bg-gradient-to-br <?php echo $tipCardClass; ?> rounded-lg p-4 border">
                    <h4 class="<?php echo $tipTextClass; ?> font-bold text-xs mb-2 flex items-center gap-1"><i class="fas fa-lightbulb <?php echo $tipIconClass; ?>"></i> AI Insight</h4>
                    <p class="<?php echo $tipTextClass; ?> text-xs leading-relaxed"><?php echo $ai_message; ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/dashboard.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/waisbot.js?v=<?php echo time(); ?>"></script>
    <button onclick="toggleChat()" class="waisbot-fab" aria-label="Open WaisBot">
        <i class="fas fa-robot"></i>
    </button>

<div id="waisBotWindow" class="hidden waisbot-window" role="dialog" aria-label="WaisBot">
    
    <div class="waisbot-header">
        <div class="waisbot-title-wrap">
            <div class="waisbot-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <h3 class="waisbot-title">WaisBot</h3>
                <p class="waisbot-subtitle">Tutulungan ka maging WAIS</p>
            </div>
        </div>
        <button onclick="toggleChat()" class="waisbot-close" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div id="chatBody" class="waisbot-body scrollbar-hide">
        <div class="chat-message chat-message--bot">
            <div class="chat-bubble chat-bubble--bot">
                <p class="chat-text">Hello! I'm WaisBot. Ask me about budgeting, savings, or tips! 🤖💰</p>
            </div>
        </div>
    </div>

    <div class="waisbot-input-row font-color-white">
        <input type="text" id="userMessage" class="waisbot-input" placeholder="Ask for advice..." onkeypress="handleEnter(event)">
        <button onclick="sendMessage()" class="waisbot-send" aria-label="Send">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

</body>
