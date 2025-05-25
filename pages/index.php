<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../public/login.php");
    exit();
}
include '../config/db.php';
include '../includes/config.php';
include '../includes/header.php';

// Ambil semua barang yang statusnya 'disetujui' dan belum dikembalikan
$query = "SELECT * FROM barang WHERE status = 'diterima' ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<main class="w-full px-6 lg:px-24 py-12 text-center">
    <div class="inline-flex items-center justify-center bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full px-3 py-1 mb-4">
        <i class="fas fa-plane-departure mr-1"></i>
        <span>Kejujuranmu membahagiakan!</span>
    </div>

    <h1 class="font-extrabold text-gray-900 text-4xl lg:text-6xl leading-tight mb-4 max-w-full">
        Temukan barangmu dan laporkan <br />barang hilang
    </h1>
    <p class="text-gray-500 text-sm lg:text-base mb-12 max-w-4xl mx-auto">
        Kalau nemu barang, jangan asal bawa pulang. Foto dulu, lapor di SiLost, dan amankan di pos keamanan. Kita bantu
        bareng-bareng.
    </p>

    <p class="text-indigo-700 font-semibold text-xs mb-1 tracking-widest">Pilihan Menu</p>
    <h2 class="font-extrabold text-gray-900 text-2xl mb-1">Cari atau Laporkan barang</h2>
    <p class="text-gray-600 text-xs mb-10">Pilih menu dibawah ini</p>

    <div class="flex flex-col sm:flex-row justify-center gap-6 max-w-4xl mx-auto">
        <button onclick="window.location.href='caribarang.php'"
            class="bg-white rounded-xl shadow-lg p-8 w-full sm:w-72 flex flex-col items-center space-y-4 hover:shadow-xl transition-shadow"
            type="button">
            <i class="fas fa-search text-indigo-900 text-6xl"></i>
            <p class="text-gray-500 text-xs text-center leading-tight max-w-[12rem]">
                Cari barangmu yang hilang melalui menu ini
            </p>
        </button>

        <button onclick="window.location.href='laporbarang.php'"
            class="bg-white rounded-xl shadow-lg p-8 w-full sm:w-72 flex flex-col items-center space-y-4 hover:shadow-xl transition-shadow"
            type="button">
            <i class="fas fa-plus text-indigo-900 text-6xl"></i>
            <p class="text-gray-500 text-xs text-center leading-tight max-w-[12rem]">
                Tambahkan barang yang kamu temukan melalui menu ini
            </p>
        </button>
    </div>

    <!-- Laporan Terbaru -->
    <section class="max-w-6xl mx-auto mt-16">
        <h2 class="font-extrabold text-gray-900 text-2xl mb-1">Laporan Barang Terbaru</h2>
        <div class="flex flex-wrap justify-center gap-6">
            <?php
            // Query barang yang sudah disetujui dan belum dikembalikan
            $latestQuery = "SELECT * FROM barang WHERE status = 'diterima' AND (status_pengembalian IS NULL OR status_pengembalian = '') ORDER BY created_at DESC LIMIT 3";
            $latestResult = $conn->query($latestQuery);

            if ($latestResult && $latestResult->num_rows > 0):
                while ($row = $latestResult->fetch_assoc()):
            ?>
                <div class="bg-white rounded-lg shadow-md p-4 text-left w-72 min-h-[380px] flex flex-col justify-between">
                    <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" 
                         alt="<?= htmlspecialchars($row['nama']) ?>" 
                         class="w-full h-40 object-cover rounded mb-3">
                    <h3 class="font-semibold text-lg text-gray-800"><?= htmlspecialchars($row['nama']) ?></h3>
                    <p class="text-sm text-gray-600 mb-2">
                        <?= htmlspecialchars(substr($row['deskripsi'], 0, 80)) ?>…
                    </p>
                    <a href="detailbarang.php?id=<?= $row['id'] ?>" class="text-indigo-700 text-sm hover:underline">
                        Lihat Detail
                    </a>
                </div>
            <?php endwhile; else: ?>
                <p class="text-gray-500 col-span-3 text-center">Belum ada laporan yang disetujui.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
