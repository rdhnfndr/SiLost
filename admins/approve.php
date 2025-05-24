<?php
require_once '../config/db.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE barang SET status = 'diterima' WHERE id = ?");
    } else if ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE barang SET status = 'ditolak' WHERE id = ?");
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        die("Gagal update: " . $stmt->error);
    }
}
?>
