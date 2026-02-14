// Status Modal Function (Helper)
function showStatusModal(type, title, message) {
    const colors = {
        'success': { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-800', icon: 'fas fa-check-circle text-green-600' },
        'error': { bg: 'bg-red-50', border: 'border-red-200', text: 'text-red-800', icon: 'fas fa-exclamation-circle text-red-600' },
        'info': { bg: 'bg-blue-50', border: 'border-blue-200', text: 'text-blue-800', icon: 'fas fa-info-circle text-blue-600' }
    };

    const color = colors[type] || colors['info'];

    // Remove if exists
    const existing = document.getElementById('statusModal');
    if (existing) existing.remove();

    const modalHtml = `
        <dialog id="statusModal" class="rounded-lg shadow-2xl max-w-sm w-full">
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    <i class="${color.icon} text-2xl"></i>
                    <h2 class="text-lg font-bold ${color.text}">${title}</h2>
                </div>
                <p class="text-gray-600 mb-6">${message}</p>
                <button type="button" class="close-status-modal w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">Close</button>
            </div>
        </dialog>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = document.getElementById('statusModal');
    modal.showModal();

    modal.querySelector('.close-status-modal').onclick = function () {
        modal.close();
        modal.remove();
    };
}

document.addEventListener('DOMContentLoaded', function () {
    // Username Form Handler
    const usernameForm = document.getElementById('usernameForm');
    if (usernameForm) {
        usernameForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const newUsername = document.getElementById('newUsername').value.trim();

            if (!newUsername || newUsername.length < 3 || newUsername.length > 50) {
                showStatusModal('error', 'Error', 'Username must be 3-50 characters');
                return;
            }

            const formData = new URLSearchParams({
                action: 'update_username',
                new_username: newUsername
            });

            fetch('logic/update_profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showStatusModal('success', 'Success!', data.message || 'Username updated');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showStatusModal('error', 'Error', data.message || 'Failed to update username');
                    }
                })
                .catch(() => showStatusModal('error', 'Error', 'Network error'));
        });
    }

    // Password Form Handler
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!currentPassword) {
                showStatusModal('error', 'Error', 'Please enter your current password');
                return;
            }

            if (!newPassword || newPassword.length < 8) {
                showStatusModal('error', 'Error', 'New password must be at least 8 characters');
                return;
            }

            if (newPassword !== confirmPassword) {
                showStatusModal('error', 'Error', 'Passwords do not match');
                return;
            }

            if (currentPassword === newPassword) {
                showStatusModal('error', 'Error', 'New password must be different from current password');
                return;
            }

            const formData = new URLSearchParams({
                action: 'update_password',
                current_password: currentPassword,
                new_password: newPassword
            });

            fetch('logic/update_profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showStatusModal('success', 'Success!', data.message || 'Password updated');
                        document.getElementById('passwordForm').reset();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showStatusModal('error', 'Error', data.message || 'Failed to update password');
                    }
                })
                .catch(() => showStatusModal('error', 'Error', 'Network error'));
        });
    }

    // Logout All Devices
    const logoutBtn = document.getElementById('logoutAllDevicesBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            const confirmModal = `
                <dialog id="confirmLogoutModal" class="rounded-lg shadow-2xl max-w-sm w-full">
                    <div class="bg-white rounded-lg p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Logout All Devices?</h2>
                        <p class="text-gray-600 mb-6">You will be logged out from all devices. You'll need to sign in again.</p>
                        <div class="flex gap-3">
                            <button type="button" class="close-logout-modal flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">Cancel</button>
                            <button type="button" class="confirm-logout-action flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">Logout</button>
                        </div>
                    </div>
                </dialog>
            `;

            document.body.insertAdjacentHTML('beforeend', confirmModal);
            const modal = document.getElementById('confirmLogoutModal');
            modal.showModal();

            modal.querySelector('.close-logout-modal').onclick = function () {
                modal.close();
                modal.remove();
            };

            modal.querySelector('.confirm-logout-action').onclick = function () {
                modal.close();
                modal.remove();

                fetch('logic/logout.php', {
                    method: 'POST'
                })
                    .then(() => {
                        window.location.href = 'login.php';
                    })
                    .catch(() => showStatusModal('error', 'Error', 'Network error'));
            };
        });
    }

    // Delete Account
    const deleteAccountBtn = document.getElementById('deleteAccountBtn');
    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', function () {
            const confirmModal = `
                <dialog id="confirmDeleteModal" class="rounded-lg shadow-2xl max-w-sm w-full">
                    <div class="bg-white rounded-lg p-6">
                        <h2 class="text-xl font-bold text-red-800 mb-2">Delete Account?</h2>
                        <p class="text-gray-600 mb-4">This action cannot be undone. All your data will be permanently deleted.</p>
                        <div class="bg-red-50 border border-red-200 rounded p-3 mb-6">
                            <p class="text-sm text-red-700">
                                <strong>Warning:</strong> This will delete your account, all transactions, goals, loans, and subscriptions.
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" class="close-delete-modal flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">Cancel</button>
                            <button type="button" class="confirm-delete-action flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">Delete</button>
                        </div>
                    </div>
                </dialog>
            `;

            document.body.insertAdjacentHTML('beforeend', confirmModal);
            const modal = document.getElementById('confirmDeleteModal');
            modal.showModal();

            modal.querySelector('.close-delete-modal').onclick = function () {
                modal.close();
                modal.remove();
            };

            modal.querySelector('.confirm-delete-action').onclick = function () {
                modal.close();
                modal.remove();

                const formData = new URLSearchParams({
                    action: 'delete_account'
                });

                fetch('logic/update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showStatusModal('success', 'Account Deleted', 'Redirecting to login page...');
                            setTimeout(() => {
                                window.location.href = 'login.php';
                            }, 1500);
                        } else {
                            showStatusModal('error', 'Error', data.message || 'Failed to delete account');
                        }
                    })
                    .catch(() => showStatusModal('error', 'Error', 'Network error'));
            };
        });
    }
});
