<link rel="stylesheet" href="assets/css/modals.css">

<!-- Add Expense Modal -->
<dialog id="addExpenseModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Expense</h2>
            <button class="modal-close" onclick="document.getElementById('addExpenseModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form onsubmit="handleExpenseSubmit(event)">
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
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('addExpenseModal').close()">Cancel</button>
            <button class="btn btn-primary" onclick="document.querySelector('#addExpenseModal form').dispatchEvent(new Event('submit'))">Add Expense</button>
        </div>
    </div>
</dialog>

<!-- Add Income Modal -->
<dialog id="addIncomeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Income</h2>
            <button class="modal-close" onclick="document.getElementById('addIncomeModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form onsubmit="handleIncomeSubmit(event)">
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
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('addIncomeModal').close()">Cancel</button>
            <button class="btn btn-success" onclick="document.querySelector('#addIncomeModal form').dispatchEvent(new Event('submit'))">Add Income</button>
        </div>
    </div>
</dialog>

<!-- Add Utang Modal -->
<dialog id="addUtangModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Debt/Loan</h2>
            <button class="modal-close" onclick="document.getElementById('addUtangModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form onsubmit="handleUtangSubmit(event)">
                <div class="form-group">
                    <label class="form-label">Creditor/Debtor Name</label>
                    <input type="text" class="form-input" placeholder="e.g., Best Friend, Mom" required>
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
                    <input type="number" class="form-input" placeholder="0.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-textarea" placeholder="Add any notes..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('addUtangModal').close()">Cancel</button>
            <button class="btn btn-warning" onclick="document.querySelector('#addUtangModal form').dispatchEvent(new Event('submit'))">Add Debt</button>
        </div>
    </div>
</dialog>

<!-- Set Budget Modal -->
<dialog id="setBudgetModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Set Budget</h2>
            <button class="modal-close" onclick="document.getElementById('setBudgetModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form onsubmit="handleBudgetSubmit(event)">
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
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('setBudgetModal').close()">Cancel</button>
            <button class="btn btn-info" onclick="document.querySelector('#setBudgetModal form').dispatchEvent(new Event('submit'))">Set Budget</button>
        </div>
    </div>
</dialog>

<!-- Add Goal Modal -->
<dialog id="addGoalModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New Goal</h2>
            <button class="modal-close" onclick="document.getElementById('addGoalModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form onsubmit="handleGoalSubmit(event)">
                <div class="form-group">
                    <label class="form-label">Goal Name</label>
                    <input type="text" class="form-input" placeholder="e.g., Laptop" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Amount (₱)</label>
                    <input type="number" class="form-input" placeholder="50000" step="100" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Date</label>
                    <input type="date" class="form-input" required>
                </div>

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
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('addGoalModal').close()">Cancel</button>
            <button class="btn btn-primary" onclick="document.querySelector('#addGoalModal form').dispatchEvent(new Event('submit'))">Create Goal</button>
        </div>
    </div>
</dialog>

<!-- Add Subscription Modal -->
<dialog id="addSubscriptionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Subscription</h2>
            <button class="modal-close" onclick="document.getElementById('addSubscriptionModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form onsubmit="handleSubscriptionSubmit(event)">
                <div class="form-group">
                    <label class="form-label">Select Service</label>
                    <select class="form-select subscription-select" id="subscriptionService" required>
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
                    <input type="text" class="form-input" id="customServiceName" placeholder="e.g., Gaming Subscription">
                </div>

                <div class="form-group">
                    <label class="form-label">Monthly Cost (₱)</label>
                    <input type="number" class="form-input" placeholder="149.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Billing Day</label>
                    <input type="number" class="form-input" placeholder="1-31" min="1" max="31" required>
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
                    <input type="date" class="form-input" id="lastPaymentDate">
                </div>

                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-textarea" placeholder="Add any notes..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('addSubscriptionModal').close()">Cancel</button>
            <button class="btn btn-primary" onclick="document.querySelector('#addSubscriptionModal form').dispatchEvent(new Event('submit'))">Add Subscription</button>
        </div>
    </div>
</dialog>

<!-- Status Modal (Success/Error/Warning/Info) -->
<dialog id="statusModal">
    <div class="modal-content">
        <div class="modal-header">
            <button class="modal-close" onclick="closeStatusModal()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body flex flex-col items-center justify-center text-center py-8">
            <div id="statusIcon" class="mb-4"></div>
            <h2 id="statusTitle" class="text-2xl font-bold text-gray-900 mb-2"></h2>
            <p id="statusMessage" class="text-gray-600 max-w-sm"></p>
        </div>
        <div class="modal-footer">
            <button id="statusBtn" class="btn btn-primary" onclick="closeStatusModal()">Close</button>
        </div>
    </div>
</dialog>

<!-- Delete Confirmation Modal -->
<dialog id="deleteConfirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <button class="modal-close" onclick="document.getElementById('deleteConfirmModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p id="deleteMessage" class="text-gray-700 text-center">Are you sure you want to delete this item? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('deleteConfirmModal').close()">Cancel</button>
            <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
        </div>
    </div>
</dialog>

<!-- Edit Transaction Modal -->
<dialog id="editTransactionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Transaction</h2>
            <button class="modal-close" onclick="document.getElementById('editTransactionModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form onsubmit="handleEditTransactionSubmit(event)">
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
                    <input type="date" id="editDate" name="date" class="form-input" required>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('editTransactionModal').close()">Cancel</button>
            <button class="btn btn-primary" onclick="document.querySelector('#editTransactionModal form').dispatchEvent(new Event('submit'))">Save Changes</button>
        </div>
    </div>
</dialog>

<!-- Edit Budget Modal -->
<dialog id="editBudgetModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Budget</h2>
            <button class="modal-close" onclick="document.getElementById('editBudgetModal').close()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form onsubmit="handleEditBudgetSubmit(event)">
                <input type="hidden" id="editBudgetCategory" name="category">
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" id="editBudgetCategoryDisplay" class="form-input" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Monthly Budget (₱)</label>
                    <input type="number" id="editBudgetAmount" name="amount" class="form-input" placeholder="5000" step="0.01" required>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('editBudgetModal').close()">Cancel</button>
            <button class="btn btn-primary" onclick="document.querySelector('#editBudgetModal form').dispatchEvent(new Event('submit'))">Save Changes</button>
            <button class="btn btn-danger" onclick="deleteEditingBudget()">Delete Budget</button>
        </div>
    </div>
</dialog>

<script src="assets/js/modals.js"></script>
