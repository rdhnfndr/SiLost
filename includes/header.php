<?php
// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<body class="bg-white text-gray-900">
    <header class="flex flex-col sm:flex-row items-center justify-between px-6 lg:px-24 py-4 border-b border-gray-200">
        <a href="../pages/index.php" class="flex items-center space-x-1">
            <span class="font-extrabold text-2xl text-gray-900">Si</span>
            <span class="font-extrabold text-2xl text-indigo-700">Lost</span>
        </a>

        <form class="flex items-center flex-1 max-w-full mx-6 lg:mx-24">
            <label for="search" class="sr-only">Cari apapun disini...</label>
            <div class="relative w-full">
                <input id="search" type="search" placeholder="Cari apapun disini..."
                    class="w-full bg-gray-100 text-gray-400 text-sm rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            </div>
        </form>

        <nav class="flex items-center space-x-4 text-sm text-gray-400 whitespace-nowrap">
            <button class="flex items-center space-x-1 hover:text-gray-600">
                <i class="fas fa-globe"></i>
                <span>Bahasa Indonesia</span>
                <i class="fas fa-chevron-down text-xs"></i>
            </button>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <!-- Belum login -->
                <a href="/project/public/login.php"
                    class="flex items-center space-x-1 bg-indigo-100 text-indigo-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-indigo-200">
                    <i class="fas fa-sign-in-alt text-indigo-700"></i>
                    <span>Log in</span>
                </a>
                <a href="/project/public/register.php"
                    class="flex items-center space-x-1 bg-indigo-100 text-indigo-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-indigo-200">
                    <i class="fas fa-user-plus text-indigo-700"></i>
                    <span>Sign Up</span>
                </a>
            <?php else: ?>
                <!-- Udah login -->
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600 font-semibold text-sm">
                        👋 Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Pengguna'); ?>
                    </span>
                    <a href="../public/logout.php"
                        class="bg-red-100 text-red-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-red-200">
                        Logout
                    </a>
                </div>
            <?php endif; ?>
        </nav>
    </header>