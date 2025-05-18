<?php
include 'config/db.php'; // pastiin path ini sesuai folder lu

// Coba ambil semua user
$query = "SELECT * FROM users";
$result = $conn->query($query);

if (!$result) {
    echo "Query error: " . $conn->error;
    exit;
}

if ($result->num_rows > 0) {
    echo "<h2>Data User:</h2>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Nama: " . $row['nama'] . " - Email: " . $row['email'] . "<br>";
    }
} else {
    echo "Belum ada data user di tabel.";
}

$conn->close();
?>
