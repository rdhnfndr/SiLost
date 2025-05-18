<?php
session_start();
include '../config/db.php';
include '../includes/header.php';

// Ambil data barang yang pending
$query = "SELECT b.*, u.nama AS nama_user FROM barang b JOIN users u ON b.user_id = u.id WHERE b.status = 'pending' ORDER BY b.id DESC";
$result = $conn->query($query);
?>

<main class="p-8 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-6">Persetujuan Barang Temuan</h1>
    <?php if ($result->num_rows > 0): ?>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            <thead class="bg-indigo-100 text-indigo-700 text-sm font-bold">
                <tr>
                    <th class="px-6 py-3 text-left">Nama Barang</th>
                    <th class="px-6 py-3 text-left">Pelapor</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Gambar</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['nama_user']); ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['tanggal_hilang']); ?></td>
                        <td class="px-6 py-4"><img src="../uploads/<?= $row['gambar']; ?>" width="80"></td>
                        <td class="px-6 py-4">
                            <form action="../actions/setujui_barang.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <button type="submit" class="text-green-600 hover:underline">Setujui</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-600">Belum ada barang yang perlu disetujui.</p>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
