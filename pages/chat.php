<?php 
include '../includes/header.php'; 
include '../config/db.php';

$user_id = 1;  // ganti dengan ID pengguna yang login
$other_user_id = 2; // ganti dengan ID penerima pesan yang ingin di-chat

$query = "SELECT * FROM chat WHERE (pengirim_id = $user_id AND penerima_id = $other_user_id) OR (pengirim_id = $other_user_id AND penerima_id = $user_id)";
$result = $conn->query($query);
?>

<main class="flex-grow px-6 lg:px-24 py-12 bg-gray-50">
    <h1 class="text-3xl font-bold text-indigo-700 mb-6">Chat dengan Pengguna</h1>
    <div class="bg-white p-4 rounded-lg shadow-lg">
        <div class="overflow-y-auto max-h-60">
            <?php while($chat = $result->fetch_assoc()): ?>
                <p class="mb-2">
                    <span class="font-semibold"><?= $chat['pengirim_id'] == $user_id ? 'Anda' : 'Teman'; ?>:</span> <?= $chat['pesan']; ?>
                </p>
            <?php endwhile; ?>
        </div>
        <form action="actions/kirim_chat.php" method="POST">
            <textarea name="pesan" placeholder="Ketik pesan..." class="border p-2 rounded w-full"></textarea>
            <button type="submit" class="bg-indigo-700 text-white px-4 py-2 rounded-full mt-2">Kirim</button>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>