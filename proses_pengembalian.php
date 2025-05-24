<?php
session_start();
require 'config/db.php'; // koneksi database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barang_id = $_POST['barang_id'];
    
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $filename = uniqid() . '_' . basename($_FILES['bukti']['name']);
        $target_dir = __DIR__ . "/uploads/";
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
            // Update status jadi menunggu/pending + simpan nama file bukti
            $sql = "UPDATE barang SET status_pengembalian='pending', foto_bukti_pengembalian=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $filename, $barang_id);
            $stmt->execute();

            $_SESSION['success'] = "Bukti pengembalian berhasil diupload, sedang ditinjau admin.";
        } else {
            $_SESSION['error'] = "Gagal mengupload bukti pengembalian.";
        }
    } else {
        $_SESSION['error'] = "File bukti wajib diupload.";
    }

    header("Location: ../pages/detailbarang.php?id=$barang_id");
    exit;
}
?>
