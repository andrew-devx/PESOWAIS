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
        <i class="fa-solid fa-right-to-bracket text-xl"></i>
      </span>
      <div>
        <p class="text-sm uppercase tracking-wide text-primary font-semibold">Welcome Back</p>
        <h1 class="text-3xl font-bold text-primary">Sign in to PesoWais</h1>
      </div>
    </div>

    <?php if(isset($_GET['verified']) && $_GET['verified'] === 'true'): ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
      <p class="text-sm text-green-800">
        <i class="fa-solid fa-circle-check mr-2"></i>
        <strong>Email verified successfully!</strong> You can now sign in to your account.
      </p>
    </div>
    <?php endif; ?>

    <form action="logic/login_code.php" method="POST" class="space-y-6">
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

      <div>
        <div class="flex items-center justify-between mb-2">
          <label for="password" class="block text-sm font-medium text-primary">Password</label>
          <a href="forgot_password.php" class="text-sm font-semibold text-highlight hover:text-accent transition-colors">Forgot password?</a>
        </div>
        <input 
          id="password" 
          type="password" 
          name="password" 
          required 
          autocomplete="current-password" 
          class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-primary shadow-sm focus:border-highlight focus:ring-2 focus:ring-highlight/30 transition"
          placeholder="Enter your password"
        />
      </div>

      <button 
        type="submit" 
        name="loginBTN"
        class="w-full py-3 px-6 rounded-lg bg-primary text-white font-semibold shadow hover:bg-highlight transition flex items-center justify-center gap-2"
      >
        <i class="fa-solid fa-right-to-bracket"></i>
        Sign in
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
      Don't have an account?
      <a href="register.php" class="font-semibold text-primary hover:text-highlight transition-colors">Create account</a>
    </p>
  </div>
</div>

<!-- Error Modal -->
<dialog id="errorModal">
    <div class="modal-content modal-content--small">
        <div class="modal-header">
            <h2 id="errorTitle">⚠️ Login Failed</h2>
            <button class="modal-close">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="text-center py-4">
                <!-- Icon container without inline styles -->
                <div id="errorIconContainer" class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-red-400 to-rose-600 text-white mb-4 shadow-lg">
                    <i id="errorIcon" class="fa-solid fa-lock text-4xl"></i>
                </div>
                <p id="errorHeading" class="text-gray-700 font-semibold mb-2 text-lg">Invalid Credentials</p>
                <p id="errorText" class="text-sm text-gray-500">The email or password you entered is incorrect.</p>
            </div>
        </div>
        <div class="modal-footer" id="errorFooter">
            <button class="btn btn-secondary close-modal-btn">
                Try Again
            </button>
            <a href="verify.php" class="btn btn-primary">
                Verify Email
            </a>
        </div>
    </div>
</dialog>

<?php include 'includes/footer.php'; ?>
<link rel="stylesheet" href="assets/css/modals.css">

<!-- New External Login Script -->
<script src="assets/js/login.js" defer></script>
</body>
</html>