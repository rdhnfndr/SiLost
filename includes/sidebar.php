
<?php
// Query untuk menghitung jumlah permintaan pending
$sqlPending = "SELECT COUNT(*) as pending_count FROM barang WHERE status = 'pending'";
$resultPending = $conn->query($sqlPending);
$rowPending = $resultPending->fetch_assoc();
$pendingCount = $rowPending['pending_count'];
?>

<aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
    <nav class="flex flex-col py-6 px-4 space-y-2 text-gray-700 text-sm font-semibold">
        <?php
        $current = basename($_SERVER['PHP_SELF']);
        if (!function_exists('activeClass')) {
            function activeClass($page)
            {
                return basename($_SERVER['PHP_SELF']) == $page
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'hover:bg-indigo-50 hover:text-indigo-700';
            }
        }
        ?>
        <a href="../admins/admin.php"
            class="flex items-center space-x-3 px-3 py-2 rounded-lg <?= activeClass('dashboard.php') ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="../admins/permintaan.php"
            class="flex items-center space-x-3 px-3 py-2 rounded-lg <?= activeClass('permintaan.php') ?>">
            <i class="fas fa-search"></i>
            <span>Permintaan</span>
            <?php if ($pendingCount > 0): ?>
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>
        <a href="../admins/history.php"
            class="flex items-center space-x-3 px-3 py-2 rounded-lg <?= activeClass('history.php') ?>">
            <i class="fas fa-plus-circle"></i>
            <span>History</span>
        </a>
        <a href="../admins/manajemen.php"
            class="flex items-center space-x-3 px-3 py-2 rounded-lg <?= activeClass('manajemen.php') ?>">
            <i class="fas fa-users"></i>
            <span>Manajemen Pengguna</span>
        </a>
        <a href="../public/logout.php"
            class="flex items-center space-x-3 px-3 py-2 rounded-lg <?= activeClass('logout.php') ?>">
            <i class="fas fa-cogs"></i>
            <span>Log out</span>
        </a>
    </nav>
</aside>