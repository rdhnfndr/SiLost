<?php
// Contoh koneksi mysqli sederhana
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "silost_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
