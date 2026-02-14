// Modal handling functions
function openModal(modalId) {
    document.getElementById(modalId).showModal();
}

function closeModal(modalId) {
    document.getElementById(modalId).close();
}

function openUtangModal() {
    openModal('addUtangModal');
}

// Edit Transaction Functions
function openEditTransactionModal(transaction) {
    document.getElementById('editTransactionId').value = transaction.transaction_id;
    document.getElementById('editCategory').value = transaction.category;
    document.getElementById('editAmount').value = transaction.amount;
    document.getElementById('editDescription').value = transaction.description;
    // Extract only the date portion (YYYY-MM-DD) from the timestamp
    let dateValue = new Date().toISOString().split('T')[0];
    if (transaction.transaction_date && transaction.transaction_date.trim() !== '') {
        // Get first 10 characters which is the date part (YYYY-MM-DD)
        dateValue = transaction.transaction_date.substring(0, 10);
    }
    document.getElementById('editDate').value = dateValue;
    document.getElementById('editTransactionModal').showModal();
}

async function handleEditTransactionSubmit(e) {
    e.preventDefault();

    // Manually build FormData with all required fields
    const form = e.target;
    const formData = new FormData();

    formData.append('action', 'update');
    formData.append('transaction_id', document.getElementById('editTransactionId').value);
    formData.append('category', document.getElementById('editCategory').value);
    formData.append('amount', document.getElementById('editAmount').value);
    formData.append('description', document.getElementById('editDescription').value);
    formData.append('date', document.getElementById('editDate').value);

    // Debug log
    console.log('Form data being sent:', {
        action: formData.get('action'),
        transaction_id: formData.get('transaction_id'),
        category: formData.get('category'),
        amount: formData.get('amount'),
        description: formData.get('description'),
        date: formData.get('date')
    });

    try {
        const response = await fetch('logic/update_transaction.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            showStatusModal('success', 'Success!', result.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', result.message);
        }
    } catch (error) {
        console.error('Error details:', error);
        showStatusModal('error', 'Error', 'Failed to update transaction. Check console for details.');
    }

    document.getElementById('editTransactionModal').close();
}

async function deleteTransaction(transactionId) {
    document.getElementById('deleteMessage').textContent = 'Are you sure you want to delete this transaction? This action cannot be undone.';
    document.getElementById('confirmDeleteBtn').onclick = async function () {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('transaction_id', transactionId);

        try {
            const response = await fetch('logic/update_transaction.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                showStatusModal('success', 'Success!', result.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showStatusModal('error', 'Error', result.message);
            }
        } catch (error) {
            showStatusModal('error', 'Error', 'Failed to delete transaction');
        }

        document.getElementById('deleteConfirmModal').close();
    };
    document.getElementById('deleteConfirmModal').showModal();
}

// Edit Budget Functions
function openEditBudgetModal(category, amount) {
    document.getElementById('editBudgetCategory').value = category;
    document.getElementById('editBudgetCategoryDisplay').value = category;
    document.getElementById('editBudgetAmount').value = amount;
    document.getElementById('editBudgetModal').showModal();
}

async function handleEditBudgetSubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('action', 'set');

    try {
        const response = await fetch('logic/manage_budgets.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            showStatusModal('success', 'Success!', result.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', result.message);
        }
    } catch (error) {
        showStatusModal('error', 'Error', 'Failed to update budget');
    }

    document.getElementById('editBudgetModal').close();
}

async function deleteEditingBudget() {
    document.getElementById('deleteMessage').textContent = 'Are you sure you want to delete this budget? This action cannot be undone.';
    document.getElementById('confirmDeleteBtn').onclick = async function () {
        const category = document.getElementById('editBudgetCategory').value;
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('category', category);

        try {
            const response = await fetch('logic/manage_budgets.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                showStatusModal('success', 'Success!', result.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showStatusModal('error', 'Error', result.message);
            }
        } catch (error) {
            showStatusModal('error', 'Error', 'Failed to delete budget');
        }

        document.getElementById('deleteConfirmModal').close();
    };
    document.getElementById('deleteConfirmModal').showModal();
}

