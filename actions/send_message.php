<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'], $_POST['pesan'], $_POST['to'])) {
    header("Location: ../pages/chat.php?error=invalid");
    exit;
}

$pengirim_id = $_SESSION['user_id'];
$penerima_id = intval($_POST['to']);
$pesan = trim($_POST['pesan']);

if ($penerima_id <= 0 || empty($pesan)) {
    header("Location: ../pages/chat.php?error=kosong");
    exit;
}

$stmt = $conn->prepare("INSERT INTO chat (pengirim_id, penerima_id, pesan, tanggal_kirim, is_read) VALUES (?, ?, ?, NOW(), 0)");
$stmt->bind_param("iis", $pengirim_id, $penerima_id, $pesan);
$stmt->execute();

header("Location: ../pages/chat.php?to=$penerima_id");
exit;
