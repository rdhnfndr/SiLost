<?php
include 'config/db.php';

// Data dummy
$nama = "User Dummy";
$email = "dummy@example.com";
$password = password_hash("password123", PASSWORD_DEFAULT); // enkripsi password

// Insert ke DB
$query = "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $nama, $email, $password);

if ($stmt->execute()) {
    echo "✅ User dummy berhasil ditambahkan.";
} else {
    echo "❌ Gagal insert: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