// Form submit handlers (replace with actual backend calls)
async function handleExpenseSubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('type', 'Expense');

    try {
        const response = await fetch('logic/add_transaction.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            showStatusModal('success', 'Success!', result.message);
            e.target.reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', result.message);
        }
    } catch (error) {
        showStatusModal('error', 'Error', 'Failed to add expense');
    }

    document.getElementById('addExpenseModal').close();
}

async function handleIncomeSubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('type', 'Income');

    try {
        const response = await fetch('logic/add_transaction.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            showStatusModal('success', 'Success!', result.message);
            e.target.reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', result.message);
        }
    } catch (error) {
        showStatusModal('error', 'Error', 'Failed to add income');
    }

    document.getElementById('addIncomeModal').close();
}

function handleUtangSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const person_name = form.querySelector('input[type="text"]').value.trim();
    const type = form.querySelector('input[name="type"]:checked').value === 'payable' ? 'Payable' : 'Receivable';
    const amount = form.querySelector('input[type="number"]').value;
    const due_date = form.querySelector('input[type="date"]').value;

    const params = new URLSearchParams({
        action: 'add_loan',
        type,
        person_name,
        amount,
        due_date
    });
    fetch('logic/manage_loans.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
        .then(res => {
            if (!res.ok) {
                throw new Error('HTTP error! status: ' + res.status);
            }
            return res.text();
        })
        .then(text => {
            try {
                const result = JSON.parse(text);
                if (result.status === 'success') {
                    showStatusModal('success', 'Success!', result.message);
                    form.reset();
                    setTimeout(() => {
                        document.getElementById('addUtangModal').close();
                        location.reload();
                    }, 1000);
                } else {
                    showStatusModal('error', 'Error', result.message || 'Failed to add loan.');
                }
            } catch (e) {
                console.error('Response text:', text);
                showStatusModal('error', 'Error', 'Invalid server response: ' + text.substring(0, 100));
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            showStatusModal('error', 'Error', 'Network error: ' + error.message);
        });
}

async function handleBudgetSubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('action', 'set');

    try {
        const response = await fetch('logic/manage_budgets.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            showStatusModal('success', 'Success!', result.message);
            e.target.reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', result.message);
        }
    } catch (error) {
        showStatusModal('error', 'Error', 'Failed to set budget');
    }

    document.getElementById('setBudgetModal').close();
}

