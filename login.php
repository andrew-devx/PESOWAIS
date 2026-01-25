<?php
require_once 'includes/connection.php';
require_once 'includes/header.php';
?>

<div class="bg-gray-50 min-h-[75vh] flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 sm:p-10">
    <div class="flex items-center gap-3 mb-6">
      <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 text-primary">
        <i class="fa-solid fa-right-to-bracket text-xl"></i>
      </span>
      <div>
        <p class="text-sm uppercase tracking-wide text-accent font-semibold">Welcome Back</p>
        <h1 class="text-3xl font-bold text-primary">Sign in to PesoWais</h1>
      </div>
    </div>

    <form action="logic/login_process.php" method="POST" class="space-y-6">
      <div>
        <label for="username" class="block text-sm font-medium text-primary">Username</label>
        <input 
          id="username" 
          type="text" 
          name="username" 
          required 
          autocomplete="username" 
          class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-primary shadow-sm focus:border-highlight focus:ring-2 focus:ring-highlight/30 transition"
          placeholder="Username"
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

<?php include 'includes/footer.php'; ?>