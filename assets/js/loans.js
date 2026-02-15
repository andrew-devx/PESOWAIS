function markPaid(loanId) {
  // Remove any existing modal first
  const existingModal = document.getElementById('markPaidConfirmModal');
  if (existingModal) {
    existingModal.close();
    existingModal.remove();
  }

  const modalHtml = `
        <dialog id="markPaidConfirmModal" class="rounded-lg shadow-2xl max-w-sm w-full">
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center justify-center mb-4 w-12 h-12 bg-green-100 rounded-full mx-auto">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 text-center mb-2">Mark as Paid?</h2>
                <p class="text-gray-600 text-center mb-6 text-sm">Are you sure you want to mark this loan as fully paid?</p>
                <div class="flex gap-3">
                    <button type="button" class="close-mark-paid-modal flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">Cancel</button>
                    <button type="button" class="confirm-mark-paid-modal flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">Yes, Mark Paid</button>
                </div>
            </div>
        </dialog>
    `;

  document.body.insertAdjacentHTML('beforeend', modalHtml);
  const modal = document.getElementById('markPaidConfirmModal');
  modal.showModal();

  modal.querySelector('.close-mark-paid-modal').onclick = function () {
    modal.close();
    modal.remove();
  };

  modal.querySelector('.confirm-mark-paid-modal').onclick = function () {
    modal.close();
    modal.remove();
    confirmMarkPaid(loanId);
  };
}

function confirmMarkPaid(loanId) {
  const formData = new URLSearchParams({
    action: 'update_status',
    loan_id: loanId
  });

  fetch('logic/manage_loans.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: formData.toString()
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        showStatusModal('success', 'Success!', data.message || 'Loan marked as paid');
        setTimeout(() => location.reload(), 1500);
      } else {
        showStatusModal('error', 'Error', data.message || 'Failed to update');
      }
    })
    .catch(() => showStatusModal('error', 'Error', 'Network error'));
}

function deleteLoan(loanId) {
  // Remove any existing modal first
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
                <h2 class="text-xl font-bold text-gray-800 text-center mb-6">Delete Loan?</h2>
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

  modal.querySelector('.close-delete-modal').onclick = function () {
    modal.close();
    modal.remove();
  };

  modal.querySelector('.confirm-delete-modal').onclick = function () {
    modal.close();
    modal.remove();
    confirmLoanDelete(loanId);
  };
}

function confirmLoanDelete(loanId) {
  fetch('logic/manage_loans.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=delete_loan&loan_id=${loanId}`
  })
    .then(res => res.json())
    .then(data => {
      console.log('Delete response:', data);
      if (data.status === 'success') {
        showStatusModal('success', 'Success!', data.message || 'Loan deleted');
        setTimeout(() => location.reload(), 1500);
      } else {
        showStatusModal('error', 'Error', data.message || 'Failed to delete');
      }
    })
    .catch(error => {
      console.error('Delete error:', error);
      showStatusModal('error', 'Error', 'Network error');
    });
}

function openUtangModal() {
  const modal = document.getElementById('addUtangModal');
  if (modal) modal.showModal();
}

function editLoan(loanId, personName, type, amount, dueDate) {
  console.log('editLoan called with:', { loanId, personName, type, amount, dueDate });

  // Ensure amount is a valid number
  const numericAmount = parseFloat(amount) || 0;

  // Remove existing modal if any
  const existingModal = document.getElementById('editLoanModal');
  if (existingModal) existingModal.remove();

  // Create and show edit modal using status modal as template
  const modalHtml = `
        <dialog id="editLoanModal" class="rounded-lg shadow-2xl max-w-md w-full">
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Edit Loan</h2>
                    <button class="close-edit-loan-modal text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="loanEditForm">
                    <input type="hidden" id="loanEditId" value="${loanId}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Person Name</label>
                        <input type="text" id="loanEditPersonName" value="" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select id="loanEditType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="Payable" ${type === 'Payable' ? 'selected' : ''}>Payable</option>
                            <option value="Receivable" ${type === 'Receivable' ? 'selected' : ''}>Receivable</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" id="loanEditAmount" value="" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                        <input type="date" id="loanEditDueDate" value="" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" class="close-edit-loan-modal flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </dialog>
    `;

  document.body.insertAdjacentHTML('beforeend', modalHtml);
  const modal = document.getElementById('editLoanModal');
  modal.showModal();

  // Event listeners
  modal.querySelectorAll('.close-edit-loan-modal').forEach(btn => {
    btn.onclick = function () {
      modal.close();
      modal.remove();
    };
  });

  // Ensure inputs receive the correct values (avoids attribute parsing issues)
  const personInput = document.getElementById('loanEditPersonName');
  const typeSelect = document.getElementById('loanEditType');
  const amountInput = document.getElementById('loanEditAmount');
  const dueDateInput = document.getElementById('loanEditDueDate');

  if (personInput) personInput.value = personName;
  if (typeSelect) typeSelect.value = type;
  if (amountInput) amountInput.value = numericAmount.toFixed(2);
  if (dueDateInput) dueDateInput.value = dueDate;

  // Verify the input has the value
  setTimeout(() => {
    const form = document.getElementById('loanEditForm');
    if (!form) return;

    form.onsubmit = function (e) {
      e.preventDefault();

      const personNameInput = document.getElementById('loanEditPersonName');
      const typeInput = document.getElementById('loanEditType');
      const amountInput = document.getElementById('loanEditAmount');
      const dueDateInput = document.getElementById('loanEditDueDate');

      const personName = personNameInput ? personNameInput.value.trim() : '';
      const amountValue = amountInput ? amountInput.value : '';
      const dueDate = dueDateInput ? dueDateInput.value : '';

      // Validation checks
      if (!personName) {
        showStatusModal('error', 'Validation Error', 'Please enter a person name');
        return;
      }

      if (!amountValue || parseFloat(amountValue) <= 0) {
        showStatusModal('error', 'Validation Error', 'Please enter a valid amount');
        return;
      }

      if (!dueDate) {
        showStatusModal('error', 'Validation Error', 'Please select a due date');
        return;
      }

      const formData = new URLSearchParams({
        action: 'update_loan',
        loan_id: loanId,
        person_name: personName,
        type: typeInput.value,
        amount: amountValue,
        due_date: dueDate
      });

      fetch('logic/manage_loans.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
      })
        .then(res => res.json())
        .then(data => {
          modal.close();
          modal.remove();
          if (data.status === 'success') {
            showStatusModal('success', 'Success!', data.message || 'Loan updated');
            setTimeout(() => location.reload(), 1500);
          } else {
            showStatusModal('error', 'Error', data.message || 'Failed to update');
          }
        })
        .catch((error) => {
          console.error('Fetch error:', error);
          modal.close();
          modal.remove();
          showStatusModal('error', 'Error', 'Network error');
        });
    };
  }, 100);
}

