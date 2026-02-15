<link rel="stylesheet" href="assets/css/modals.css">

<!-- Add Expense Modal -->
<dialog id="addExpenseModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Expense</h2>
            <button class="modal-close modal-close-btn" data-modal="addExpenseModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addExpenseForm">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select category</option>
                        <option value="Food">Food</option>
                        <option value="Transport">Transport</option>
                        <option value="School">School</option>
                        <option value="Shopping">Shopping</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Bills">Bills</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" name="amount" class="form-input" placeholder="0.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-input" placeholder="e.g., Jollibee lunch" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="addExpenseModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Expense</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Add Income Modal -->
<dialog id="addIncomeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Income</h2>
            <button class="modal-close modal-close-btn" data-modal="addIncomeModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addIncomeForm">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select category</option>
                        <option value="Salary">Salary</option>
                        <option value="Allowance">Allowance</option>
                        <option value="Freelance">Freelance</option>
                        <option value="Business">Business</option>
                        <option value="Investment">Investment</option>
                        <option value="Gift">Gift</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" name="amount" class="form-input" placeholder="0.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-input" placeholder="e.g., Monthly salary, Allowance" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="addIncomeModal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Income</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Add Utang Modal -->
<dialog id="addUtangModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Debt/Loan</h2>
            <button class="modal-close modal-close-btn" data-modal="addUtangModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addUtangForm">
                <div class="form-group">
                    <label class="form-label">Creditor/Debtor Name</label>
                    <input type="text" class="form-input" name="person_name" placeholder="e.g., Best Friend, Mom" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Type</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="payable" name="type" value="payable" checked>
                            <label for="payable">Payable (Money I Owe)</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="receivable" name="type" value="receivable">
                            <label for="receivable">Receivable (Money Owed to Me)</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" name="amount" class="form-input" placeholder="0.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea name="notes" class="form-textarea" placeholder="Add any notes..."></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="addUtangModal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Add Debt</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Set Budget Modal -->
<dialog id="setBudgetModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Set Budget</h2>
            <button class="modal-close modal-close-btn" data-modal="setBudgetModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="setBudgetForm">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select category</option>
                        <option value="Food">Food</option>
                        <option value="Transport">Transport</option>
                        <option value="School">School</option>
                        <option value="Shopping">Shopping</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Bills">Bills</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Monthly Budget (₱)</label>
                    <input type="number" name="amount" class="form-input" placeholder="5000" step="0.01" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="setBudgetModal">Cancel</button>
                    <button type="submit" class="btn btn-info">Set Budget</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Add Goal Modal -->
<dialog id="addGoalModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New Goal</h2>
            <button class="modal-close modal-close-btn" data-modal="addGoalModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addGoalForm">
                <div class="form-group">
                    <label class="form-label">Goal Name</label>
                    <input type="text" name="goal_name" class="form-input" placeholder="e.g., Laptop" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Amount (₱)</label>
                    <input type="number" name="target_amount" class="form-input" placeholder="50000" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Date</label>
                    <input type="date" name="deadline" class="form-input" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                </div>

                <input type="hidden" name="current_amount" value="0">

                <div class="form-group">
                    <label class="form-label">Priority</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="priority-high" name="priority" value="high">
                            <label for="priority-high">High</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="priority-medium" name="priority" value="medium" checked>
                            <label for="priority-medium">Medium</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="priority-low" name="priority" value="low">
                            <label for="priority-low">Low</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="addGoalModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Goal</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Add Subscription Modal -->
<dialog id="addSubscriptionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Subscription</h2>
            <button class="modal-close modal-close-btn" data-modal="addSubscriptionModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addSubscriptionForm">
                <div class="form-group">
                    <label class="form-label">Select Service</label>
                    <select class="form-select subscription-select" id="subscriptionService" name="service" required>
                        <option value="">Choose a subscription service</option>
                        <option value="spotify" data-icon="fab fa-spotify">🎵 Spotify</option>
                        <option value="netflix" data-icon="fas fa-play">📺 Netflix</option>
                        <option value="youtube" data-icon="fab fa-youtube">▶️ YouTube Premium</option>
                        <option value="amazon-prime" data-icon="fas fa-box">📦 Amazon Prime</option>
                        <option value="adobe" data-icon="fas fa-palette">🎨 Adobe Creative Cloud</option>
                        <option value="microsoft" data-icon="fas fa-window-maximize">🪟 Microsoft 365</option>
                        <option value="dropbox" data-icon="fas fa-cloud">☁️ Dropbox</option>
                        <option value="canva" data-icon="fas fa-pen">✏️ Canva Pro</option>
                        <option value="chatgpt" data-icon="fas fa-brain">🧠 ChatGPT Plus</option>
                        <option value="notion" data-icon="fas fa-book">📔 Notion Plus</option>
                        <option value="other" data-icon="fas fa-plus">➕ Other</option>
                    </select>
                </div>

                <div class="form-group" id="customServiceGroup" style="display: none;">
                    <label class="form-label">Service Name</label>
                    <input type="text" class="form-input" id="customServiceName" name="custom_service" placeholder="e.g., Gaming Subscription">
                </div>

                <div class="form-group">
                    <label class="form-label">Monthly Cost (₱)</label>
                    <input type="number" class="form-input" name="amount" placeholder="149.00" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Billing Day</label>
                    <input type="number" class="form-input" name="due_day" placeholder="1-31" min="1" max="31" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="sub-active" name="status" value="Active" checked>
                            <label for="sub-active">Active</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="sub-inactive" name="status" value="Inactive">
                            <label for="sub-inactive">Inactive</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Last Payment Date (Optional)</label>
                    <input type="date" class="form-input" id="lastPaymentDate" name="last_payment_date" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-textarea" name="notes" placeholder="Add any notes..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="addSubscriptionModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subscription</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Edit Subscription Modal -->
