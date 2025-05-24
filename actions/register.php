<?php
include '../config/db.php';

// Mengambil data dari form
$nama = $_POST['nama'];
$email = $_POST['email'];
$username = $_POST['username'] ?? '';
$pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
$role = 'user'; // Menetapkan role sebagai 'user'

// Menyiapkan statement untuk memasukkan data ke dalam tabel users
$stmt = $conn->prepare("INSERT INTO users (nama, email, username, password, role) VALUES (?, ?, ?, ?, 'user')");
$stmt->bind_param("ssss", $nama, $email, $username, $pass);

// Menjalankan statement
if ($stmt->execute()) {
    // Jika berhasil, redirect ke halaman login
    header("Location: ../public/login.php");
    exit();
} else {
    // Jika gagal, bisa menampilkan pesan error atau melakukan penanganan lain
    echo "Error: " . $stmt->error;
}

// Menutup statement
$stmt->close();
$conn->close();
?>