document.addEventListener('DOMContentLoaded', function () {
  // Open Utang Modal
  document.querySelectorAll('.open-utang-modal-btn').forEach(btn => {
    btn.addEventListener('click', openUtangModal);
  });

  // Event Delegation
  const loansContainer = document.querySelector('main'); // Use main to capture all grids/sections
  if (loansContainer) {
    loansContainer.addEventListener('click', function (e) {
      // Mark Paid
      const paidBtn = e.target.closest('.mark-paid-btn');
      if (paidBtn) {
        const id = paidBtn.dataset.id;
        markPaid(id);
      }

      // Edit Loan
      const editBtn = e.target.closest('.edit-loan-btn');
      if (editBtn) {
        const id = editBtn.dataset.id;
        const name = editBtn.dataset.name;
        const type = editBtn.dataset.type;
        const amount = editBtn.dataset.amount;
        const due = editBtn.dataset.due;
        editLoan(id, name, type, amount, due);
      }

      // Delete Loan
      const deleteBtn = e.target.closest('.delete-loan-btn');
      if (deleteBtn) {
        const id = deleteBtn.dataset.id;
        deleteLoan(id);
      }

      // Allow card click for Add Loan
      const addCard = e.target.closest('.add-loan-card');
      if (addCard) {
        openUtangModal();
      }

      // Add Payment
      const addPayBtn = e.target.closest('.add-payment-btn');
      if (addPayBtn) {
        const id = addPayBtn.dataset.id;
        openPaymentModal(id);
      }
    }); // Close click listener
  } // Close if statement
}); // Close DOMContentLoaded

// Open Add Payment Modal
function openPaymentModal(loanId) {
  const modal = document.getElementById('addLoanPaymentModal');
  if (modal) {
    document.getElementById('paymentLoanId').value = loanId;
    document.getElementById('paymentAmount').value = '';
    modal.showModal();
  }
}

// Handle Add Payment Form Submission
const addPaymentForm = document.getElementById('addLoanPaymentForm');
if (addPaymentForm) {
  addPaymentForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const loanId = document.getElementById('paymentLoanId').value;
    const amount = document.getElementById('paymentAmount').value;

    if (!amount || parseFloat(amount) <= 0) {
      showStatusModal('error', 'Error', 'Please enter a valid amount.');
      return;
    }

    const formData = new URLSearchParams({
      action: 'add_payment',
      loan_id: loanId,
      payment_amount: amount
    });

    fetch('logic/manage_loans.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString()
    })
      .then(res => res.json())
      .then(data => {
        const modal = document.getElementById('addLoanPaymentModal');
        if (modal) modal.close();

        if (data.status === 'success') {
          const message = data.status_code === 'Paid'
            ? 'Loan fully paid! Moving to history.'
            : 'Payment added successfully.';
          showStatusModal('success', 'Payment Added', message);
          setTimeout(() => location.reload(), 1500);
        } else {
          showStatusModal('error', 'Error', data.message || 'Failed to add payment.');
        }
      })
      .catch(err => {
        console.error(err);
        showStatusModal('error', 'Error', 'Network error occurred.');
      });
  });
}