<dialog id="editSubscriptionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Subscription</h2>
            <button class="modal-close modal-close-btn" data-modal="editSubscriptionModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editSubscriptionForm">
                <input type="hidden" name="service_name_original">
                
                <div class="form-group">
                    <label class="form-label">Service Name</label>
                    <input type="text" name="service_name" class="form-input" required disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Monthly Cost (₱)</label>
                    <input type="number" name="amount" class="form-input" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Billing Day</label>
                    <input type="number" name="due_day" class="form-input" min="1" max="31" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="edit-sub-active" name="status" value="Active">
                            <label for="edit-sub-active">Active</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="edit-sub-inactive" name="status" value="Inactive">
                            <label for="edit-sub-inactive">Inactive</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Last Payment Date (Optional)</label>
                    <input type="date" name="last_payment_date" class="form-input" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="editSubscriptionModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Status Modal (Success/Error/Warning/Info) -->
<dialog id="statusModal">
    <div class="modal-content">
        <div class="modal-header">
            <button class="modal-close modal-close-btn" data-modal="statusModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body flex flex-col items-center justify-center text-center py-8">
            <div id="statusIcon" class="mb-4"></div>
            <h2 id="statusTitle" class="text-2xl font-bold text-gray-900 mb-2"></h2>
            <p id="statusMessage" class="text-gray-600 max-w-sm"></p>
        </div>
        <div class="modal-footer">
            <button id="statusBtn" class="btn btn-primary modal-close-btn" data-modal="statusModal">Close</button>
        </div>
    </div>
</dialog>

<!-- Delete Confirmation Modal -->
<dialog id="deleteConfirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <button class="modal-close modal-close-btn" data-modal="deleteConfirmModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p id="deleteMessage" class="text-gray-700 text-center">Are you sure you want to delete this item? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="deleteConfirmModal">Cancel</button>
            <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
        </div>
    </div>
</dialog>

<!-- Edit Transaction Modal -->
<dialog id="editTransactionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Transaction</h2>
            <button class="modal-close modal-close-btn" data-modal="editTransactionModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editTransactionForm">
                <input type="hidden" id="editTransactionId" name="transaction_id">
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select id="editCategory" name="category" class="form-select" required>
                        <option value="">Select category</option>
                        <option value="Food">Food</option>
                        <option value="Transport">Transport</option>
                        <option value="School">School</option>
                        <option value="Shopping">Shopping</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Bills">Bills</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Salary">Salary</option>
                        <option value="Allowance">Allowance</option>
                        <option value="Freelance">Freelance</option>
                        <option value="Business">Business</option>
                        <option value="Investment">Investment</option>
                        <option value="Gift">Gift</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" id="editAmount" name="amount" class="form-input" placeholder="0.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" id="editDescription" name="description" class="form-input" placeholder="e.g., Transaction details" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" id="editDate" name="date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="editTransactionModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Edit Budget Modal -->
<dialog id="editBudgetModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Budget</h2>
            <button class="modal-close modal-close-btn" data-modal="editBudgetModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editBudgetForm">
                <input type="hidden" id="editBudgetCategory" name="category">
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" id="editBudgetCategoryDisplay" class="form-input" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Monthly Budget (₱)</label>
                    <input type="number" id="editBudgetAmount" name="amount" class="form-input" placeholder="5000" step="0.01" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="editBudgetModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-danger" id="deleteBudgetBtn">Delete Budget</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<!-- Add Loan Payment Modal -->
<dialog id="addLoanPaymentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>💰 Add Payment</h2>
            <button class="modal-close modal-close-btn" data-modal="addLoanPaymentModal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addLoanPaymentForm">
                <input type="hidden" id="paymentLoanId" name="loan_id">
                <div class="form-group">
                    <label class="form-label">Amount to Pay (₱)</label>
                    <input type="number" id="paymentAmount" name="payment_amount" class="form-input" step="0.01" required placeholder="0.00">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-cancel-btn" data-modal="addLoanPaymentModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<script src="assets/js/modals.js"></script>
