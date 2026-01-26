<?php
    include_once 'includes/auth_check.php';
    $username = $_SESSION['username'] ?? 'Charles';
    include 'includes/modals.php';
    // Sample financial data
    $financialData = [
        'currentBalance' => 15250.50,
        'totalIncome' => 25000,
        'totalExpenses' => 9749.50,
        'averageDailySpending' => 312.22,
        'monthlyBudgetLimit' => 10000,
    ];

    // Calculate safe days
    $safeDays = $financialData['currentBalance'] > 0 
        ? floor($financialData['currentBalance'] / $financialData['averageDailySpending']) 
        : 0;

    // Expense categories breakdown (sample data)
    $expenseBreakdown = [
        ['category' => 'Food', 'amount' => 4500, 'percentage' => 46],
        ['category' => 'Transport', 'amount' => 2200, 'percentage' => 23],
        ['category' => 'School', 'amount' => 1800, 'percentage' => 18],
        ['category' => 'Leisure', 'amount' => 1249.50, 'percentage' => 13],
    ];

    // Last 7 days cash flow (sample data)
    $cashflowDays = [
        ['day' => 'Mon', 'income' => 5000, 'expense' => 1200],
        ['day' => 'Tue', 'income' => 0, 'expense' => 950],
        ['day' => 'Wed', 'income' => 0, 'expense' => 1100],
        ['day' => 'Thu', 'income' => 0, 'expense' => 800],
        ['day' => 'Fri', 'income' => 20000, 'expense' => 2500],
        ['day' => 'Sat', 'income' => 0, 'expense' => 2000],
        ['day' => 'Sun', 'income' => 0, 'expense' => 1199.50],
    ];

    // Recent transactions (sample data)
    $recentTransactions = [
        ['date' => '2026-01-27', 'description' => 'Jollibee Burger', 'category' => 'Food', 'amount' => 189.50, 'type' => 'expense', 'method' => 'Cash'],
        ['date' => '2026-01-26', 'description' => 'Jeepney Fare', 'category' => 'Transport', 'amount' => 25.00, 'type' => 'expense', 'method' => 'Cash'],
        ['date' => '2026-01-26', 'description' => 'Monthly Allowance', 'category' => 'Income', 'amount' => 20000.00, 'type' => 'income', 'method' => 'Bank Transfer'],
        ['date' => '2026-01-25', 'description' => 'School Supplies', 'category' => 'School', 'amount' => 450.00, 'type' => 'expense', 'method' => 'G-Cash'],
        ['date' => '2026-01-25', 'description' => 'Cinema Ticket', 'category' => 'Entertainment', 'amount' => 250.00, 'type' => 'expense', 'method' => 'Maya'],
        ['date' => '2026-01-24', 'description' => 'Internet Bill', 'category' => 'Utilities', 'amount' => 1500.00, 'type' => 'expense', 'method' => 'Bank Transfer'],
        ['date' => '2026-01-24', 'description' => 'Medical Checkup', 'category' => 'Health', 'amount' => 800.00, 'type' => 'expense', 'method' => 'Cash'],
        ['date' => '2026-01-23', 'description' => 'Monthly Savings', 'category' => 'Savings', 'amount' => 5000.00, 'type' => 'expense', 'method' => 'Bank Transfer'],
    ];

    // Recurring subscriptions data
    $subscriptions = [
        ['name' => 'Spotify', 'amount' => 149, 'icon' => '🎵', 'dueDate' => 15],
        ['name' => 'Netflix', 'amount' => 249, 'icon' => '📺', 'dueDate' => 20],
    ];

    // Utang Tracker (Debts/Loans) - Sample data
    $utangData = [
        ['creditor' => 'Best Friend', 'amount' => 2500, 'dueDate' => '2026-02-15'],
        ['creditor' => 'Mom', 'amount' => 5000, 'dueDate' => '2026-03-01'],
    ];
    $totalUtang = array_sum(array_column($utangData, 'amount'));

    // Savings Goals (Multiple) - Sample data
    $savingsGoals = [
        ['name' => 'RTX 3060', 'icon' => 'fas fa-gamepad', 'saved' => 12000, 'target' => 20000],
        ['name' => 'Study Abroad', 'icon' => 'fas fa-plane', 'saved' => 45000, 'target' => 100000],
        ['name' => 'Emergency Fund', 'icon' => 'fas fa-umbrella', 'saved' => 8500, 'target' => 25000],
    ];

    // Reusable stat card function
    function renderStatCard($title, $value, $subtitle, $bgColor, $iconClass) {
        echo '<div class="bg-white rounded-lg p-4 shadow-lg card-hover">';
        echo '<div class="flex items-start justify-between">';
        echo '<div>';
        echo "<p class='text-gray-500 text-xs font-semibold mb-2 uppercase'>$title</p>";
        echo "<p class='text-lg sm:text-2xl font-bold $bgColor mb-1'>$value</p>";
        echo "<p class='text-xs text-gray-500'>$subtitle</p>";
        echo '</div>';
        echo "<i class='$iconClass text-lg sm:text-2xl opacity-80'></i>";
        echo '</div></div>';
    }

    // Determine balance color
    $balanceClass = $financialData['currentBalance'] >= 0 ? 'text-green-600' : 'text-red-600';
    $balanceStatus = $financialData['currentBalance'] >= 0 ? 'Positive' : 'Negative';

    // Mood Meter Logic - Based on spending percentage
    $spendingPercentage = ($financialData['totalExpenses'] / $financialData['totalIncome']) * 100;
    
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PesoWais Dashboard</title>
    <script src="assets/js/tailwind-config.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <nav class="bg-white/80 backdrop-blur-sm border-b border-gray-200 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-wallet text-highlight text-2xl"></i>
                        <h1 class="text-2xl font-bold text-primary">PesoWais</h1>
                    </div>
                    
                    <!-- Navigation Menu (Desktop) -->
                    <div class="hidden md:flex items-center gap-1">
                        <a href="#" class="text-gray-600 hover:text-highlight hover:bg-gray-50 px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                        <a href="#" class="text-gray-600 hover:text-highlight hover:bg-gray-50 px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                            <i class="fas fa-exchange-alt"></i> Transactions
                        </a>
                        <a href="#" class="text-gray-600 hover:text-highlight hover:bg-gray-50 px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                            <i class="fas fa-bullseye"></i> Goals
                        </a>
                        <a href="#" class="text-gray-600 hover:text-highlight hover:bg-gray-50 px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                            <i class="fas fa-hand-holding-heart"></i> Utang
                        </a>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="flex items-center gap-4">
                    <!-- Notifications (Hidden for now) -->
                    <button class="text-gray-600 hover:text-highlight hover:bg-gray-100 p-2 rounded-lg transition relative hidden sm:block">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-400 rounded-full"></span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($username); ?></p>
                            <p class="text-xs text-gray-500">Student</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-highlight flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <a href="logic/logout.php" class="text-gray-500 hover:text-red-600 transition" title="Logout">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="md:hidden text-gray-600 hover:text-highlight p-2 rounded-lg transition">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar Menu -->
        <div id="mobileSidebar" class="hidden md:hidden border-t border-gray-200 bg-white max-h-[calc(100vh-64px)] overflow-y-auto">
            <div class="px-4 py-3 space-y-1">
                <a href="#" class="block px-3 py-2 rounded-lg text-gray-600 hover:text-highlight hover:bg-gray-50 text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="#" class="block px-3 py-2 rounded-lg text-gray-600 hover:text-highlight hover:bg-gray-50 text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-exchange-alt"></i> Transactions
                </a>
                <a href="#" class="block px-3 py-2 rounded-lg text-gray-600 hover:text-highlight hover:bg-gray-50 text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-bullseye"></i> Goals
                </a>
                <a href="#" class="block px-3 py-2 rounded-lg text-gray-600 hover:text-highlight hover:bg-gray-50 text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-hand-holding-heart"></i> Utang
                </a>
            </div>
            <hr class="border-gray-200">
            <div class="px-4 py-3 space-y-1">
                <div class="px-3 py-2 flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-highlight flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($username); ?></p>
                        <p class="text-xs text-gray-500">Student</p>
                    </div>
                </div>
                <a href="logic/logout.php" class="block px-3 py-2 rounded-lg text-gray-600 hover:text-red-600 hover:bg-gray-50 text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

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
                            'Total Debt',
                            '₱' . number_format($totalUtang, 2),
                            count($utangData) . ' loans',
                            'text-orange-600',
                            'fas fa-hand-holding-heart text-orange-600'
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
                            <?php
                                $budgetData = [
                                    ['category' => 'Food', 'spent' => 4500, 'budget' => 5000, 'color' => 'bg-red-400'],
                                    ['category' => 'Transport', 'spent' => 2200, 'budget' => 2500, 'color' => 'bg-green-400'],
                                    ['category' => 'School', 'spent' => 1800, 'budget' => 2000, 'color' => 'bg-green-400'],
                                    ['category' => 'Leisure', 'spent' => 1249.50, 'budget' => 1500, 'color' => 'bg-green-400'],
                                ];
                                foreach ($budgetData as $item):
                                    $percentage = ($item['spent'] / $item['budget']) * 100;
                                    $statusColor = $percentage >= 80 ? 'text-yellow-600' : 'text-green-600';
                            ?>
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-semibold text-sm text-gray-900"><?php echo $item['category']; ?></span>
                                        <span class="text-xs <?php echo $statusColor; ?>">₱<?php echo number_format($item['spent'], 0); ?></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="<?php echo $item['color']; ?> h-2 rounded-full" style="width: <?php echo min($percentage, 100); ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-lg">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-4">Cash Flow</h3>
                        
                        <div class="bg-gray-50 p-3 rounded-lg mb-4 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-semibold text-gray-700">Range</label>
                                    <select id="cashflowRange" class="text-xs border border-gray-300 rounded px-2 py-1">
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>
                                <button class="text-xs text-blue-600 hover:text-blue-700 font-medium" onclick="resetCashflowFilters()">Reset</button>
                            </div>
                            <div id="cashflowCustomDates" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <input type="date" id="cashflowStartDate" class="text-xs border border-gray-300 rounded px-2 py-1" placeholder="From">
                                <input type="date" id="cashflowEndDate" class="text-xs border border-gray-300 rounded px-2 py-1" placeholder="To">
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
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                                    <input type="date" id="filterStartDate" class="text-xs border border-gray-300 rounded px-2 py-1" placeholder="From">
                                    <input type="date" id="filterEndDate" class="text-xs border border-gray-300 rounded px-2 py-1" placeholder="To">
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

                            <!-- Method Filter -->
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-semibold text-gray-700">Payment Method</label>
                                <div class="flex gap-1 flex-wrap">
                                    <?php
                                        // Extract unique methods from transactions
                                        $methods = array_unique(array_column($recentTransactions, 'method'));
                                        sort($methods);
                                        foreach ($methods as $method):
                                    ?>
                                        <button
                                            type="button"
                                            class="method-filter-btn px-2 py-1 rounded text-xs font-medium border border-gray-300 text-gray-700 transition hover:border-green-500"
                                            data-method="<?php echo htmlspecialchars($method); ?>"
                                        >
                                            <?php echo htmlspecialchars($method); ?>
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
                                    <th class="text-center py-2 px-3 text-gray-600 font-semibold">Method</th>
                                </tr>
                            </thead>
                            <tbody id="transactionTableBody">
                                <?php foreach (array_slice($recentTransactions, 0, 5) as $transaction): ?>
                                    <tr class="transaction-row border-b border-gray-100 hover:bg-gray-50 transition" data-date="<?php echo $transaction['date']; ?>" data-category="<?php echo $transaction['category']; ?>" data-method="<?php echo $transaction['method']; ?>">
                                        <td class="py-2 px-3 text-gray-900"><?php echo date('M d', strtotime($transaction['date'])); ?></td>
                                        <td class="py-2 px-3 text-gray-900 font-medium"><?php echo $transaction['description']; ?></td>
                                        <td class="py-2 px-3">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium"><?php echo $transaction['category']; ?></span>
                                        </td>
                                        <td class="py-2 px-3 text-right font-semibold <?php echo $transaction['type'] === 'income' ? 'text-green-600' : 'text-gray-900'; ?>">
                                            <?php echo ($transaction['type'] === 'income' ? '+' : '-') . '₱' . number_format($transaction['amount'], 0); ?>
                                        </td>
                                        <td class="py-2 px-3 text-center text-xs text-gray-600"><?php echo $transaction['method']; ?></td>
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
                                    <i class="<?php echo $goal['icon']; ?> text-purple-600 text-lg"></i>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div class="h-2 rounded-full" style="width: <?php echo $progressWidth; ?>%; background: linear-gradient(90deg, #0f3460 0%, #1a1a2e 100%);"></div>
                                </div>
                                <p class="text-xs font-semibold text-gray-900">₱<?php echo number_format($goal['saved'], 0); ?> / ₱<?php echo number_format($goal['target'], 0); ?></p>
                                <p class="text-xs text-gray-500"><?php echo $progressDisplay; ?>% Complete</p>
                            </div>
                        <?php endforeach; ?>
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
                        <button class="text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-full w-6 h-6 flex items-center justify-center transition text-xs font-bold" title="Add subscription"><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="space-y-2">
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
                    </div>
                </div>

                <!-- Tip Card -->
                <div class="bg-gradient-to-br from-amber-100 to-amber-50 rounded-lg p-4 border border-amber-300">
                    <h4 class="text-amber-900 font-bold text-xs mb-2 flex items-center gap-1"><i class="fas fa-lightbulb text-amber-600"></i> Smart Tip</h4>
                    <p class="text-amber-800 text-xs leading-relaxed">Packed lunch saves ₱150/day = ₱4,500/month extra!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center text-xs">
                <p class="text-gray-600">© 2024 PesoWais. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-600 hover:text-gray-900">Privacy Policy</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>window.cashflowData = <?php echo json_encode($cashflowDays); ?>;</script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>