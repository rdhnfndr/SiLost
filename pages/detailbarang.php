<?php
session_start();
require_once '../config/db.php';
include '../includes/config.php';
include '../includes/header.php';

// Ambil id barang dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek validitas ID
if ($id <= 0) {
    echo "<p class='text-red-500 text-center mt-10'>Barang tidak ditemukan.</p>";
    include '../includes/footer.php';
    exit;
}

// Ambil data barang + pelapor
$sql = "SELECT b.*, u.nama AS nama_pelapor, u.email, u.username 
        FROM barang b
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah barang ditemukan
if ($result->num_rows === 0) {
    echo "<p class='text-red-500 text-center mt-10'>Barang tidak ditemukan.</p>";
    include '../includes/footer.php';
    exit;
}

$barang = $result->fetch_assoc();
?>
<?php if (isset($_SESSION['notif'])): ?>
    <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
        <?= $_SESSION['notif'] ?>
    </div>
    <?php unset($_SESSION['notif']); ?>
<?php endif; ?>

<main class="flex-grow px-6 lg:px-24 py-12 bg-white max-w-5xl mx-auto">
    <!-- Tombol Kembali -->
    <button onclick="history.back()"
        class="mb-6 text-indigo-700 font-semibold hover:underline flex items-center space-x-2">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali</span>
    </button>

    <!-- Konten Detail Barang -->
    <article class="bg-white rounded-lg shadow p-8 flex flex-col md:flex-row gap-8">
        <img alt="Foto barang hilang" class="rounded-lg w-full md:w-1/2 object-cover"
            src="../uploads/<?= htmlspecialchars($barang['foto']) ?>" width="400" height="300" />
        <div class="flex flex-col justify-between w-full md:w-1/2">
            <div>
                <h1 class="text-3xl font-extrabold text-indigo-700 mb-4"><?= htmlspecialchars($barang['nama']) ?></h1>
                <p class="text-gray-600 mb-2"><span class="font-semibold">Tanggal Hilang:</span>
                    <?= date('d-m-Y', strtotime($barang['tanggal_hilang'])) ?></p>
                <p class="text-gray-600 mb-2"><span class="font-semibold">Lokasi Hilang:</span>
                    <?= htmlspecialchars($barang['lokasi']) ?></p>
                <p class="text-gray-700 mb-6"><?= nl2br(htmlspecialchars($barang['deskripsi'])) ?></p>
            </div>

            <!-- Kontak & Aksi -->
            <div class="bg-indigo-50 rounded-lg p-4">
                <h2 class="font-semibold text-indigo-700 mb-2">Kontak Pelapor</h2>
                <p class="text-gray-700"><?= htmlspecialchars($barang['nama_pelapor']) ?></p>
                <p class="text-gray-700">Email: <?= htmlspecialchars($barang['email']) ?></p>
                <p class="text-gray-700">Username: <?= htmlspecialchars($barang['username']) ?></p>

                <?php if ($_SESSION['user_id'] == $barang['user_id']): ?>
                    <p class="font-semibold text-indigo-700 mb-2">(Barang dilaporkan olehmu)</p>

                    <?php if ($barang['status_pengembalian'] == 'hilang' || $barang['status_pengembalian'] == 'ditolak' || $barang['status_pengembalian'] == 'none' || $barang['status_pengembalian'] == ''): ?>
                        <!-- Tombol untuk munculin form upload bukti -->
                        <button onclick="document.getElementById('form-kembali').classList.toggle('hidden')"
                            class="mt-4 w-full bg-yellow-500 text-white font-semibold py-2 rounded-full hover:bg-yellow-600 transition-colors">
                            Kembalikan barang
                        </button>

                        <!-- Form upload bukti pengembalian (default hidden) -->
                        <form id="form-kembali" method="POST" action="/proses_pengembalian.php" enctype="multipart/form-data"
                            class="mt-4 hidden">
                            <input type="hidden" name="barang_id" value="<?= htmlspecialchars($barang['id']) ?>">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Pengembalian</label>
                            <input type="file" name="bukti" required
                                class="block w-full mb-2 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" />
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-semibold py-2 rounded-full hover:bg-green-700 transition-colors">
                                Upload Bukti
                            </button>
                        </form>

                    <?php elseif ($barang['status_pengembalian'] == 'menunggu' || $barang['status_pengembalian'] == 'pending'): ?>
                        <!-- Status pengembalian menunggu review admin -->
                        <p class="mt-4 text-sm text-yellow-600 font-medium">Permintaan pengembalian sedang ditinjau admin.</p>
                        <?php if (!empty($barang['foto_bukti_pengembalian'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($barang['foto_bukti_pengembalian']) ?>"
                                alt="Bukti Pengembalian" class="mt-4 rounded-md max-w-xs" />
                        <?php endif; ?>

                    <?php elseif ($barang['status_pengembalian'] == 'diterima' || $barang['status_pengembalian'] == 'dikembalikan'): ?>
                        <!-- Status pengembalian diterima -->
                        <p class="mt-4 text-sm text-green-600 font-medium">Barang telah dikembalikan dan disetujui admin.</p>
                        <?php if (!empty($barang['foto_bukti_pengembalian'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($barang['foto_bukti_pengembalian']) ?>"
                                alt="Bukti Pengembalian" class="mt-4 rounded-md max-w-xs" />
                        <?php endif; ?>

                    <?php elseif ($barang['status_pengembalian'] == 'ditolak'): ?>
                        <!-- Status pengembalian ditolak -->
                        <p class="mt-4 text-sm text-red-600 font-medium">Pengembalian barang ditolak admin. Coba unggah bukti
                            baru.</p>
                        <?php if (!empty($barang['foto_bukti_pengembalian'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($barang['foto_bukti_pengembalian']) ?>"
                                alt="Bukti Pengembalian" class="mt-4 rounded-md max-w-xs" />
                        <?php endif; ?>
                        <!-- Berikan tombol & form upload ulang -->
                        <button onclick="document.getElementById('form-kembali').classList.toggle('hidden')"
                            class="mt-4 w-full bg-yellow-500 text-white font-semibold py-2 rounded-full hover:bg-yellow-600 transition-colors">
                            Unggah Bukti Baru
                        </button>
                        <form id="form-kembali" method="POST" action="../proses_pengembalian.php" enctype="multipart/form-data"
                            class="mt-4 hidden">
                            <input type="hidden" name="barang_id" value="<?= htmlspecialchars($barang['id']) ?>">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Pengembalian</label>
                            <input type="file" name="bukti" required
                                class="block w-full mb-2 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" />
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-semibold py-2 rounded-full hover:bg-green-700 transition-colors">
                                Upload Bukti
                            </button>
                        </form>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Tombol chat kalau bukan pelapor -->
                    <button
                        class="mt-4 w-full bg-indigo-700 text-white font-semibold py-2 rounded-full hover:bg-indigo-800 transition-colors"
                        onclick="window.location.href='chat.php?to=<?= $barang['user_id'] ?>'">
                        Chat Pelapor
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </article>
</main>


<?php include '../includes/footer.php'; ?>