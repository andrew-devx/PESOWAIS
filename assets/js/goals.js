function openGoalModal() {
    const modal = document.getElementById('addGoalModal');
    if (modal) modal.showModal();
}

function addMoneyToGoal(goalId, goalName, targetAmount, currentAmount) {
    // Remove existing modal if any
    const existingModal = document.getElementById('addMoneyModal');
    if (existingModal) existingModal.remove();

    const modalHtml = `
        <dialog id="addMoneyModal" class="rounded-lg shadow-2xl max-w-sm w-full">
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Add Money</h2>
                    <button type="button" class="close-add-money-modal text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="addMoneyForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Goal: ${goalName}</label>
                        <div class="text-xs text-gray-600 mb-3">
                            Current: ₱${parseFloat(currentAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} / Target: ₱${parseFloat(targetAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount to Add</label>
                        <input type="number" id="amountToAdd" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" class="close-add-money-modal flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">Add Money</button>
                    </div>
                </form>
            </div>
        </dialog>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = document.getElementById('addMoneyModal');
    modal.showModal();

    // Event listeners for closing
    modal.querySelectorAll('.close-add-money-modal').forEach(btn => {
        btn.onclick = function() {
            modal.close();
            modal.remove();
        };
    });
    
    document.getElementById('addMoneyForm').onsubmit = function(e) {
        e.preventDefault();
        const amountInput = document.getElementById('amountToAdd');
        const amount = parseFloat(amountInput.value);
        
        if (!amount || amount <= 0) {
            showStatusModal('error', 'Error', 'Please enter a valid amount');
            return;
        }
        
        const formData = new URLSearchParams({
            action: 'add_money',
            goal_id: goalId,
            amount: amount
        });
        
        fetch('logic/manage_goals.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            modal.close();
            modal.remove();
            if (data.status === 'success') {
                showStatusModal('success', 'Success!', data.message || 'Money added');
                setTimeout(() => location.reload(), 1500);
            } else {
                showStatusModal('error', 'Error', data.message || 'Failed to add money');
            }
        })
        .catch(error => {
            modal.close();
            modal.remove();
            showStatusModal('error', 'Error', 'Network error');
        });
    };
}

function editGoal(goalId, goalName, targetAmount, deadline) {
    const existingModal = document.getElementById('editGoalModal');
    if (existingModal) existingModal.remove();

    const modalHtml = `
        <dialog id="editGoalModal" class="rounded-lg shadow-2xl max-w-md w-full">
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Edit Goal</h2>
                    <button type="button" class="close-edit-goal-modal text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="editGoalForm">
                    <input type="hidden" id="editGoalId" value="${goalId}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Goal Name</label>
                        <input type="text" id="editGoalName" value="" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Amount</label>
                        <input type="number" id="editTargetAmount" value="" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                        <input type="date" id="editDeadline" value="" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" class="close-edit-goal-modal flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </dialog>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = document.getElementById('editGoalModal');
    modal.showModal();

    // Event listeners
    modal.querySelectorAll('.close-edit-goal-modal').forEach(btn => {
        btn.onclick = function() {
            modal.close();
            modal.remove();
        };
    });
    
    setTimeout(() => {
        document.getElementById('editGoalName').value = goalName;
        document.getElementById('editTargetAmount').value = parseFloat(targetAmount).toFixed(2);
        document.getElementById('editDeadline').value = deadline;
        
        document.getElementById('editGoalForm').onsubmit = function(e) {
            e.preventDefault();
            
            const nameInput = document.getElementById('editGoalName');
            const amountInput = document.getElementById('editTargetAmount');
            const deadlineInput = document.getElementById('editDeadline');
            
            const updatedGoalName = nameInput.value.trim();
            const updatedTargetAmount = parseFloat(amountInput.value);
            const updatedDeadline = deadlineInput.value;
            
            if (!updatedGoalName) {
                showStatusModal('error', 'Error', 'Please enter a goal name');
                return;
            }
            
            if (!updatedTargetAmount || updatedTargetAmount <= 0) {
                showStatusModal('error', 'Error', 'Please enter a valid target amount');
                return;
            }
            
            const formData = new URLSearchParams({
                action: 'update',
                goal_id: goalId,
                goal_name: updatedGoalName,
                target_amount: updatedTargetAmount,
                deadline: updatedDeadline || ''
            });
            
            fetch('logic/manage_goals.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
            .then(res => res.json())
            .then(data => {
                modal.close();
                modal.remove();
                if (data.status === 'success') {
                    showStatusModal('success', 'Success!', data.message || 'Goal updated');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showStatusModal('error', 'Error', data.message || 'Failed to update goal');
                }
            })
            .catch(error => {
                modal.close();
                modal.remove();
                showStatusModal('error', 'Error', 'Network error');
            });
        };
    }, 50);
}

function deleteGoal(goalId) {
    const existingModal = document.getElementById('deleteConfirmModal');
    if (existingModal) {
        existingModal.close();
        existingModal.remove();
    }
    
    const modalHtml = `
        <dialog id="deleteConfirmModal" class="rounded-lg shadow-2xl max-w-sm w-full">
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center justify-center mb-4 w-12 h-12 bg-red-100 rounded-full mx-auto">
                    <i class="fas fa-trash text-red-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 text-center mb-6">Delete Goal?</h2>
                <div class="flex gap-3">
                    <button type="button" class="close-delete-modal flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">Cancel</button>
                    <button type="button" class="confirm-delete-modal flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">Delete</button>
                </div>
            </div>
        </dialog>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = document.getElementById('deleteConfirmModal');
    modal.showModal();

    modal.querySelector('.close-delete-modal').onclick = function() {
        modal.close();
        modal.remove();
    };

    modal.querySelector('.confirm-delete-modal').onclick = function() {
        modal.close();
        modal.remove();
        confirmGoalDelete(goalId);
    };
}

function confirmGoalDelete(goalId) {
    fetch('logic/manage_goals.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&goal_id=${goalId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showStatusModal('success', 'Success!', data.message || 'Goal deleted');
            setTimeout(() => location.reload(), 1500);
        } else {
            showStatusModal('error', 'Error', data.message || 'Failed to delete');
        }
    })
    .catch(() => showStatusModal('error', 'Error', 'Network error'));
}

document.addEventListener('DOMContentLoaded', function() {
    // Open Goal Modal Button
    const openGoalModalBtn = document.getElementById('openGoalModalBtn');
    if(openGoalModalBtn) {
        openGoalModalBtn.addEventListener('click', openGoalModal);
    }
    // Also support class-based trigger if multiple buttons open it (though ID is cleaner for one button)
    document.querySelectorAll('.open-goal-modal-btn').forEach(btn => {
        btn.addEventListener('click', openGoalModal);
    });

    // Event Delegation for Goal Cards
    // Use document.body or a main container to ensure we catch events from all grids
    const mainContainer = document.querySelector('main'); 
    if(mainContainer) {
        mainContainer.addEventListener('click', function(e) {
            // Add Money Button
            const addMoneyBtn = e.target.closest('.add-money-goal-btn');
            if(addMoneyBtn) {
                const id = addMoneyBtn.dataset.id;
                const name = addMoneyBtn.dataset.name;
                const target = addMoneyBtn.dataset.target;
                const current = addMoneyBtn.dataset.current;
                addMoneyToGoal(id, name, target, current);
            }

            // Edit Goal Button
            const editBtn = e.target.closest('.edit-goal-btn');
            if(editBtn) {
                const id = editBtn.dataset.id;
                const name = editBtn.dataset.name;
                const target = editBtn.dataset.target;
                const deadline = editBtn.dataset.deadline;
                editGoal(id, name, target, deadline);
            }

            // Delete Goal Button
            const deleteBtn = e.target.closest('.delete-goal-btn');
            if(deleteBtn) {
                const id = deleteBtn.dataset.id;
                deleteGoal(id);
            }
            
            // Allow clicking the card itself to open add goal modal if it's the "Add Goal" card
            const addGoalCard = e.target.closest('.add-goal-card');
            if(addGoalCard) {
                openGoalModal();
            }
        });
    }
});
