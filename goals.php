<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'includes/connection.php';
include 'includes/header.php';
include 'includes/modals.php';

// Fetch goals data
include 'logic/fetch_goals_data.php';
?>

<main class="min-h-screen py-6 px-4 md:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Savings Goals</h2>
            <p class="text-xs sm:text-sm text-gray-600">Track and manage your financial goals</p>
        </div>

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mb-6">
            <!-- Total Target Card -->
            <div class="relative bg-white border-2 border-purple-200 rounded-lg p-2 shadow-sm hover:shadow-md hover:border-purple-400 transition-all duration-300 group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-[9px] text-purple-700 font-semibold uppercase tracking-wide">Target</p>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-lg p-1.5 group-hover:border-purple-400 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-bullseye text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-base sm:text-lg font-bold text-purple-700">₱<?php echo number_format($totalTargetAmount, 2); ?></p>
                    <p class="text-xs text-purple-500 mt-0.5"><?php echo $totalGoals; ?> goal(s)</p>
                </div>
            </div>

            <!-- Total Saved Card -->
            <div class="relative bg-white border-2 border-cyan-200 rounded-lg p-2 shadow-sm hover:shadow-md hover:border-cyan-400 transition-all duration-300 group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-[9px] text-cyan-700 font-semibold uppercase tracking-wide">Saved</p>
                        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 border-2 border-cyan-200 rounded-lg p-1.5 group-hover:border-cyan-400 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-piggy-bank text-cyan-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-base sm:text-lg font-bold text-cyan-700">₱<?php echo number_format($totalCurrentAmount, 2); ?></p>
                    <p class="text-xs text-cyan-500 mt-0.5"><?php echo count($activeGoals); ?> active</p>
                </div>
            </div>

            <!-- Progress Card -->
            <div class="relative bg-white border-2 border-amber-200 rounded-lg p-2 shadow-sm hover:shadow-md hover:border-amber-400 transition-all duration-300 group hidden sm:block">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-[9px] text-amber-700 font-semibold uppercase tracking-wide">Progress</p>
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-200 rounded-lg p-1.5 group-hover:border-amber-400 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-chart-line text-amber-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-base sm:text-lg font-bold text-amber-700"><?php echo round($progressPercentage, 1); ?>%</p>
                    <p class="text-xs text-amber-500 mt-0.5"><?php echo count($achievedGoals); ?> achieved</p>
                </div>
            </div>

            <!-- Add Button - Ghost Button -->
            <div class="relative border-2 border-indigo-400 rounded-lg overflow-hidden cursor-pointer group hover:shadow-md transition-all duration-300 open-goal-modal-btn">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-400 to-indigo-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative flex flex-col items-center justify-center p-2 text-indigo-600 hover:text-indigo-700 transition-colors h-full">
                    <span class="material-icons text-2xl sm:text-3xl mb-1 group-hover:scale-125 group-hover:rotate-90 transition-transform duration-300">add_circle_outline</span>
                    <span class="text-xs font-semibold tracking-wide">Add Goal</span>
                </div>
            </div>
        </div>

        <!-- Goals Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
            <!-- Active Goals Section -->
            <section class="lg:col-span-1 bg-white rounded-lg p-4 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-gray-800">Active Goals</h2>
                    <span class="text-xs text-gray-500 font-medium"><?php echo count($activeGoals); ?> items</span>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    <?php if (empty($activeGoals)): ?>
                        <div class="text-center py-6 text-sm text-gray-400">
                            <p>No active goals yet. Create one!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($activeGoals as $goal): 
                            $progress = ($goal['current_amount'] / $goal['target_amount']) * 100;
                            $daysLeft = !empty($goal['deadline']) ? (int)((strtotime($goal['deadline']) - time()) / 86400) : null;
                        ?>
                            <div class="flex flex-col gap-2 bg-indigo-50 border border-indigo-200 rounded-lg p-3 hover:bg-indigo-100 transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 truncate text-sm"><?php echo htmlspecialchars($goal['goal_name']); ?></div>
                                        <div class="text-xs text-gray-600 mt-0.5">
                                            ₱<?php echo number_format($goal['current_amount'], 2); ?> / ₱<?php echo number_format($goal['target_amount'], 2); ?>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-indigo-600"><?php echo round($progress, 0); ?>%</span>
                                        <?php if ($daysLeft !== null): ?>
                                            <div class="text-xs text-gray-500">
                                                <?php if ($daysLeft > 0): ?>
                                                    <i class="far fa-calendar-alt mr-1"></i><?php echo $daysLeft; ?> days
                                                <?php else: ?>
                                                    <span class="text-red-500">Overdue</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-1.5 rounded-full transition-all duration-300" style="width: <?php echo min($progress, 100); ?>%"></div>
                                </div>
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" class="add-money-goal-btn text-green-600 hover:text-green-800 p-1.5 hover:bg-green-100 rounded text-sm" 
                                            title="Add Money"
                                            data-id="<?php echo $goal['goal_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($goal['goal_name'], ENT_QUOTES); ?>"
                                            data-target="<?php echo $goal['target_amount']; ?>"
                                            data-current="<?php echo $goal['current_amount']; ?>">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" class="edit-goal-btn text-blue-600 hover:text-blue-800 p-1.5 hover:bg-blue-100 rounded text-sm" 
                                            title="Edit"
                                            data-id="<?php echo $goal['goal_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($goal['goal_name'], ENT_QUOTES); ?>"
                                            data-target="<?php echo $goal['target_amount']; ?>"
                                            data-deadline="<?php echo $goal['deadline']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="delete-goal-btn text-red-600 hover:text-red-800 p-1.5 hover:bg-red-100 rounded text-sm" 
                                            title="Delete"
                                            data-id="<?php echo $goal['goal_id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Achieved Goals Section -->
            <section class="lg:col-span-1 bg-white rounded-lg p-4 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-gray-800">Achieved Goals</h2>
                    <span class="text-xs text-gray-500 font-medium"><?php echo count($achievedGoals); ?> items</span>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    <?php if (empty($achievedGoals)): ?>
                        <div class="text-center py-6 text-sm text-gray-400">
                            <p>No achieved goals yet. Keep saving!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($achievedGoals as $goal): ?>
                            <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-lg p-3 hover:bg-emerald-100 transition-all">
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold text-gray-700 truncate text-sm"><?php echo htmlspecialchars($goal['goal_name']); ?></span>
                                    <div class="text-xs text-gray-600 mt-0.5">₱<?php echo number_format($goal['target_amount'], 2); ?> • <?php echo date('M d, Y', strtotime($goal['created_at'])); ?></div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center gap-1 text-emerald-600 text-sm font-semibold">
                                        <i class="fas fa-check-circle"></i> Achieved
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</main>

<script src="assets/js/goals.js"></script>

<?php include 'includes/footer.php'; ?>
