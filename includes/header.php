<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user_id'] ?? null;

// Fungsi hitung unread chat
function getUnreadMessagesCount($conn, $userId) {
    $sql = "SELECT COUNT(*) AS unread_count FROM chat WHERE penerima_id = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['unread_count'] ?? 0;
}

// Fungsi ambil notif user
function getNotifikasiUser($conn, $userId, $limit = 5) {
    $sql = "SELECT * FROM notifikasi WHERE user_id = ? ORDER BY tanggal DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Hitung notif belum dibaca
function countNotifBelumDibaca($conn, $userId) {
    $sql = "SELECT COUNT(*) as jumlah FROM notifikasi WHERE user_id = ? AND status = 'unread'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['jumlah'] ?? 0;
}

// Update notif via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_read') {
    if (!$userId) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit;
    }

    $sql = "UPDATE notifikasi SET status = 'read' WHERE user_id = ? AND status = 'unread'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update']);
    }
    exit;
}

// Ambil data notif buat display
$unreadCount = 0;
$notifikasi = null;
$jumlahNotif = 0;
if ($userId) {
    $unreadCount = getUnreadMessagesCount($conn, $userId);
    $notifikasi = getNotifikasiUser($conn, $userId);
    $jumlahNotif = countNotifBelumDibaca($conn, $userId);
}
?>

<header class="flex flex-col sm:flex-row items-center justify-between px-6 lg:px-24 py-4 border-b border-gray-200">
    <a href="../pages/index.php" class="flex items-center space-x-1">
        <span class="font-extrabold text-2xl text-gray-900">Si</span>
        <span class="font-extrabold text-2xl text-indigo-700">Lost</span>
    </a>

    <form class="flex items-center flex-1 max-w-full mx-6 lg:mx-24">
        <div class="relative w-full">
            <input type="search" placeholder="Cari apapun disini..."
                class="w-full bg-gray-100 text-gray-400 text-sm rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>
    </form>

    <nav class="flex items-center space-x-4 text-sm text-gray-400 whitespace-nowrap">
        <?php if (!$userId): ?>
            <a href="../public/login.php" class="bg-indigo-100 text-indigo-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-indigo-200">
                <i class="fas fa-sign-in-alt"></i> Log in
            </a>
            <a href="../public/register.php" class="bg-indigo-100 text-indigo-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-indigo-200">
                <i class="fas fa-user-plus"></i> Sign Up
            </a>
        <?php else: ?>
            <div class="flex items-center space-x-3">
                <span class="text-gray-600 font-semibold text-sm">👋 Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>

                <!-- CHAT BUTTON -->
                <a href="../pages/chat.php" class="relative text-indigo-700 hover:text-indigo-900">
                    <i class="fas fa-comment-alt text-xl"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                            <?= $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- NOTIFIKASI UMUM -->
                <div class="relative">
                    <button id="notif-general-btn" class="relative focus:outline-none" type="button">
                        <i class="fas fa-bell text-xl text-gray-600 hover:text-indigo-700"></i>
                        <?php if ($jumlahNotif > 0): ?>
                            <span id="notif-count-badge" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                                <?= $jumlahNotif ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <div id="notif-general-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-gray-300 rounded shadow-lg z-50 max-h-60 overflow-auto">
                        <ul>
                            <?php if (!$notifikasi || $notifikasi->num_rows == 0): ?>
                                <li class="p-3 text-gray-500 text-center">Tidak ada notifikasi</li>
                            <?php else: ?>
                                <?php while ($notif = $notifikasi->fetch_assoc()): ?>
                                    <li class="border-b last:border-none px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($notif['isi']) ?><br>
                                        <small class="text-xs text-gray-400"><?= date('d M Y H:i', strtotime($notif['tanggal'])) ?></small>
                                    </li>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <a href="../public/logout.php" class="bg-red-100 text-red-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-red-200">
                    Logout
                </a>
            </div>

            <script>
                const notifBtn = document.getElementById('notif-general-btn');
                const notifDropdown = document.getElementById('notif-general-dropdown');
                const notifCountBadge = document.getElementById('notif-count-badge');

                // Toggle dropdown notif
                notifBtn.addEventListener('click', () => {
                    notifDropdown.classList.toggle('hidden');

                    // Kalau dropdown dibuka, gak usah sembunyiin badge
                    // Kalau dropdown ditutup, sembunyikan badge dan update status notif di backend
                    if (notifDropdown.classList.contains('hidden')) {
                        if (notifCountBadge) {
                            notifCountBadge.style.display = 'none';
                        }

                        fetch(window.location.href, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'action=mark_read'
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status !== 'success') {
                                console.error('Gagal update status notif');
                            }
                        })
                        .catch(e => console.error('Error:', e));
                    }
                });

                // Tutup dropdown jika klik di luar
                document.addEventListener('click', (e) => {
                    if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                        if (!notifDropdown.classList.contains('hidden')) {
                            notifDropdown.classList.add('hidden');

                            if (notifCountBadge) {
                                notifCountBadge.style.display = 'none';
                            }

                            fetch(window.location.href, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'action=mark_read'
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status !== 'success') {
                                    console.error('Gagal update status notif');
                                }
                            })
                            .catch(e => console.error('Error:', e));
                        }
                    }
                });
            </script>
        <?php endif; ?>
    </nav>
</header>
