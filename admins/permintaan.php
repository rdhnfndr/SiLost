
<?php
require_once '../config/db.php';
session_start();

$sql = "SELECT * FROM barang WHERE status = 'pending' ORDER BY tanggal_hilang DESC";
$result = $conn->query($sql);

if ($result === false) {
    die("Query error: " . $conn->error);
}

include '../includes/config.php';
include '../includes/header.php';
?>

<div class="flex min-h-screen bg-gray-50">
    <?php include '../includes/sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8">
        <section class="mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Laporan Pending</h1>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-indigo-100 text-indigo-700 font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
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
                                <td class="border px-4 py-2"><?= $row['nama'] ?></td>
                                <td class="border px-4 py-2"><?= date('d-m-Y', strtotime($row['tanggal_hilang'])) ?></td>
                                <td class="border px-4 py-2"><?= ucfirst($row['status']) ?></td>
                                <td class="border px-4 py-2">
                                    <div class="flex gap-2">
                                        <a href="approve.php?id=<?= $row['id'] ?>&action=approve"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">ACC</a>
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
    </main>
</div>