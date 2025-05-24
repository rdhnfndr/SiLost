<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header('Location: ../public/login.php');
    exit;
}

$myId = $_SESSION['user_id'];

$chatWithId = isset($_GET['to']) ? intval($_GET['to']) : 0;

// Cari user chat pertama kalau belum pilih
if ($chatWithId <= 0) {
    $sqlFirstChat = "
        SELECT u.id FROM users u
        JOIN chat c ON ( (c.pengirim_id = u.id AND c.penerima_id = ?) OR (c.penerima_id = u.id AND c.pengirim_id = ?) )
        WHERE u.id != ?
        GROUP BY u.id
        ORDER BY MAX(c.tanggal_kirim) DESC
        LIMIT 1
    ";
    $stmt = $conn->prepare($sqlFirstChat);
    $stmt->bind_param("iii", $myId, $myId, $myId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $chatWithId = $row['id'];
    } else {
        $chatWithId = 0;
    }
}

if ($chatWithId <= 0) {
    die("Belum ada chat sama siapa pun, coba mulai chat dulu.");
}

$stmt = $conn->prepare("SELECT id, nama, username FROM users WHERE id = ?");
$stmt->bind_param("i", $chatWithId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("User tidak ditemukan.");
}
$chatWithUser = $result->fetch_assoc();

// Kirim pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $pesan = trim($_POST['pesan']);
    if ($pesan !== '') {
        $pengirim_id = $myId;
        $penerima_id = $chatWithId;
        $tanggal_kirim = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO chat (pengirim_id, penerima_id, pesan, tanggal_kirim) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $pengirim_id, $penerima_id, $pesan, $tanggal_kirim);
        $stmt->execute();

        header("Location: chat.php?to=$chatWithId");
        exit;
    }
}

// Ambil daftar user yang pernah chat dengan gua, 1 user 1 kali, ambil pesan terakhir
$sqlChats = "
SELECT
    u.id,
    u.nama,
    c.pesan,
    c.tanggal_kirim as last_date
FROM users u
JOIN chat c ON c.id = (
    SELECT c2.id FROM chat c2
    WHERE ( (c2.pengirim_id = u.id AND c2.penerima_id = ?) OR (c2.penerima_id = u.id AND c2.pengirim_id = ?) )
    ORDER BY c2.tanggal_kirim DESC
    LIMIT 1
)
WHERE u.id != ?
ORDER BY c.tanggal_kirim DESC
";
$stmt = $conn->prepare($sqlChats);
$stmt->bind_param("iii", $myId, $myId, $myId);
$stmt->execute();
$result = $stmt->get_result();
$chatList = [];
while ($row = $result->fetch_assoc()) {
    $chatList[] = $row;
}

// Ambil chat pesan dari dan ke user terpilih, ORDER BY tanggal kirim ASC (paling lama atas)
$sqlMessages = "
SELECT c.*, u.nama AS pengirim_nama FROM chat c 
JOIN users u ON c.pengirim_id = u.id
WHERE (c.pengirim_id = ? AND c.penerima_id = ?) OR (c.pengirim_id = ? AND c.penerima_id = ?)
ORDER BY c.tanggal_kirim ASC
";
$stmt = $conn->prepare($sqlMessages);
$stmt->bind_param("iiii", $myId, $chatWithId, $chatWithId, $myId);
$stmt->execute();
$result = $stmt->get_result();
$chatMessages = [];
while ($row = $result->fetch_assoc()) {
    $chatMessages[] = $row;
}
?>
<body class="h-full flex flex-col">

<div class="flex flex-grow h-screen max-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-72 bg-white shadow-md flex flex-col">
        <header class="px-6 py-4 text-xl font-semibold border-b border-gray-300">Chats</header>
        <div class="flex-grow overflow-y-auto">
            <?php foreach ($chatList as $chat) : 
                $active = ($chat['id'] == $chatWithId) ? 'bg-indigo-100' : 'hover:bg-gray-100';
            ?>
                <a href="?to=<?= $chat['id'] ?>" class="block px-4 py-3 border-b border-gray-200 cursor-pointer <?= $active ?>">
                    <div class="font-semibold text-gray-800 truncate"><?= htmlspecialchars($chat['nama']) ?></div>
                    <div class="text-sm text-gray-500 truncate"><?= htmlspecialchars($chat['pesan']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex flex-col flex-grow bg-white shadow-md rounded-r-lg">
        <header class="flex items-center px-6 py-4 border-b border-gray-300 bg-indigo-50">
            <button onclick="window.location.href='index.php'" class="mr-4 text-indigo-600 hover:text-indigo-900" title="Kembali ke Home">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <h2 class="text-xl font-semibold text-gray-800">Chat dengan <?= htmlspecialchars($chatWithUser['nama']) ?></h2>
        </header>

        <main id="messages" class="flex-grow p-6 pt-8 pb-8 overflow-y-auto space-y-4 bg-gray-50">
            <?php if (count($chatMessages) === 0) : ?>
                <p class="text-center text-gray-500 italic">Belum ada pesan, mulai chat sekarang!</p>
            <?php else : ?>
                <?php foreach ($chatMessages as $msg) : 
                    $isSent = ($msg['pengirim_id'] == $myId);
                    $bgClass = $isSent ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-300 text-gray-900 rounded-bl-none';
                    $alignClass = $isSent ? 'self-end' : 'self-start';
                ?>
                    <div class="max-w-xs px-4 py-2 <?= $bgClass ?> <?= $alignClass ?> rounded-lg break-words">
                        <?= nl2br(htmlspecialchars($msg['pesan'])) ?>
                        <time class="block text-xs mt-1 text-gray-300 text-right"><?= date('d M H:i', strtotime($msg['tanggal_kirim'])) ?></time>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>

        <form id="chat-form" method="POST" action="?to=<?= $chatWithId ?>" class="flex border-t border-gray-300 p-4 bg-indigo-50">
            <textarea name="pesan" rows="2" required placeholder="Ketik pesan..." class="flex-grow resize-none p-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            <button type="submit" class="ml-4 bg-indigo-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-indigo-700 transition">Kirim</button>
        </form>
    </div>
</div>

</body>