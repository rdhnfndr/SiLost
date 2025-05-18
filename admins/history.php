
<?php
require_once '../config/db.php';
session_start();

// Query untuk mendapatkan barang yang sudah di-approve atau di-reject
$sql = "SELECT * FROM barang WHERE status IN ('approved', 'rejected') ORDER BY tanggal_hilang DESC";
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
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">History Barang</h1>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-indigo-100 text-indigo-700 font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
                            <th class="px-6 py-3 text-left">Nama Barang</th>
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="<?= $row['status'] == 'approved' ? 'bg-green-50' : 'bg-red-50' ?>">
                                <td class="border px-4 py-2"><?= $row['id'] ?></td>
                                <td class="border px-4 py-2"><?= $row['nama'] ?></td>
                                <td class="border px-4 py-2"><?= date('d-m-Y', strtotime($row['tanggal_hilang'])) ?></td>
                                <td class="border px-4 py-2">
                                    <span class="<?= $row['status'] == 'approved' ? 'text-green-600' : 'text-red-600' ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<footer class="text-gray-400 text-xs lg:text-sm pt-6 text-center">
    &copy; 2024–2025 SiLost. All Rights Reserved.
</footer>