<?php
include '../includes/header.php';
include '../includes/config.php';
include '../config/db.php';

$q = $_GET['q'] ?? '';

if ($q !== '') {
    $safeQ = $conn->real_escape_string($q);
    $sql = "SELECT * FROM barang 
            WHERE nama LIKE '%{$safeQ}%' 
              AND status = 'diterima' 
              AND (status_pengembalian IS NULL OR status_pengembalian = '') 
            ORDER BY created_at DESC";
    $res = $conn->query($sql);
} else {
    $res = null;
    // Ambil barang terbaru yang sudah diterima dan belum dikembalikan
    $latestSql = "SELECT * FROM barang 
                  WHERE status = 'diterima' 
                    AND (status_pengembalian IS NULL OR status_pengembalian = '') 
                  ORDER BY created_at DESC 
                  LIMIT 6";
    $latestRes = $conn->query($latestSql);
}
?>

<main class="p-6">
    <form method="GET" class="mb-6 flex gap-2">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari barang hilang..."
            class="border p-2 flex-grow rounded" />
        <button type="submit" class="bg-indigo-700 text-white px-4 py-2 rounded">Cari</button>
    </form>

    <?php if ($q !== '' && $res && $res->num_rows > 0): ?>
        <h2 class="text-xl font-semibold mb-4">Hasil Pencarian</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($row = $res->fetch_assoc()): ?>
                <div class="bg-white p-4 rounded shadow">
                    <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>"
                        class="w-full h-40 object-cover mb-2 rounded" />
                    <h2 class="font-semibold"><?= htmlspecialchars($row['nama']) ?></h2>
                    <p class="text-gray-600 text-sm"><?= htmlspecialchars(substr($row['deskripsi'], 0, 80)) ?>…</p>
                    <a href="detailbarang.php?id=<?= $row['id'] ?>" class="text-indigo-700 hover:underline text-sm">Detail</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php elseif ($q !== ''): ?>
        <p class="text-red-500">Tidak ditemukan hasil untuk “<?= htmlspecialchars($q) ?>”.</p>
    <?php else: ?>
        <h2 class="text-xl font-semibold mb-4">Laporan Terbaru</h2>
        <?php if (isset($latestRes) && $latestRes && $latestRes->num_rows > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php while ($row = $latestRes->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>"
                            class="w-full h-40 object-cover mb-2 rounded" />
                        <h2 class="font-semibold"><?= htmlspecialchars($row['nama']) ?></h2>
                        <p class="text-gray-600 text-sm"><?= htmlspecialchars(substr($row['deskripsi'], 0, 80)) ?>…</p>
                        <a href="detailbarang.php?id=<?= $row['id'] ?>" class="text-indigo-700 hover:underline text-sm">Detail</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500">Belum ada laporan yang tersedia.</p>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
