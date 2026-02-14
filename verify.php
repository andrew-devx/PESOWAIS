<?php
require_once 'includes/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';
?>

<div class="bg-gray-50 min-h-[75vh] flex items-center justify-center px-4 pt-20">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 sm:p-10">
    <div class="flex items-center gap-3 mb-6">
      <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 text-primary">
        <i class="fa-solid fa-envelope-circle-check text-xl"></i>
      </span>
      <div>
        <p class="text-sm uppercase tracking-wide text-primary font-semibold">Email Verification</p>
        <h1 class="text-3xl font-bold text-primary">Verify Your Email</h1>
      </div>
    </div>

    <?php if(isset($_GET['email'])): ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
      <p class="text-sm text-green-800">
        <i class="fa-solid fa-circle-check mr-2"></i>
        <strong>Account created successfully!</strong> Please verify your email to complete registration.
      </p>
    </div>
    <?php endif; ?>

    <!-- Step 1: Request OTP -->
    <div id="requestOtpSection" class="space-y-6">
      <p class="text-sm text-gray-600">Enter your email address to receive a verification code.</p>
      
      <form id="requestOtpForm" class="space-y-6">
        <div>
          <label for="email" class="block text-sm font-medium text-primary">Email Address</label>
          <input 
            id="email" 
            type="email" 
            name="email" 
            required 
            autocomplete="email" 
            class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-primary shadow-sm focus:border-highlight focus:ring-2 focus:ring-highlight/30 transition"
            placeholder="your.email@example.com"
          />
        </div>

        <button 
          type="submit" 
          id="sendOtpBtn"
          class="w-full py-3 px-6 rounded-lg bg-primary text-white font-semibold shadow hover:bg-highlight transition flex items-center justify-center gap-2"
        >
          <i class="fa-solid fa-paper-plane"></i>
          Send Verification Code
        </button>
      </form>
    </div>

    <!-- Step 2: Verify OTP (Hidden by default) -->
    <div id="verifyOtpSection" class="space-y-6 hidden">
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
          <i class="fa-solid fa-circle-info mr-2"></i>
          We've sent a 6-digit code to <span id="sentEmail" class="font-semibold"></span>
        </p>
      </div>

      <form id="verifyOtpForm" class="space-y-6">
        <div>
          <label for="otp" class="block text-sm font-medium text-primary">Verification Code</label>
          <input 
            id="otp" 
            type="text" 
            name="otp" 
            required 
            maxlength="6"
            pattern="[0-9]{6}"
            class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-primary shadow-sm focus:border-highlight focus:ring-2 focus:ring-highlight/30 transition text-center text-2xl tracking-widest font-semibold"
            placeholder="000000"
          />
          <p class="mt-2 text-xs text-gray-500">Code expires in 10 minutes</p>
        </div>

        <button 
          type="submit" 
          id="verifyOtpBtn"
          class="w-full py-3 px-6 rounded-lg bg-primary text-white font-semibold shadow hover:bg-highlight transition flex items-center justify-center gap-2"
        >
          <i class="fa-solid fa-check-circle"></i>
          Verify Code
        </button>
      </form>

      <div class="text-center">
        <button 
          id="resendOtpBtn"
          class="text-sm text-primary hover:text-highlight font-semibold transition-colors"
        >
          <i class="fa-solid fa-rotate-right mr-1"></i>
          Resend Code
        </button>
      </div>
    </div>

    <!-- Back to Login Link -->
    <p class="mt-6 text-center text-sm text-gray-600">
      Already verified?
      <a href="login.php" class="font-semibold text-primary hover:text-highlight transition-colors">Sign in</a>
    </p>
  </div>
</div>

<!-- Success Modal -->
<dialog id="successModal">
    <div class="modal-content modal-content--small">
        <div class="modal-header">
            <h2>✅ Email Verified!</h2>
            <button class="modal-close" id="closeSuccessModalBtn">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="text-center py-4">
                <!-- Removed inline style -->
                <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 text-white mb-4 shadow-lg animate-bounce">
                    <i class="fa-solid fa-check text-4xl"></i>
                </div>
                <p class="text-gray-700 mb-2 font-semibold text-lg">Your email has been successfully verified!</p>
                <p class="text-sm text-gray-500">You can now access all features.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" id="goToLoginBtn">
                Go to Login
            </button>
        </div>
    </div>
</dialog>

<!-- Error Modal -->
<dialog id="errorModal">
    <div class="modal-content modal-content--small">
        <div class="modal-header">
            <h2>⚠️ Verification Error</h2>
            <button class="modal-close" id="closeErrorModalBtn">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="text-center py-4">
                <!-- Removed inline style -->
                <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-red-400 to-rose-600 text-white mb-4 shadow-lg animate-shake">
                    <i class="fa-solid fa-exclamation-triangle text-4xl"></i>
                </div>
                <p id="errorMessage" class="text-gray-700 font-semibold"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="tryAgainBtn">
                Try Again
            </button>
        </div>
    </div>
</dialog>

<?php include 'includes/footer.php'; ?>
<link rel="stylesheet" href="assets/css/modals.css">

<!-- New External Verify Script -->
<script src="assets/js/verify.js" defer></script>
</body>
</html>
