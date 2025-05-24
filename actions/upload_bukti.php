<?php
session_start();
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header('Location: ../public/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_bukti']) && isset($_GET['barang_id'])) {
    $barangId = intval($_GET['barang_id']);
    $userId = $_SESSION['user_id'];

    // Pastikan barang itu milik user yang login
    $stmt = $conn->prepare("SELECT user_id FROM barang WHERE id = ?");
    $stmt->bind_param("i", $barangId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Barang tidak ditemukan.");
    }
    $barang = $result->fetch_assoc();
    if ($barang['user_id'] != $userId) {
        die("Kamu bukan pemilik barang ini.");
    }

    // Proses upload file
    $uploadDir = __DIR__ . '../uploads/';
    $fileTmp = $_FILES['foto_bukti']['tmp_name'];
    $fileName = uniqid('bukti_') . '_' . basename($_FILES['foto_bukti']['name']);
    $filePath = $uploadDir . $fileName;

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['foto_bukti']['type'], $allowedTypes)) {
        die("Tipe file tidak diizinkan.");
    }

    if (move_uploaded_file($fileTmp, $filePath)) {
        // Update database status dan foto bukti
        $stmt = $conn->prepare("UPDATE barang SET status_pengembalian = 'pending', foto_bukti_pengembalian = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $barangId);
        $stmt->execute();

        header("Location: detailbarang.php?id=$barangId&msg=upload_success");
        exit;
    } else {
        die("Upload gagal.");
    }
} else {
    die("Akses tidak valid.");
}
