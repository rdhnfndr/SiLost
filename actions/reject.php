<?php
require_once '../config/db.php';

if (isset($_GET['id']) && $_GET['action'] === 'reject') {
    $barangId = $_GET['id'];

    // Update status barang jadi 'ditolak'
    $stmt = $conn->prepare("UPDATE barang SET status = 'ditolak', updated_at = NOW() WHERE id = ?");
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
            $isiNotif = "Maaf, laporan barang '$namaBarang' telah ditolak oleh admin.";
            $status = 'unread';
            $tanggal = date('Y-m-d H:i:s');

            // Insert ke notifikasi
            $stmtNotif = $conn->prepare("INSERT INTO notifikasi (user_id, isi, status, tanggal) VALUES (?, ?, ?, ?)");
            $stmtNotif->bind_param("isss", $userId, $isiNotif, $status, $tanggal);
            $stmtNotif->execute();
        }

        // Redirect ke halaman permintaan setelah tolak
        header("Location: ../admins/permintaan.php?status=rejected");
        exit;
    } else {
        header("Location: ../admins/permintaan.php?error=failed_to_reject");
        exit;
    }
} else {
    header("Location: ../admins/permintaan.php?error=invalid_request");
    exit;
}
?>
