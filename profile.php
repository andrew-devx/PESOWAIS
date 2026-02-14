<?php
include 'logic/fetch_profile.php';
include 'includes/header.php';
?>

<main class="min-h-screen py-6 px-4 md:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Account Settings</h2>
            <p class="text-xs sm:text-sm text-gray-600">Manage your profile and security settings</p>
        </div>

        <!-- Profile Container -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-transparent rounded-xl border border-indigo-100 p-8">
                    <div class="flex flex-col items-center">
                        <img src="assets/images/profile.png" alt="profile-avatar" class="w-28 h-28 object-cover mb-6 jiggle-animation">
                        <h3 class="text-3xl font-bold text-gray-900 text-center mb-1"><?php echo htmlspecialchars($user['username']); ?></h3>
                        <p class="text-sm text-indigo-600 font-medium mb-4">Account Owner</p>
                        <p class="text-sm text-gray-600 text-center leading-relaxed mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                        <div class="text-xs text-gray-500 mt-3 flex items-center justify-center gap-1">
                            <i class="far fa-calendar"></i>
                            <span>Joined <?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>
                    <div class="border-t border-indigo-100 mt-7 pt-7">
                        <div class="flex items-center justify-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg hover:shadow-lg transition-shadow">
                            <i class="fas fa-check-circle text-green-600 mr-2 text-lg"></i>
                            <span class="text-sm font-bold text-green-700">Account Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Forms -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Change Username Section -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Username</h3>
                        <i class="fas fa-user-edit text-indigo-600"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Update your display name</p>
                    <form id="usernameForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Username</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Username</label>
                            <input type="text" id="newUsername" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter new username" required>
                            <p class="text-xs text-gray-500 mt-1">Must be 3-50 characters</p>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">Update Username</button>
                    </form>
                </div>

                <!-- Change Password Section -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Password</h3>
                        <i class="fas fa-lock text-red-600"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Update your account password</p>
                    <form id="passwordForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" id="currentPassword" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Enter current password" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" id="newPassword" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Enter new password" required>
                            <p class="text-xs text-gray-500 mt-1">At least 8 characters</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" id="confirmPassword" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Confirm new password" required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-colors">Update Password</button>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-red-800">Danger Zone</h3>
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <p class="text-sm text-red-700 mb-4">Log out from all devices or delete your account</p>
                    <div class="flex flex-col gap-3">
                        <button type="button" id="logoutAllDevicesBtn" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold transition-colors">Logout All Devices</button>
                        <button type="button" id="deleteAccountBtn" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-colors">Delete Account</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/profile.js"></script>

<?php include 'includes/footer.php'; ?>
