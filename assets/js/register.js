/**
 * register.js
 * Handles registration page interactions, specifically error modals based on URL parameters.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Generic Close Handlers
    document.querySelectorAll('.modal-close, .close-modal-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const errorModal = document.getElementById('errorModal');
            if (errorModal) errorModal.close();
        });
    });
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');

    // Elements
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    const errorTitle = document.getElementById('errorTitle');
    const errorIconContainer = document.querySelector('#errorModal .inline-flex');
    const errorIcon = errorIconContainer ? errorIconContainer.querySelector('i') : null;

    if (error && errorModal) {
        // Reset base classes for icon container
        if (errorIconContainer) {
            errorIconContainer.className = 'inline-flex items-center justify-center h-20 w-20 rounded-full text-white mb-4 shadow-lg';
            // Default error style
            errorIconContainer.classList.add('bg-gradient-to-br', 'from-red-400', 'to-rose-600');
        }

        if (error === 'password_mismatch') {
            errorTitle.textContent = '⚠️ Password Mismatch';
            errorMessage.textContent = 'Passwords do not match. Please ensure both password fields are identical.';
            if (errorIconContainer) errorIconContainer.classList.add('animate-shake');
            if (errorIcon) errorIcon.className = 'fa-solid fa-exclamation-triangle text-4xl';

        } else if (error === 'duplicate_email') {
            const email = urlParams.get('email');
            errorTitle.textContent = '📧 Email Already Registered';
            errorMessage.innerHTML = `The email <strong>${email || 'you entered'}</strong> is already registered. Please use a different email or <a href="login.php" class="text-primary hover:text-highlight font-semibold underline">sign in</a> instead.`;

            // Info style
            if (errorIconContainer) {
                errorIconContainer.classList.remove('from-red-400', 'to-rose-600');
                errorIconContainer.classList.add('from-blue-400', 'to-indigo-600', 'animate-pulse');
            }
            if (errorIcon) errorIcon.className = 'fa-solid fa-info-circle text-4xl';

        } else if (error === 'registration_failed') {
            errorTitle.textContent = '❌ Registration Failed';
            errorMessage.textContent = 'Registration failed. Please try again or contact support if the issue persists.';
            if (errorIconContainer) errorIconContainer.classList.add('animate-shake');
            if (errorIcon) errorIcon.className = 'fa-solid fa-circle-xmark text-4xl';

        } else {
            errorTitle.textContent = '⚠️ Registration Error';
            errorMessage.textContent = 'An error occurred during registration. Please try again.';
            if (errorIconContainer) errorIconContainer.classList.add('animate-shake');
            if (errorIcon) errorIcon.className = 'fa-solid fa-exclamation-triangle text-4xl';
        }

        errorModal.showModal();
        errorModal.classList.add('modal-animate-in');
    }
});
