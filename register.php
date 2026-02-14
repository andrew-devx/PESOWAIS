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
        <i class="fa-solid fa-user-plus text-xl"></i>
      </span>
      <div>
        <p class="text-sm uppercase tracking-wide text-primary font-semibold">Get Started</p>
        <h1 class="text-3xl font-bold text-primary">Create Your Account</h1>
      </div>
    </div>

    <form action="logic/register_code.php" method="POST" class="space-y-6">

        <div>
        <label for="email" class="block text-sm font-medium text-primary">Email address</label>
        <input 
          id="email" 
          type="email" 
          name="email" 
          required 
          autocomplete="email" 
          class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-primary shadow-sm focus:border-highlight focus:ring-2 focus:ring-highlight/30 transition"
          placeholder="Enter your email address"
        />
      </div>

      <div>
        <label for="username" class="block text-sm font-medium text-primary">Username</label>
        <input 
          id="username" 
          type="text" 
          name="username" 
          required 
          autocomplete="username" 
          class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-primary shadow-sm focus:border-highlight focus:ring-2 focus:ring-highlight/30 transition"
          placeholder="Choose a username"
        />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-primary">Password</label>
        <input 
          id="password" 
          type="password" 
          name="password" 
          required 
          autocomplete="new-password" 
          class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-primary shadow-sm focus:border-highlight focus:ring-2 focus:ring-highlight/30 transition"
          placeholder="Create a strong password"
        />
      </div>

      <div>
        <label for="confirm_password" class="block text-sm font-medium text-primary">Confirm Password</label>
        <input 
          id="confirm_password" 
          type="password" 
          name="confirm_password" 
          required 
          autocomplete="new-password" 
          class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-primary shadow-sm focus:border-highlight focus:ring-2 focus:ring-highlight/30 transition"
          placeholder="Re-enter your password"
        />
      </div>

      <button 
        type="submit" 
        class="w-full py-3 px-6 rounded-lg bg-primary text-white font-semibold shadow hover:bg-highlight transition flex items-center justify-center gap-2"
        name="registerBTN"
      >
        <i class="fa-solid fa-user-plus"></i>
        Create Account
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
      Already have an account?
      <a href="login.php" class="font-semibold text-primary hover:text-highlight transition-colors">Sign in</a>
    </p>
  </div>
</div>

<!-- Error Modal -->
<dialog id="errorModal">
    <div class="modal-content modal-content--small">
        <div class="modal-header">
            <h2 id="errorTitle">⚠️ Registration Error</h2>
            <button class="modal-close">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="text-center py-4">
                <!-- Icon container without inline styles -->
                <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-red-400 to-rose-600 text-white mb-4 shadow-lg">
                    <i class="fa-solid fa-exclamation-triangle text-4xl"></i>
                </div>
                <p id="errorMessage" class="text-gray-700 font-semibold"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary close-modal-btn">
                Try Again
            </button>
        </div>
    </div>
</dialog>

<?php include 'includes/footer.php'; ?>
<link rel="stylesheet" href="assets/css/modals.css">

<!-- New External Register Script -->
<script src="assets/js/register.js" defer></script>
</body>
</html>