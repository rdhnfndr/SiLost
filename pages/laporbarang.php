<?php
    include '../includes/config.php';
    include '../includes/header.php';
?>

<main class="flex-grow px-6 lg:px-24 py-12 bg-gray-50">
    <h1 class="text-3xl font-bold text-black-700 mb-6">Lapor Barang Hilang</h1>
    <form action="../actions/tambah_barang.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="nama" placeholder="Nama Barang" class="border p-2 rounded mb-4 w-full">
        <textarea name="deskripsi" placeholder="Deskripsi Barang" class="border p-2 rounded mb-4 w-full"></textarea>
        <input type="text" name="lokasi" placeholder="Lokasi Kehilangan" class="border p-2 rounded mb-4 w-full">
        <input type="date" name="tanggal_hilang" class="border p-2 rounded mb-4 w-full">
        <input type="file" name="foto" class="border p-2 rounded mb-4 w-full">
        <button type="submit" class="bg-indigo-700 text-white px-4 py-2 rounded-full">Lapor Barang</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>