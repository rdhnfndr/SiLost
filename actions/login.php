<?php
session_start();
include_once(__DIR__ . '/../config/db.php'); // path dibenerin

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] == 'admin') {
        header("Location: ../admins/admin.php");
    } else {
        header("Location: ../pages/index.php");
    }
    exit;
} else {
    // Login gagal
    header("Location: ../public/login.php?error=1");
    exit;
}
?>
