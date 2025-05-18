<?php include '../includes/config.php'; ?>

<body class="text-gray-900 min-h-screen flex flex-col">
    <main class="flex-grow flex items-center justify-center px-6 lg:px-24 py-12">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-10">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-6 text-center">
                Masuk ke SiLost
            </h1>
            <?php
            if (session_status() === PHP_SESSION_NONE)
                session_start();
            if (isset($_SESSION['error'])) {
                echo '<div class="text-red-600 text-sm mb-3 text-center">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>


            <form class="space-y-6" action="../actions/login.php" method="POST" novalidate>
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" placeholder="email@example.com" required
                        class="w-full rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi</label>
                    <input id="password" name="password" type="password" placeholder="Masukkan kata sandi" required
                        class="w-full rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="remember"
                            class="rounded border-gray-300 text-indigo-700 focus:ring-indigo-300" />
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="text-indigo-700 hover:underline">Lupa kata sandi?</a>
                </div>
                <button type="submit"
                    class="w-full bg-indigo-700 text-white font-semibold rounded-full py-3 hover:bg-indigo-800 transition-colors">
                    Masuk
                </button>
            </form>
            <p class="mt-6 text-center text-gray-500 text-xs">
                Belum punya akun?
                <a href="register.php" class="text-indigo-700 font-semibold hover:underline">Daftar sekarang</a>
            </p>
        </div>
    </main>
</body>