<?php
require_once '../config/db.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = trim($_GET['action']);

    if ($action === 'approve') {
        $status = 'approved';
    } elseif ($action === 'reject') {
        $status = 'rejected'; // <-- ini harus ditambah ke ENUM juga kalau mau pake
    }
    


    $stmt = $conn->prepare("UPDATE barang SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

header("Location: admin.php");
exit;
