<?php
require_once '../config/db.php';

if (isset($_GET['id']) && $_GET['action'] === 'approve') {
    $barangId = $_GET['id'];

    // Update status barang jadi 'disetujui'
    $stmt = $conn->prepare("UPDATE barang SET status = 'diterima', updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $barangId);

    if ($stmt->execute()) {
        // Ambil info pelapor dan nama barang
        $stmtGet = $conn->prepare("SELECT user_id, nama FROM barang WHERE id = ?");
        $stmtGet->bind_param("i", $barangId);
        $stmtGet->execute();
        $result = $stmtGet->get_result();
        $barang = $result->fetch_assoc();

        if ($barang) {
            $userId = $barang['user_id'];
            $namaBarang = $barang['nama'];
            $isiNotif = "Laporan barang '$namaBarang' telah disetujui oleh admin.";
            $status = 'unread';
            $tanggal = date('Y-m-d H:i:s');

            // Insert ke notifikasi
            $stmtNotif = $conn->prepare("INSERT INTO notifikasi (user_id, isi, status, tanggal) VALUES (?, ?, ?, ?)");
            $stmtNotif->bind_param("isss", $userId, $isiNotif, $status, $tanggal);
            $stmtNotif->execute();
        }

        // Redirect ke dashboard admin setelah approve
        header("Location: admin.php?status=approved");
        exit;
    } else {
        // Jika gagal update status
        header("Location: permintaan.php?error=failed_to_approve");
        exit;
    }
} else {
    // Jika akses tidak valid
    header("Location: admin.php?error=invalid_request");
    exit;
}
?>