async function handleGoalSubmit(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('goal_name', document.querySelector('#addGoalModal input[name="goal_name"]').value);
    formData.append('target_amount', document.querySelector('#addGoalModal input[name="target_amount"]').value);
    formData.append('deadline', document.querySelector('#addGoalModal input[name="deadline"]').value);
    formData.append('current_amount', document.querySelector('#addGoalModal input[name="current_amount"]').value || '0');

    try {
        const response = await fetch('logic/manage_goals.php', {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            showStatusModal('error', 'Error', 'Invalid server response. Check console.');
            console.error('Goal create response (non-JSON):', text);
            document.getElementById('addGoalModal').close();
            return;
        }

        if (result.status === 'success') {
            showStatusModal('success', 'Success!', result.message);
            e.target.reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', result.message);
        }
    } catch (error) {
        showStatusModal('error', 'Error', 'Failed to create goal');
    }

    document.getElementById('addGoalModal').close();
}

async function handleSubscriptionSubmit(e) {
    e.preventDefault();
    const serviceSelect = document.getElementById('subscriptionService');
    const selectedValue = serviceSelect.value;
    const selectedText = serviceSelect.options[serviceSelect.selectedIndex].text;
    const customService = document.getElementById('customServiceName').value.trim();

    const serviceName = selectedValue === 'other' ? customService : selectedText.replace(/^[^A-Za-z0-9]+\s*/, '');

    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('service_name', serviceName);
    formData.append('amount', document.querySelector('#addSubscriptionModal input[name="amount"]').value);
    formData.append('due_day', document.querySelector('#addSubscriptionModal input[name="due_day"]').value);
    formData.append('status', document.querySelector('#addSubscriptionModal input[name="status"]:checked').value);
    formData.append('last_payment_date', document.querySelector('#addSubscriptionModal input[name="last_payment_date"]').value);

    try {
        const response = await fetch('logic/manage_subscriptions.php', {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            showStatusModal('error', 'Error', 'Invalid server response. Check console.');
            console.error('Subscription create response (non-JSON):', text);
            document.getElementById('addSubscriptionModal').close();
            return;
        }

        if (result.status === 'success') {
            showStatusModal('success', 'Success!', result.message);
            e.target.reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', result.message);
        }
    } catch (error) {
        showStatusModal('error', 'Error', 'Failed to add subscription');
    }

    document.getElementById('addSubscriptionModal').close();
}

function openEditSubscriptionModal(serviceName, amount, dueDay, status, lastPaymentDate) {
    document.querySelector('#editSubscriptionModal input[name="service_name"]').value = serviceName;
    document.querySelector('#editSubscriptionModal input[name="service_name_original"]').value = serviceName;
    document.querySelector('#editSubscriptionModal input[name="amount"]').value = amount;
    document.querySelector('#editSubscriptionModal input[name="due_day"]').value = dueDay;
    
    // Set status radio button - default to Active if not provided
    const statusToSet = status || 'Active';
    const statusRadio = document.querySelector(`#editSubscriptionModal input[name="status"][value="${statusToSet}"]`);
    if (statusRadio) statusRadio.checked = true;
    
    // Set last payment date if available
    if (lastPaymentDate) {
         document.querySelector('#editSubscriptionModal input[name="last_payment_date"]').value = lastPaymentDate;
    }

    document.getElementById('editSubscriptionModal').showModal();
}

async function handleEditSubscriptionSubmit(e) {
    e.preventDefault();
    const originalName = document.querySelector('#editSubscriptionModal input[name="service_name_original"]').value;
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('service_name_original', originalName);
    formData.append('amount', document.querySelector('#editSubscriptionModal input[name="amount"]').value);
    formData.append('due_day', document.querySelector('#editSubscriptionModal input[name="due_day"]').value);
    formData.append('status', document.querySelector('#editSubscriptionModal input[name="status"]:checked').value);
    formData.append('last_payment_date', document.querySelector('#editSubscriptionModal input[name="last_payment_date"]').value);

    try {
        const response = await fetch('logic/manage_subscriptions.php', {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            showStatusModal('error', 'Error', 'Invalid server response. Check console.');
            console.error('Subscription update response (non-JSON):', text);
            document.getElementById('editSubscriptionModal').close();
            return;
        }

        if (result.status === 'success') {
            showStatusModal('success', 'Success!', result.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', result.message);
        }
    } catch (error) {
        showStatusModal('error', 'Error', 'Failed to update subscription');
    }

    document.getElementById('editSubscriptionModal').close();
}

function deleteSubscription(serviceName) {
    document.getElementById('deleteMessage').textContent = `Are you sure you want to delete "${serviceName}"? This action cannot be undone.`;
    document.getElementById('confirmDeleteBtn').onclick = async function () {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('service_name', serviceName);

        try {
            const response = await fetch('logic/manage_subscriptions.php', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (parseError) {
                showStatusModal('error', 'Error', 'Invalid server response. Check console.');
                console.error('Subscription delete response (non-JSON):', text);
                document.getElementById('deleteConfirmModal').close();
                return;
            }

            if (result.status === 'success') {
                showStatusModal('success', 'Success!', result.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showStatusModal('error', 'Error', result.message);
            }
        } catch (error) {
            showStatusModal('error', 'Error', 'Failed to delete subscription');
        }

        document.getElementById('deleteConfirmModal').close();
    };
    document.getElementById('deleteConfirmModal').showModal();
}

function deleteEditingSubscription() {
    const serviceName = document.querySelector('#editSubscriptionModal input[name="service_name_original"]').value;
    document.getElementById('deleteMessage').textContent = `Are you sure you want to delete "${serviceName}"? This action cannot be undone.`;
    document.getElementById('confirmDeleteBtn').onclick = async function () {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('service_name', serviceName);

        try {
            const response = await fetch('logic/manage_subscriptions.php', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (parseError) {
                showStatusModal('error', 'Error', 'Invalid server response. Check console.');
                console.error('Subscription delete response (non-JSON):', text);
                document.getElementById('deleteConfirmModal').close();
                return;
            }

            if (result.status === 'success') {
                showStatusModal('success', 'Success!', result.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showStatusModal('error', 'Error', result.message);
            }
        } catch (error) {
            showStatusModal('error', 'Error', 'Failed to delete subscription');
        }

        document.getElementById('deleteConfirmModal').close();
    };
    document.getElementById('deleteConfirmModal').showModal();
}

// Status Modal Functions (defined globally)
let onStatusModalClose = null;

function showStatusModal(type, title, message, callback = null) {
    const modal = document.getElementById('statusModal');
    const iconContainer = document.getElementById('statusIcon');
    const titleEl = document.getElementById('statusTitle');
    const msgEl = document.getElementById('statusMessage');
    const btn = document.getElementById('statusBtn');

    let icon, bgColor, btnClass;

    switch (type) {
        case 'success':
            icon = '<i class="fas fa-check-circle text-green-500 text-4xl"></i>';
            bgColor = 'from-green-50 to-green-100';
            btnClass = 'btn-success';
            break;
        case 'error':
            icon = '<i class="fas fa-exclamation-circle text-red-500 text-4xl"></i>';
            bgColor = 'from-red-50 to-red-100';
            btnClass = 'btn-danger';
            break;
        case 'warning':
            icon = '<i class="fas fa-warning text-amber-500 text-4xl"></i>';
            bgColor = 'from-amber-50 to-amber-100';
            btnClass = 'btn-warning';
            break;
        case 'info':
        default:
            icon = '<i class="fas fa-info-circle text-blue-500 text-4xl"></i>';
            bgColor = 'from-blue-50 to-blue-100';
            btnClass = 'btn-primary';
    }

    iconContainer.innerHTML = icon;
    titleEl.textContent = title;
    msgEl.textContent = message;
    btn.className = `btn ${btnClass}`;

    onStatusModalClose = callback;
    modal.showModal();
}

function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.close();
    if (onStatusModalClose && typeof onStatusModalClose === 'function') {
        onStatusModalClose();
    }
}

// Toggle custom service name field when "Other" is selected
document.addEventListener('DOMContentLoaded', function () {
    // Subscription Select
    const subscriptionSelect = document.getElementById('subscriptionService');
    const customServiceGroup = document.getElementById('customServiceGroup');
    const customServiceName = document.getElementById('customServiceName');

    if (subscriptionSelect) {
        subscriptionSelect.addEventListener('change', function () {
            if (this.value === 'other') {
                customServiceGroup.style.display = 'block';
                customServiceName.required = true;
            } else {
                customServiceGroup.style.display = 'none';
                customServiceName.required = false;
            }
        });
    }

    // Modal Close Buttons
    document.querySelectorAll('.modal-close-btn, .modal-cancel-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const modalId = btn.dataset.modal;
            if (modalId) {
                const modal = document.getElementById(modalId);
                if (modal) modal.close();
            } else {
                const dialog = btn.closest('dialog');
                if (dialog) dialog.close();
            }
        });
    });

    // Form Submit Listeners
    const forms = {
        'addExpenseForm': handleExpenseSubmit,
        'addIncomeForm': handleIncomeSubmit,
        'addUtangForm': handleUtangSubmit,
        'setBudgetForm': handleBudgetSubmit,
        'addGoalForm': handleGoalSubmit,
        'addSubscriptionForm': handleSubscriptionSubmit,
        'editSubscriptionForm': handleEditSubscriptionSubmit,
        'editTransactionForm': handleEditTransactionSubmit,
        'editBudgetForm': handleEditBudgetSubmit
    };

    for (const [id, handler] of Object.entries(forms)) {
        const form = document.getElementById(id);
        if (form) {
            form.addEventListener('submit', handler);
        }
    }

    // Delete Budget Button
    const deleteBudgetBtn = document.getElementById('deleteBudgetBtn');
    if (deleteBudgetBtn) {
        deleteBudgetBtn.addEventListener('click', deleteEditingBudget);
    }

    // Close Status Modal
    const statusModalCloseBtn = document.querySelector('#statusModal .modal-close-btn');
    if (statusModalCloseBtn) {
        statusModalCloseBtn.addEventListener('click', closeStatusModal);
    }
});
