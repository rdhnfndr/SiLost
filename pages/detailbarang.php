<?php 
include 'includes/header.php'; 
include 'config/db.php';

$id = $_GET['id'];
$query = "SELECT * FROM barang WHERE id = $id";
$result = $conn->query($query);
$barang = $result->fetch_assoc();
?>

<main class="flex-grow px-6 lg:px-24 py-12 bg-gray-50">
    <h1 class="text-3xl font-bold text-indigo-700 mb-6"><?= $barang['nama']; ?></h1>
    <img src="<?= $barang['foto']; ?>" alt="Foto Barang" class="w-full h-60 object-cover mb-4">
    <p class="text-gray-600 mb-2"><span class="font-semibold">Tanggal Hilang:</span> <?= $barang['tanggal_hilang']; ?></p>
    <p class="text-gray-600 mb-2"><span class="font-semibold">Lokasi Hilang:</span> <?= $barang['lokasi']; ?></p>
    <p class="text-gray-700 mb-6"><?= $barang['deskripsi']; ?></p>
</main>

<?php include 'includes/footer.php'; ?>