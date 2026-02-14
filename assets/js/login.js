/**
 * login.js
 * Handles login page interactions, specifically the error/info modals based on URL parameters.
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
    const errorTitle = document.getElementById('errorTitle');
    const errorHeading = document.getElementById('errorHeading');
    const errorText = document.getElementById('errorText');
    const errorIcon = document.getElementById('errorIcon');
    const errorIconContainer = document.getElementById('errorIconContainer');
    const errorFooter = document.getElementById('errorFooter');

    // Event Delegation for dynamic footer buttons
    if (errorFooter) {
        errorFooter.addEventListener('click', function (e) {
            // Close button
            if (e.target.closest('.close-modal-btn')) {
                errorModal.close();
            }
        });
    }

    if (error && errorModal) {
        if (error === 'email_not_verified') {
            const email = urlParams.get('email');

            errorTitle.textContent = '📧 Email Not Verified';
            errorHeading.textContent = 'Please Verify Your Email';
            errorText.textContent = 'Your account was created but email is not verified. Please verify your email to continue.';

            // Icon
            errorIcon.className = 'fa-solid fa-envelope-circle-check text-4xl';

            // Container Styling & Animation
            errorIconContainer.className = 'inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 text-white mb-4 shadow-lg';
            // Using classList instead of inline style for animation
            errorIconContainer.classList.add('animate-pulse');

            // Update footer buttons
            errorFooter.innerHTML = `
                <button class="btn btn-secondary close-modal-btn">
                    Cancel
                </button>
                <a href="verify.php?email=${encodeURIComponent(email || '')}" class="btn btn-primary">
                    Verify Now
                </a>
            `;
        } else if (error === 'invalid_credentials') {
            errorTitle.textContent = '⚠️ Login Failed';
            errorHeading.textContent = 'Invalid Credentials';
            errorText.textContent = 'The email or password you entered is incorrect.';

            // Icon
            errorIcon.className = 'fa-solid fa-lock text-4xl';

            // Container Styling & Animation
            errorIconContainer.className = 'inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-red-400 to-rose-600 text-white mb-4 shadow-lg';
            // Using classList for animation
            errorIconContainer.classList.add('animate-shake');

            // Reset footer to default
            errorFooter.innerHTML = `
                <button class="btn btn-secondary close-modal-btn">
                    Try Again
                </button>
            `;
        }

        // Show the modal with a slight delay for smoother entrance if needed, or immediately
        errorModal.showModal();
        errorModal.classList.add('modal-animate-in');
    }
});
