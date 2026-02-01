<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PesoWais</title>
    <link rel="icon" href="assets/images/logo2.svg" type="image/svg+xml" sizes="192x192" />
    <link rel="apple-touch-icon" href="assets/images/logo2.svg" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/tailwind-config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-gray-50">
    <nav class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-sm border-b border-gray-200 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">  
                
                <a href="dashboard.php" class="flex items-center gap-2">
                    <img src="assets/images/logo.svg" alt="PesoWais Logo" class="w-auto" style="max-height: 2rem;" />
                </a>

                
                <div class="hidden md:flex items-center gap-8">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="text-gray-600 hover:text-highlight transition-colors font-medium text-sm">Dashboard</a>
                        <a href="transactions.php" class="text-gray-600 hover:text-highlight transition-colors font-medium text-sm">History</a>
                        <a href="loans.php" class="text-gray-600 hover:text-highlight transition-colors font-medium text-sm">Loans</a>
                        <a href="goals.php" class="text-gray-600 hover:text-highlight transition-colors font-medium text-sm">Goals</a>
                    <?php endif; ?>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-gray-600 hover:text-highlight transition-colors font-medium text-sm">
                                <i class="fa-solid fa-user"></i>
                                <span><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-0 w-48 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="profile.php" class="block px-4 py-3 text-gray-600 hover:text-highlight hover:bg-gray-50 transition-colors text-sm border-b border-gray-100">My Profile</a>
                                <a href="logic/logout.php" class="block px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-gray-50 transition-colors text-sm">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="inline-flex items-center justify-center px-3 py-1.5 rounded-md bg-accent border border-primary text-white font-semibold text-sm hover:bg-highlight transition-colors">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            Login
                        </a>
                        <a href="register.php" class="inline-flex items-center justify-center px-3 py-1.5 rounded-md border border-primary text-primary font-semibold text-sm hover:bg-accent hover:text-white transition-colors">
                            <i class="fa-solid fa-user-plus mr-2"></i>
                            Register
                        </a>
                    <?php endif; ?>
                </div>

                <button id="mobileMenuBtn" class="md:hidden text-gray-600 hover:text-highlight">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>

            <div id="mobileMenu" class="hidden md:hidden border-t border-gray-200 py-4 space-y-2">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-colors">Dashboard</a>
                    <a href="transactions.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-colors">History</a>
                    <a href="loans.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-colors">Loans</a>
                    <a href="goals.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-colors">Goals</a>
                    <hr class="my-2">
                    <a href="profile.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-colors">My Profile</a>
                    <a href="logic/logout.php" class="block px-4 py-2 text-gray-600 hover:text-red-600 hover:bg-gray-50 rounded transition-colors">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="block px-4 py-2 rounded-md bg-primary border border-primary text-white font-semibold hover:bg-highlight transition-colors">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i>
                        Login
                    </a>
                    <a href="register.php" class="block px-4 py-2 rounded-md border border-primary text-primary font-semibold hover:bg-primary hover:text-white transition-colors">
                        <i class="fa-solid fa-user-plus mr-2"></i>
                        Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">

    <script src="assets/js/mobile-menu.js"></script>
</body>