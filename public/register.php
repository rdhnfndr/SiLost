<?php include '../config/db.php'; ?>
<?php include '../includes/config.php'; ?>

<body class="text-gray-900 min-h-screen flex flex-col">
    <main class="flex-grow flex items-center justify-center px-6 lg:px-24 py-12">
        <section class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-6 text-center">Daftar Akun Baru</h1>
            <form class="space-y-6" action="../actions/register.php" method="POST" novalidate>
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-semibold mb-1">Nama Lengkap</label>
                    <input id="name" name="nama" type="text" placeholder="Masukkan nama lengkap" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div>
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-1">Username</label>
                    <input id="username" name="username" type="text" placeholder="Masukkan username" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-1">Email</label>
                    <input id="email" name="email" type="email" placeholder="Masukkan email" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-1">Password</label>
                    <input id="password" name="password" type="password" placeholder="Masukkan password" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div>
                    <label for="confirm-password" class="block text-gray-700 text-sm font-semibold mb-1">Konfirmasi
                        Password</label>
                    <input id="confirm-password" name="confirm-password" type="password"
                        placeholder="Konfirmasi password" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <button type="submit"
                    class="w-full bg-indigo-700 text-white font-semibold py-2 rounded-full hover:bg-indigo-800 transition-colors">
                    Daftar
                </button>
            </form>

            <p class="mt-6 text-center text-gray-500 text-xs">
                Sudah punya akun?
                <a href="login.php" class="text-indigo-700 font-semibold hover:underline">Login di sini</a>
            </p>
        </section>
    </main>
</body>