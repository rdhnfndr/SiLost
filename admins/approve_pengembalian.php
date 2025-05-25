<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barang_id'])) {
    $barangId = $_POST['barang_id'];

    // Update status_pengembalian jadi 'dikembalikan'
    $update = $conn->prepare("UPDATE barang SET status_pengembalian = 'dikembalikan', tanggal_pengembalian = NOW() WHERE id = ?");
    $update->bind_param("i", $barangId);
    $update->execute();

    // Ambil data user pelapor dan nama barang
    $stmt = $conn->prepare("SELECT user_id, nama FROM barang WHERE id = ?");
    $stmt->bind_param("i", $barangId);
    $stmt->execute();
    $result = $stmt->get_result();
    $barang = $result->fetch_assoc();

    if ($barang) {
        $userId = $barang['user_id'];
        $namaBarang = $barang['nama'];

        // Isi notifikasi
        $isiNotif = "Permintaan pengembalian barang '$namaBarang' telah disetujui oleh admin.";
        $status = 'unread';
        $tanggal = date('Y-m-d H:i:s');

        // Masukkan notifikasi ke tabel notifikasi
        $stmt2 = $conn->prepare("INSERT INTO notifikasi (user_id, isi, status, tanggal) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $userId, $isiNotif, $status, $tanggal);
        $stmt2->execute();
    }

    // Redirect ke halaman permintaan dengan pesan sukses
    header("Location: permintaan.php?status=approved");
    exit;
} else {
    // Redirect kalau akses tidak valid
    header("Location: permintaan.php?error=invalid_request");
    exit;
}
?>
