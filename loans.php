<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'includes/connection.php';
include 'includes/header.php';
include 'includes/modals.php';

// Fetch loans data
include 'logic/fetch_loans_data.php';
?>

<main class="min-h-screen py-6 px-4 md:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Utang & Pautang Tracker</h2>
            <p class="text-xs sm:text-sm text-gray-600">Track your payables and receivables</p>
        </div>

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mb-6">
            <!-- Total Payable Card -->
            <div class="relative bg-white border-2 border-red-200 rounded-lg p-2 shadow-sm hover:shadow-md hover:border-red-400 transition-all duration-300 group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-[9px] text-red-700 font-semibold uppercase tracking-wide">Payable</p>
                        <div class="bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-200 rounded-lg p-1.5 group-hover:border-red-400 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-money-bill-wave text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-base sm:text-lg font-bold text-red-700">₱<?php echo number_format($totalPayable, 2); ?></p>
                    <p class="text-xs text-red-500 mt-0.5"><?php echo count($pendingLoans['payable']); ?> debt(s)</p>
                </div>
            </div>

            <!-- Total Receivable Card -->
            <div class="relative bg-white border-2 border-green-200 rounded-lg p-2 shadow-sm hover:shadow-md hover:border-green-400 transition-all duration-300 group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-[9px] text-green-700 font-semibold uppercase tracking-wide">Receivable</p>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-lg p-1.5 group-hover:border-green-400 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-hand-holding-usd text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-base sm:text-lg font-bold text-green-700">₱<?php echo number_format($totalReceivable, 2); ?></p>
                    <p class="text-xs text-green-500 mt-0.5"><?php echo count($pendingLoans['receivable']); ?> loan(s)</p>
                </div>
            </div>

            <!-- Net Balance Card -->
            <div class="relative bg-white border-2 border-blue-200 rounded-lg p-2 shadow-sm hover:shadow-md hover:border-blue-400 transition-all duration-300 group hidden sm:block">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-[9px] text-blue-700 font-semibold uppercase tracking-wide">Net Balance</p>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-lg p-1.5 group-hover:border-blue-400 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-scale-balanced text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <p class="text-base sm:text-lg font-bold <?php echo $netBalance >= 0 ? 'text-blue-700' : 'text-red-700'; ?>">₱<?php echo number_format(abs($netBalance), 2); ?></p>
                    <p class="text-xs <?php echo $netBalance >= 0 ? 'text-blue-500' : 'text-red-500'; ?> mt-0.5"><?php echo $netBalance >= 0 ? 'Positive' : 'Negative'; ?></p>
                </div>
            </div>

            <!-- Add Button - Ghost Button -->
            <div class="relative border-2 border-orange-400 rounded-lg overflow-hidden cursor-pointer group hover:shadow-md transition-all duration-300 add-loan-card open-utang-modal-btn">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-orange-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative flex flex-col items-center justify-center p-2 text-orange-600 hover:text-orange-700 transition-colors h-full">
                    <span class="material-icons text-2xl sm:text-3xl mb-1 group-hover:scale-125 group-hover:rotate-90 transition-transform duration-300">add_circle_outline</span>
                    <span class="text-xs font-semibold tracking-wide">Add Loan</span>
                </div>
            </div>
        </div>

        <!-- Loans Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
            <!-- Payables Section -->
            <section class="lg:col-span-1 bg-white rounded-lg p-4 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-gray-800">Payables</h2>
                    <span class="text-xs text-gray-500 font-medium"><?php echo count($pendingLoans['payable']); ?> items</span>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    <?php if (empty($pendingLoans['payable'])): ?>
                        <div class="text-center py-6 text-sm text-gray-400">
                            <p>No outstanding payables</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingLoans['payable'] as $loan): ?>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 bg-red-50 border border-red-200 rounded-lg p-3 text-sm hover:bg-red-100 transition-all">
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 truncate text-sm"><?php echo htmlspecialchars($loan['person_name']); ?></div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        <i class="far fa-calendar-alt mr-1"></i><?php echo date('M d, Y', strtotime($loan['due_date'])); ?>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-2">
                                    <span class="text-sm font-bold text-red-600">₱<?php echo number_format($loan['amount'], 2); ?></span>
                                    <div class="flex gap-1">
                                        <button type="button" class="mark-paid-btn text-green-600 hover:text-green-800 p-1.5 hover:bg-green-100 rounded" 
                                                title="Mark as Paid"
                                                data-id="<?php echo $loan['loan_id']; ?>">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                        <button type="button" class="edit-loan-btn text-blue-600 hover:text-blue-800 p-1.5 hover:bg-blue-100 rounded" 
                                                title="Edit"
                                                data-id="<?php echo $loan['loan_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($loan['person_name'], ENT_QUOTES); ?>"
                                                data-type="Payable"
                                                data-amount="<?php echo $loan['amount']; ?>"
                                                data-due="<?php echo $loan['due_date']; ?>">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button type="button" class="delete-loan-btn text-red-600 hover:text-red-800 p-1.5 hover:bg-red-100 rounded" 
                                                title="Delete"
                                                data-id="<?php echo $loan['loan_id']; ?>">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Receivables Section -->
            <section class="lg:col-span-1 bg-white rounded-lg p-4 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-gray-800">Receivables</h2>
                    <span class="text-xs text-gray-500 font-medium"><?php echo count($pendingLoans['receivable']); ?> items</span>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    <?php if (empty($pendingLoans['receivable'])): ?>
                        <div class="text-center py-6 text-sm text-gray-400">
                            <p>No pending receivables</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingLoans['receivable'] as $loan): ?>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 bg-green-50 border border-green-200 rounded-lg p-3 text-sm hover:bg-green-100 transition-all">
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 truncate text-sm"><?php echo htmlspecialchars($loan['person_name']); ?></div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        <i class="far fa-calendar-alt mr-1"></i><?php echo date('M d, Y', strtotime($loan['due_date'])); ?>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-2">
                                    <span class="text-sm font-bold text-green-600">₱<?php echo number_format($loan['amount'], 2); ?></span>
                                    <div class="flex gap-1">
                                        <button type="button" class="mark-paid-btn text-blue-600 hover:text-blue-800 p-1.5 hover:bg-blue-100 rounded" 
                                                title="Mark as Paid"
                                                data-id="<?php echo $loan['loan_id']; ?>">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                        <button type="button" class="edit-loan-btn text-blue-600 hover:text-blue-800 p-1.5 hover:bg-blue-100 rounded" 
                                                title="Edit"
                                                data-id="<?php echo $loan['loan_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($loan['person_name'], ENT_QUOTES); ?>"
                                                data-type="Receivable"
                                                data-amount="<?php echo $loan['amount']; ?>"
                                                data-due="<?php echo $loan['due_date']; ?>">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button type="button" class="delete-loan-btn text-red-600 hover:text-red-800 p-1.5 hover:bg-red-100 rounded" 
                                                title="Delete"
                                                data-id="<?php echo $loan['loan_id']; ?>">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- History Section -->
        <section class="bg-white rounded-lg p-4 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-800">Completed Loans</h2>
                <span class="text-xs text-gray-500 font-medium"><?php echo count($paidLoans); ?> completed</span>
            </div>
            <div class="space-y-1 max-h-56 overflow-y-auto">
                <?php if (empty($paidLoans)): ?>
                    <div class="text-center py-6 text-sm text-gray-400">
                        <p>No completed loans yet</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($paidLoans as $loan): ?>
                        <div class="flex items-center justify-between bg-gray-100 border border-gray-300 rounded p-2.5 text-sm hover:bg-gray-200 transition-all">
                            <div class="flex-1 min-w-0">
                                <span class="font-semibold text-gray-700 truncate block"><?php echo htmlspecialchars($loan['person_name']); ?></span>
                                <span class="text-xs text-gray-600 mt-0.5">₱<?php echo number_format($loan['amount'], 2); ?> - <?php echo date('M d, Y', strtotime($loan['due_date'])); ?></span>
                            </div>
                            <span class="ml-2 px-2 py-1 bg-gray-600 text-white text-xs rounded font-semibold"><?php echo $loan['type']; ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<script src="assets/js/loans.js"></script>


<?php include 'includes/footer.php'; ?>
