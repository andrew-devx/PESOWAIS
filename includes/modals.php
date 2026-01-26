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
                    <label class="form-label">Description</label>
                    <input type="text" class="form-input" placeholder="e.g., Jollibee" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" class="form-input" placeholder="0.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select class="form-select" required>
                        <option value="">Select category</option>
                        <option>Food</option>
                        <option>Transport</option>
                        <option>School</option>
                        <option>Leisure</option>
                        <option>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select" required>
                        <option value="">Select method</option>
                        <option>Cash</option>
                        <option>G-Cash</option>
                        <option>Maya</option>
                        <option>Bank Transfer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-input" required>
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
                    <label class="form-label">Income Source</label>
                    <input type="text" class="form-input" placeholder="e.g., Allowance, Part-time Job" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" class="form-input" placeholder="0.00" step="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select" required>
                        <option value="">Select method</option>
                        <option>Cash</option>
                        <option>G-Cash</option>
                        <option>Maya</option>
                        <option>Bank Transfer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-input" required>
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
                    <select class="form-select" required>
                        <option value="">Select category</option>
                        <option>Food</option>
                        <option>Transport</option>
                        <option>School</option>
                        <option>Leisure</option>
                        <option>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Monthly Budget (₱)</label>
                    <input type="number" class="form-input" placeholder="5000" step="100" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Alert at % of Budget</label>
                    <select class="form-select" required>
                        <option>80%</option>
                        <option>70%</option>
                        <option>60%</option>
                        <option>90%</option>
                    </select>
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

<script>
    function openModal(modalId) {
        document.getElementById(modalId).showModal();
    }

    function closeModal(modalId) {
        document.getElementById(modalId).close();
    }

    // Form submit handlers (replace with actual backend calls)
    function handleExpenseSubmit(e) {
        e.preventDefault();
        console.log('Expense submitted');
        // Add your backend call here
        document.getElementById('addExpenseModal').close();
    }

    function handleIncomeSubmit(e) {
        e.preventDefault();
        console.log('Income submitted');
        // Add your backend call here
        document.getElementById('addIncomeModal').close();
    }

    function handleUtangSubmit(e) {
        e.preventDefault();
        console.log('Utang submitted');
        // Add your backend call here
        document.getElementById('addUtangModal').close();
    }

    function handleBudgetSubmit(e) {
        e.preventDefault();
        console.log('Budget submitted');
        // Add your backend call here
        document.getElementById('setBudgetModal').close();
    }

    function handleGoalSubmit(e) {
        e.preventDefault();
        console.log('Goal submitted');
        // Add your backend call here
        document.getElementById('addGoalModal').close();
    }
</script>
