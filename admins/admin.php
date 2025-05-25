<?php
require_once '../config/db.php';
include '../includes/config.php';
include '../includes/header_admin.php';
?>

<div class="flex min-h-screen bg-gray-50">
    <?php include '../includes/sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Dashboard Admin</h1>

        <section class="mb-12">
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
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        // Query untuk mengambil barang yang disetujui tapi belum dikembalikan
                        $query = "SELECT * FROM barang 
          WHERE status = 'diterima' 
            AND (status_pengembalian IS NULL OR status_pengembalian != 'dikembalikan') 
          ORDER BY created_at DESC";
                        $result = $conn->query($query);

                        // Cek apakah ada hasil
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='px-6 py-4'>{$row['id']}</td>";
                                echo "<td class='px-6 py-4'>{$row['nama']}</td>";
                                echo "<td class='px-6 py-4'>{$row['created_at']}</td>";
                                echo "<td class='px-6 py-4 text-green-600'>Disetujui</td>";
                                echo "<td class='px-6 py-4'>
                <a href='?delete={$row['id']}' class='bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus laporan ini?\")'>Hapus</a>
              </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='px-6 py-4 text-center text-gray-500'>Tidak ada laporan yang aktif.</td></tr>";
                        }

                        // Handle proses penghapusan
                        if (isset($_GET['delete'])) {
                            $delete_id = $_GET['delete'];
                            // Hapus data barang dari database
                            $delete_query = "DELETE FROM barang WHERE id = ?";
                            if ($stmt = $conn->prepare($delete_query)) {
                                $stmt->bind_param("i", $delete_id);
                                if ($stmt->execute()) {
                                    echo "<script>alert('Laporan berhasil dihapus!'); window.location.href='admin.php';</script>";
                                } else {
                                    echo "<script>alert('Terjadi kesalahan dalam menghapus laporan.');</script>";
                                }
                                $stmt->close();
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="text-gray-400 text-xs lg:text-sm pt-6 text-center">
            &copy; 2024–2025 SiLost. All Rights Reserved.
        </footer>
    </main>
</div>