<?php
// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

require_once __DIR__ . '/../config/db.php'; // pastikan path bener

// Fungsi cuma 1x di sini, jangan di tempat lain
function getUnreadMessagesCount($conn, $userId) {
    $sql = "SELECT COUNT(*) AS unread_count FROM chat WHERE penerima_id = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['unread_count'] ?? 0;
}

function getUnreadMessagesSenders($conn, $userId) {
    $sql = "SELECT DISTINCT pengirim_id FROM chat WHERE penerima_id = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $senders = [];
    while ($row = $result->fetch_assoc()) {
        $senders[] = $row['pengirim_id'];
    }
    return $senders;
}

$unreadCount = 0;
$unreadSenders = [];
if (isset($_SESSION['user_id'])) {
    $unreadCount = getUnreadMessagesCount($conn, $_SESSION['user_id']);
    $unreadSenders = getUnreadMessagesSenders($conn, $_SESSION['user_id']);
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
            <a href="../public/login.php"
                class="flex items-center space-x-1 bg-indigo-100 text-indigo-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-indigo-200">
                <i class="fas fa-sign-in-alt text-indigo-700"></i>
                <span>Log in</span>
            </a>
            <a href="../public/register.php"
                class="flex items-center space-x-1 bg-indigo-100 text-indigo-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-indigo-200">
                <i class="fas fa-user-plus text-indigo-700"></i>
                <span>Sign Up</span>
            </a>
        <?php else: ?>
            <!-- Udah login -->
            <div class="flex items-center space-x-3 relative">
                <span class="text-gray-600 font-semibold text-sm">
                    👋 Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Pengguna'); ?>
                </span>

                <!-- Notifikasi chat -->
                <div class="relative">
                    <button id="notif-btn" class="relative focus:outline-none" aria-label="Notifikasi Chat">
                        <i class="fas fa-comment-alt text-xl text-indigo-700"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span
                                class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                                <?= $unreadCount ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown notif chat -->
                    <div id="notif-dropdown" 
                         class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-300 rounded shadow-lg z-50 max-h-60 overflow-auto">
                        <ul>
                            <?php if ($unreadCount == 0): ?>
                                <li class="p-3 text-gray-500 text-center">Tidak ada pesan baru</li>
                            <?php else: ?>
                                <?php
                                foreach ($unreadSenders as $senderId):
                                    $stmt = $conn->prepare("SELECT nama FROM users WHERE id = ?");
                                    $stmt->bind_param("i", $senderId);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $sender = $res->fetch_assoc();
                                    $senderName = $sender ? htmlspecialchars($sender['nama']) : 'User';
                                ?>
                                    <li class="border-b last:border-none hover:bg-indigo-100 cursor-pointer">
                                        <a href="../pages/chat.php?to=<?= $senderId ?>" class="block p-3 text-indigo-700 font-semibold">
                                            Pesan dari <?= $senderName ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <a href="../public/logout.php"
                    class="bg-red-100 text-red-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-red-200">
                    Logout
                </a>
            </div>

            <script>
                const notifBtn = document.getElementById('notif-btn');
                const notifDropdown = document.getElementById('notif-dropdown');
                notifBtn.addEventListener('click', () => {
                    notifDropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', (e) => {
                    if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                        notifDropdown.classList.add('hidden');
                    }
                });
            </script>
        <?php endif; ?>
    </nav>
</header>
