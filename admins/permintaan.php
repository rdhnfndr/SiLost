<?php
require_once '../config/db.php';
session_start();

$sql = "SELECT * FROM barang WHERE status = 'pending' ORDER BY tanggal_hilang DESC";
$result = $conn->query($sql);

if ($result === false) {
    die("Query error: " . $conn->error);
}

include '../includes/config.php';
include '../includes/header_admin.php';
?>

<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <?php include '../includes/sidebar.php'; ?>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
        <section class="mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Laporan Pending</h1>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-indigo-100 text-indigo-700 font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
                            <th class="px-6 py-3 text-left">Foto</th>
                            <th class="px-6 py-3 text-left">Nama Barang</th>
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= $row['id'] ?></td>
                                <td class="border px-4 py-2">
                                    <?php if (!empty($row['foto'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" alt="Foto Barang"
                                            class="w-16 h-16 object-cover rounded" />
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Tidak ada foto</span>
                                    <?php endif; ?>
                                </td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="border px-4 py-2"><?= date('d-m-Y', strtotime($row['tanggal_hilang'])) ?></td>
                                <td class="border px-4 py-2"><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                                <td class="border px-4 py-2">
                                    <div class="flex gap-2">
                                        <a href="approve.php?id=<?= $row['id'] ?>&action=approve"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">Terima</a>
                                        <a href="approve.php?id=<?= $row['id'] ?>&action=reject"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Tolak</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-12">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-4">Permintaan Pengembalian Barang</h2>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-yellow-100 text-yellow-700 font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Nama Barang</th>
                            <th class="px-6 py-3 text-left">Pelapor</th>
                            <th class="px-6 py-3 text-left">Bukti</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pendingPengembalian = $conn->query("SELECT * FROM barang WHERE status_pengembalian='pending' OR status_pengembalian='menunggu'");
                        while ($row = $pendingPengembalian->fetch_assoc()):
                            // ambil nama user pelapor
                            $user_id = intval($row['user_id']);
                            $userRes = $conn->query("SELECT nama FROM users WHERE id=$user_id");
                            $user = $userRes->fetch_assoc();
                            ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($user['nama'] ?? 'Unknown') ?></td>
                                <td class="border px-4 py-2">
                                    <?php if (!empty($row['foto_bukti_pengembalian'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($row['foto_bukti_pengembalian']) ?>"
                                            alt="Bukti" class="w-24" />
                                    <?php else: ?>
                                        Tidak ada bukti
                                    <?php endif; ?>
                                </td>
                                <td class="border px-4 py-2 flex gap-2">
                                    <!-- Form Terima -->
                                    <form method="POST" action="approve_pengembalian.php"
                                        onsubmit="return confirm('Setujui pengembalian barang ini?');">
                                        <input type="hidden" name="barang_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">Terima</button>
                                    </form>

                                    <!-- Form Tolak -->
                                    <form method="POST" action="approve_pengembalian.php"
                                        onsubmit="return confirm('Tolak dan hapus pengembalian barang ini?');">
                                        <input type="hidden" name="barang_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Tolak</button>
                                    </form>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</main>
</div>