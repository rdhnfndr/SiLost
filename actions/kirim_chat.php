<?php
include '../config/db.php';

$pengirim_id = 1;  // ganti dengan ID pengguna yang login
$penerima_id = 2; // ganti dengan ID penerima pesan
$pesan = $_POST['pesan'];

$query = "INSERT INTO chat (pengirim_id, penerima_id, pesan) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $pengirim_id, $penerima_id, $pesan);
$stmt->execute();

header("Location: ../pages/chat.php");
?>