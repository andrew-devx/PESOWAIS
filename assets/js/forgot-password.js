/**
 * forgot-password.js
 * Handles the multi-step password reset process.
 */

document.addEventListener('DOMContentLoaded', function() {
    const requestResetForm = document.getElementById('requestResetForm');
    const verifyResetForm = document.getElementById('verifyResetForm');
    const newPasswordForm = document.getElementById('newPasswordForm');
    const requestResetSection = document.getElementById('requestResetSection');
    const verifyResetSection = document.getElementById('verifyResetSection');
    const newPasswordSection = document.getElementById('newPasswordSection');
    const emailInput = document.getElementById('email');
    const otpInput = document.getElementById('otp');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const sentEmailSpan = document.getElementById('sentEmail');
    const sendResetBtn = document.getElementById('sendResetBtn');
    const verifyResetBtn = document.getElementById('verifyResetBtn');
    const resetPasswordBtn = document.getElementById('resetPasswordBtn');
    const resendResetBtn = document.getElementById('resendResetBtn');
    const successModal = document.getElementById('successModal');
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');

    // Request Reset Code
    if (requestResetForm) {
        requestResetForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            sendResetBtn.disabled = true;
            sendResetBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
            
            const formData = new FormData();
            formData.append('sendotp', '1');
            formData.append('email', emailInput.value);
            formData.append('type', 'reset'); // Mark as password reset
            
            try {
                const response = await fetch('logic/sendotp.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Switch to verify section
                    requestResetSection.classList.add('hidden');
                    verifyResetSection.classList.remove('hidden');
                    sentEmailSpan.textContent = emailInput.value;
                    otpInput.focus();
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Failed to send reset code. Please try again.');
            } finally {
                sendResetBtn.disabled = false;
                sendResetBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Reset Code';
            }
        });
    }

    // Verify Reset Code
    if (verifyResetForm) {
        verifyResetForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            verifyResetBtn.disabled = true;
            verifyResetBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';
            
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
                    // Switch to new password section
                    verifyResetSection.classList.add('hidden');
                    newPasswordSection.classList.remove('hidden');
                    newPasswordInput.focus();
                } else {
                    showError(data.message);
                    otpInput.value = '';
                    otpInput.focus();
                }
            } catch (error) {
                showError('Verification failed. Please try again.');
            } finally {
                verifyResetBtn.disabled = false;
                verifyResetBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Verify Code';
            }
        });
    }

    // Reset Password
    if (newPasswordForm) {
        newPasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                showError('Passwords do not match. Please try again.');
                return;
            }
            
            if (newPasswordInput.value.length < 6) {
                showError('Password must be at least 6 characters long.');
                return;
            }
            
            resetPasswordBtn.disabled = true;
            resetPasswordBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Resetting...';
            
            const formData = new FormData();
            formData.append('reset_password', '1');
            formData.append('new_password', newPasswordInput.value);
            
            try {
                const response = await fetch('logic/reset_password.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    successModal.showModal();
                    successModal.classList.add('modal-animate-in');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Failed to reset password. Please try again.');
            } finally {
                resetPasswordBtn.disabled = false;
                resetPasswordBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Reset Password';
            }
        });
    }

    // Resend Code
    if (resendResetBtn) {
        resendResetBtn.addEventListener('click', async function() {
            resendResetBtn.disabled = true;
            resendResetBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Sending...';
            
            const formData = new FormData();
            formData.append('sendotp', '1');
            formData.append('email', emailInput.value);
            formData.append('type', 'reset');
            
            try {
                const response = await fetch('logic/sendotp.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showError('New code sent successfully!'); // Using error modal for info is existing behavior
                    // Ideally we should use a success/info modal or toast, but following existing patterns for now.
                    // Let's improve this part by checking if we should use a different visual
                     const errorIconContainer = document.querySelector('#errorModal .inline-flex');
                     if(errorIconContainer) {
                         errorIconContainer.className = 'inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 text-white mb-4 shadow-lg';
                         errorIconContainer.classList.add('animate-pulse');
                         const icon = errorIconContainer.querySelector('i');
                         if(icon) icon.className = 'fa-solid fa-info-circle text-4xl';
                     }
                     const errorTitle = document.querySelector('#errorModal h2');
                     if(errorTitle) errorTitle.textContent = 'ℹ️ Info';

                    otpInput.value = '';
                    otpInput.focus();
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Failed to resend code. Please try again.');
            } finally {
                resendResetBtn.disabled = false;
                resendResetBtn.innerHTML = '<i class="fa-solid fa-rotate-right mr-1"></i> Resend Code';
            }
        });
    }

    // Auto-format OTP input
    if (otpInput) {
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    function showError(message) {
        errorMessage.textContent = message;
        
        // Reset to error style if it was changed
        const errorIconContainer = document.querySelector('#errorModal .inline-flex');
         if(errorIconContainer) {
             errorIconContainer.className = 'inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-red-400 to-rose-600 text-white mb-4 shadow-lg';
             errorIconContainer.classList.add('animate-shake');
             const icon = errorIconContainer.querySelector('i');
             if(icon) icon.className = 'fa-solid fa-exclamation-triangle text-4xl';
         }
         const errorTitle = document.querySelector('#errorModal h2');
         if(errorTitle) errorTitle.textContent = '⚠️ Error';

        errorModal.showModal();
        errorModal.classList.add('modal-animate-in');
    }
});
