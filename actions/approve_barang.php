<?php
require_once __DIR__ . '/../config/db.php';

// ambil id dan action
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id && ($action === 'approve' || $action === 'reject')) {
    // gunakan nilai enum yang ada di DB: 'approved' atau 'rejected'
    $newStatus = $action === 'approve' ? 'approved' : 'rejected';

    $stmt = $conn->prepare("UPDATE barang SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $id);
    $stmt->execute();
}

// setelah update, kembali ke daftar permintaan
header("Location: ../admins/permintaan.php");
exit;
