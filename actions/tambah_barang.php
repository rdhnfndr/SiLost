<?php
session_start();
include_once(__DIR__ . '/../config/db.php');

// Ambil data dari form
$nama = $_POST['nama'];
$deskripsi = $_POST['deskripsi'];
$lokasi = $_POST['lokasi'];
$tanggal = $_POST['tanggal_hilang'];
$user_id = $_SESSION['user_id']; // Pastikan session ini diset saat login

// Upload file
$foto = $_FILES['foto']['name'];
$tmp = $_FILES['foto']['tmp_name'];
$path = '../uploads/' . $foto;

// Cek folder uploads
if (!is_dir('../uploads')) {
    mkdir('../uploads', 0777, true);
}

move_uploaded_file($tmp, $path);

// Insert ke database
$stmt = $conn->prepare("INSERT INTO barang (nama, deskripsi, lokasi, tanggal_hilang, foto, user_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
$stmt->bind_param("sssssi", $nama, $deskripsi, $lokasi, $tanggal, $foto, $user_id);
$stmt->execute();

header("Location: ../pages/index.php?success=1");
exit;
?>