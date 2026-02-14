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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/tailwind-config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">

</head>
<body class="bg-gray-50 relative">
    <!-- Spotlight Mesh Gradient Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-0 -left-4 w-96 h-96 bg-gradient-radial from-[#0f3460]/40 via-[#1a1a2e]/25 to-transparent rounded-full blur-3xl animate-spotlight-1"></div>
        <div class="absolute top-1/4 right-0 w-[32rem] h-[32rem] bg-gradient-radial from-[#16213e]/35 via-[#0f3460]/20 to-transparent rounded-full blur-3xl opacity-80 animate-spotlight-2"></div>
        <div class="absolute bottom-0 left-1/3 w-[28rem] h-[28rem] bg-gradient-radial from-[#1a1a2e]/30 via-[#16213e]/15 to-transparent rounded-full blur-3xl animate-spotlight-3"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808018_1px,transparent_1px),linear-gradient(to_bottom,#80808018_1px,transparent_1px)] bg-[size:24px_24px]"></div>
    </div>
    

    
    <nav class="fixed top-4 left-1/2 -translate-x-1/2 w-[95%] max-w-7xl bg-white/70 backdrop-blur-md border-2 border-primary/60 shadow-lg rounded-full z-50 hidden md:block">
        <div class="px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">  
                
                <a href="index.php" class="flex items-center gap-2">
                    <img src="assets/images/logo.svg" alt="PesoWais Logo" class="w-auto mb-1" style="max-height: 2.5rem;" />
                </a>

                
                <div class="flex items-center gap-8">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="text-gray-600 hover:text-highlight transition-all duration-200 font-medium text-sm hover:scale-110 hover:drop-shadow-md">Dashboard</a>
                        <a href="transactions.php" class="text-gray-600 hover:text-highlight transition-all duration-200 font-medium text-sm hover:scale-110 hover:drop-shadow-md">History</a>
                        <a href="loans.php" class="text-gray-600 hover:text-highlight transition-all duration-200 font-medium text-sm hover:scale-110 hover:drop-shadow-md">Loans</a>
                        <a href="goals.php" class="text-gray-600 hover:text-highlight transition-all duration-200 font-medium text-sm hover:scale-110 hover:drop-shadow-md">Goals</a>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-3">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-gray-600 hover:text-highlight transition-all duration-200 font-medium text-sm hover:scale-105 hover:drop-shadow-md">
                                <i class="fa-solid fa-user"></i>
                                <span><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 group-hover:shadow-xl">
                                <a href="profile.php" class="block px-4 py-3 text-gray-600 hover:text-highlight hover:bg-blue-50 transition-all duration-200 text-sm border-b border-gray-100 hover:pl-5">My Profile</a>
                                <a href="logic/logout.php" class="block px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200 text-sm hover:pl-5">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-accent border border-primary text-white font-semibold text-sm hover:bg-highlight transition-colors">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            Login
                        </a>
                        <a href="register.php" class="inline-flex items-center justify-center px-4 py-2 rounded-full border border-primary text-primary font-semibold text-sm hover:bg-accent hover:text-white transition-colors">
                            <i class="fa-solid fa-user-plus mr-2"></i>
                            Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navbar (unchanged style) -->
    <nav class="fixed top-0 left-0 right-0 bg-white/70 backdrop-blur-md border-b-2 border-primary/60 shadow-lg z-50 md:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-center h-16">  
                
                <a href="dashboard.php" class="flex items-center gap-2">
                    <img src="assets/images/logo.svg" alt="PesoWais Logo" class="w-auto" style="max-height: 2rem;" />
                </a>

                <button id="mobileMenuBtn" class="text-gray-600 hover:text-highlight transition-all duration-200 hover:scale-125 hover:drop-shadow-md">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>

            <div id="mobileMenu" class="hidden border-t border-gray-200 py-4 space-y-2">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-all duration-200 hover:pl-6 hover:shadow-sm">Dashboard</a>
                    <a href="transactions.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-all duration-200 hover:pl-6 hover:shadow-sm">History</a>
                    <a href="loans.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-all duration-200 hover:pl-6 hover:shadow-sm">Loans</a>
                    <a href="goals.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-gray-50 rounded transition-all duration-200 hover:pl-6 hover:shadow-sm">Goals</a>
                    <hr class="my-2">
                    <a href="profile.php" class="block px-4 py-2 text-gray-600 hover:text-highlight hover:bg-blue-50 rounded transition-all duration-200 hover:pl-6 hover:shadow-sm">My Profile</a>
                    <a href="logic/logout.php" class="block px-4 py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded transition-all duration-200 hover:pl-6 hover:shadow-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="block px-4 py-2 rounded-full bg-primary border border-primary text-white font-semibold hover:bg-highlight transition-all duration-200 hover:shadow-lg hover:scale-105">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i>
                        Login
                    </a>
                    <a href="register.php" class="block px-4 py-2 rounded-full border border-primary text-primary font-semibold hover:bg-primary hover:text-white transition-all duration-200 hover:shadow-lg hover:scale-105">
                        <i class="fa-solid fa-user-plus mr-2"></i>
                        Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">

