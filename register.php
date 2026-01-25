<?php
require_once 'includes/connection.php';
require_once 'includes/header.php';
?>

<div class="bg-gray-50 min-h-[75vh] flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 sm:p-10">
    <div class="flex items-center gap-3 mb-6">
      <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 text-primary">
        <i class="fa-solid fa-user-plus text-xl"></i>
      </span>
      <div>
        <p class="text-sm uppercase tracking-wide text-accent font-semibold">Get Started</p>
        <h1 class="text-3xl font-bold text-primary">Create Your Account</h1>
      </div>
    </div>

    <form action="logic/register_process.php" method="POST" class="space-y-6">

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

<?php include 'includes/footer.php'; ?>
