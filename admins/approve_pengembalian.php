<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id = intval($_POST['barang_id']);
    $action = $_POST['action'] ?? '';

    if ($action === 'approve') {
        // Update status jadi 'dikembalikan' dan tanggal pengembalian sekarang
        $tanggalSekarang = date('Y-m-d H:i:s');
        $sql = "UPDATE barang SET status='dikembalikan', tanggal_pengembalian='$tanggalSekarang' WHERE id=$barang_id";
        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Pengembalian barang disetujui.";
        } else {
            $_SESSION['error'] = "Gagal menyetujui pengembalian: " . $conn->error;
        }
    } elseif ($action === 'reject') {
        // Hapus data barang
        $sql = "DELETE FROM barang WHERE id=$barang_id";
        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Pengembalian barang ditolak dan data dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus data barang: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Aksi tidak valid.";
    }
} else {
    $_SESSION['error'] = "Request tidak valid.";
}

// Redirect balik ke halaman permintaan pengembalian
header('Location: pengembalian.php');
exit;
