/**
 * verify.js
 * Handles the email verification process.
 */

document.addEventListener('DOMContentLoaded', function () {
    const requestOtpForm = document.getElementById('requestOtpForm');
    const verifyOtpForm = document.getElementById('verifyOtpForm');
    const requestOtpSection = document.getElementById('requestOtpSection');
    const verifyOtpSection = document.getElementById('verifyOtpSection');
    const emailInput = document.getElementById('email');
    const otpInput = document.getElementById('otp');
    const sentEmailSpan = document.getElementById('sentEmail');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const successModal = document.getElementById('successModal');
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');

    // Check if email is provided in URL (from registration)
    const urlParams = new URLSearchParams(window.location.search);
    const emailFromUrl = urlParams.get('email');

    if (emailFromUrl && emailInput) {
        emailInput.value = decodeURIComponent(emailFromUrl);
        // Auto-send OTP
        setTimeout(() => {
            if (requestOtpForm) requestOtpForm.dispatchEvent(new Event('submit'));
        }, 500);
    }

    // Request OTP
    if (requestOtpForm) {
        requestOtpForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            sendOtpBtn.disabled = true;
            sendOtpBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';

            const formData = new FormData();
            formData.append('sendotp', '1');
            formData.append('email', emailInput.value);

            try {
                const response = await fetch('logic/sendotp.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Switch to verify section
                    requestOtpSection.classList.add('hidden');
                    verifyOtpSection.classList.remove('hidden');
                    sentEmailSpan.textContent = emailInput.value;
                    otpInput.focus();
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Failed to send OTP. Please try again.');
            } finally {
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Verification Code';
            }
        });
    }

    // Verify OTP
    if (verifyOtpForm) {
        verifyOtpForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            verifyOtpBtn.disabled = true;
            verifyOtpBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';

            const formData = new FormData();
            formData.append('verifyotp', '1');
            formData.append('otp', otpInput.value);

            try {
                const response = await fetch('logic/verify_logic.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    successModal.showModal();
                    successModal.classList.add('modal-animate-in');
                    setTimeout(() => {
                        window.location.href = 'login.php?verified=true';
                    }, 2000);
                } else {
                    showError(data.message);
                    otpInput.value = '';
                    otpInput.focus();
                }
            } catch (error) {
                showError('Verification failed. Please try again.');
            } finally {
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Verify Code';
            }
        });
    }

    // Resend OTP
    if (resendOtpBtn) {
        resendOtpBtn.addEventListener('click', async function () {
            resendOtpBtn.disabled = true;
            resendOtpBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Sending...';

            const formData = new FormData();
            formData.append('sendotp', '1');
            formData.append('email', emailInput.value);

            try {
                const response = await fetch('logic/sendotp.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Temporarily switch error modal to info mode for success message
                    showError('New code sent successfully!');

                    // Override error style to info style
                    const errorIconContainer = document.querySelector('#errorModal .inline-flex');
                    if (errorIconContainer) {
                        errorIconContainer.className = 'inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 text-white mb-4 shadow-lg';
                        errorIconContainer.classList.add('animate-pulse');
                        const icon = errorIconContainer.querySelector('i');
                        if (icon) icon.className = 'fa-solid fa-info-circle text-4xl';
                    }
                    const errorTitle = document.querySelector('#errorModal h2');
                    if (errorTitle) errorTitle.textContent = 'ℹ️ Info';

                    otpInput.value = '';
                    otpInput.focus();
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Failed to resend code. Please try again.');
            } finally {
                resendOtpBtn.disabled = false;
                resendOtpBtn.innerHTML = '<i class="fa-solid fa-rotate-right mr-1"></i> Resend Code';
            }
        });
    }

    // Auto-format OTP input
    if (otpInput) {
        otpInput.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    function showError(message) {
        errorMessage.textContent = message;

        // Reset to error style if it was changed
        const errorIconContainer = document.querySelector('#errorModal .inline-flex');
        if (errorIconContainer) {
            errorIconContainer.className = 'inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-red-400 to-rose-600 text-white mb-4 shadow-lg';
            errorIconContainer.classList.add('animate-shake');
            const icon = errorIconContainer.querySelector('i');
            if (icon) icon.className = 'fa-solid fa-exclamation-triangle text-4xl';
        }
        const errorTitle = document.querySelector('#errorModal h2');
        if (errorTitle) errorTitle.textContent = '⚠️ Error';

        const errorModal = document.getElementById('errorModal');
        if (errorModal && typeof errorModal.showModal === 'function') {
            errorModal.showModal();
            errorModal.classList.add('modal-animate-in');
        }
    }

    // Modal Event Listeners
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');
    if (closeSuccessModalBtn) {
        closeSuccessModalBtn.addEventListener('click', () => {
            const successModal = document.getElementById('successModal');
            if (successModal) successModal.close();
        });
    }

    const goToLoginBtn = document.getElementById('goToLoginBtn');
    if (goToLoginBtn) {
        goToLoginBtn.addEventListener('click', () => {
            window.location.href = 'login.php';
        });
    }

    const closeErrorModalBtn = document.getElementById('closeErrorModalBtn');
    if (closeErrorModalBtn) {
        closeErrorModalBtn.addEventListener('click', () => {
            const errorModal = document.getElementById('errorModal');
            if (errorModal) errorModal.close();
        });
    }

    const tryAgainBtn = document.getElementById('tryAgainBtn');
    if (tryAgainBtn) {
        tryAgainBtn.addEventListener('click', () => {
            const errorModal = document.getElementById('errorModal');
            if (errorModal) errorModal.close();
        });
    }
});
