<?php
session_start();
require '../config/db.php';
include '../includes/config.php';
include '../includes/header_admin.php';

// Cek apakah kolom updated_at ada, kalau nggak ada, pake created_at sebagai gantinya
$checkUpdated = $conn->query("SHOW COLUMNS FROM barang LIKE 'updated_at'");
$orderBy = ($checkUpdated->num_rows > 0) ? "updated_at" : "created_at";

// Ambil data barang + nama user, status history
$sql = "SELECT b.*, u.nama AS nama_pelapor 
        FROM barang b 
        LEFT JOIN users u ON b.user_id = u.id 
        WHERE b.status IN ('diterima', 'ditolak', 'selesai', 'dikembalikan')
        ORDER BY b.$orderBy DESC";
$result = $conn->query($sql);
?>

<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <?php include '../includes/sidebar.php'; ?>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-6">History Barang</h2>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-200 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-6 py-3 text-left">Nama Barang</th>
                        <th class="px-6 py-3 text-left">Pelapor</th>
                        <th class="px-6 py-3 text-left">Bukti Pengembalian</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Tanggal Pengembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['nama_pelapor'] ?? 'Unknown') ?></td>

                            <td class="border px-4 py-2">
                                <?php if ($row['status'] === 'dikembalikan'): ?>
                                    <?php if (!empty($row['foto_bukti_pengembalian'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($row['foto_bukti_pengembalian']) ?>" 
                                             alt="Bukti" class="w-24 h-24 object-cover rounded shadow" />
                                    <?php else: ?>
                                        <span class="italic text-gray-400">Tidak ada bukti</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>

                            <td class="border px-4 py-2 font-semibold capitalize text-indigo-700">
                                <?= htmlspecialchars($row['status']) ?>
                            </td>

                            <td class="border px-4 py-2">
                                <?php
                                if ($row['status'] === 'dikembalikan' && !empty($row['tanggal_pengembalian'])) {
                                    echo date('d-m-Y H:i', strtotime($row['tanggal_pengembalian']));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
