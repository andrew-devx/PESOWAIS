// Modal handling functions
    function openModal(modalId) {
        document.getElementById(modalId).showModal();
    }

    function closeModal(modalId) {
        document.getElementById(modalId).close();
    }

    // Edit Transaction Functions
    function openEditTransactionModal(transaction) {
        document.getElementById('editTransactionId').value = transaction.transaction_id;
        document.getElementById('editCategory').value = transaction.category;
        document.getElementById('editAmount').value = transaction.amount;
        document.getElementById('editDescription').value = transaction.description;
        document.getElementById('editDate').value = transaction.transaction_date;
        document.getElementById('editTransactionModal').showModal();
    }

    async function handleEditTransactionSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', 'update');
        
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
            showStatusModal('error', 'Error', 'Failed to update transaction');
        }
        
        document.getElementById('editTransactionModal').close();
    }

    async function deleteTransaction(transactionId) {
        document.getElementById('deleteMessage').textContent = 'Are you sure you want to delete this transaction? This action cannot be undone.';
        document.getElementById('confirmDeleteBtn').onclick = async function() {
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
        document.getElementById('confirmDeleteBtn').onclick = async function() {
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
        console.log('Utang submitted');
        // Add your backend call here
        document.getElementById('addUtangModal').close();
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

    function handleGoalSubmit(e) {
        e.preventDefault();
        console.log('Goal submitted');
        // Add your backend call here
        document.getElementById('addGoalModal').close();
    }

    function handleSubscriptionSubmit(e) {
        e.preventDefault();
        const serviceSelect = document.getElementById('subscriptionService');
        const selectedValue = serviceSelect.value;
        const selectedText = serviceSelect.options[serviceSelect.selectedIndex].text;
        
        console.log('Subscription submitted:', {
            service: selectedValue === 'other' ? document.getElementById('customServiceName').value : selectedText,
            cost: document.querySelector('#addSubscriptionModal input[type="number"]').value,
            billingDay: document.querySelector('#addSubscriptionModal input[type="number"]:nth-of-type(2)').value,
            status: document.querySelector('#addSubscriptionModal input[name="status"]:checked').value
        });
        // Add your backend call here
        document.getElementById('addSubscriptionModal').close();
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
        
        switch(type) {
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
    document.addEventListener('DOMContentLoaded', function() {
        const subscriptionSelect = document.getElementById('subscriptionService');
        const customServiceGroup = document.getElementById('customServiceGroup');
        const customServiceName = document.getElementById('customServiceName');
        
        if (subscriptionSelect) {
            subscriptionSelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    customServiceGroup.style.display = 'block';
                    customServiceName.required = true;
                } else {
                    customServiceGroup.style.display = 'none';
                    customServiceName.required = false;
                }
            });
        }
    });
