<?php
session_start();
include 'config/app.php';

$title = 'KEDAISANTUY';
$keranjang_total = array_sum($_SESSION['keranjang'] ?? []);

$kategori_terpilih = $_GET['kategori'] ?? 'Semua';
$keyword = $_GET['cari'] ?? '';

$where = "1";
if ($kategori_terpilih != 'Semua') {
    $kategori_sql = mysqli_real_escape_string($db, $kategori_terpilih);
    $where .= " AND kategori = '$kategori_sql'";
}
if ($keyword != '') {
    $keyword_sql = mysqli_real_escape_string($db, $keyword);
    $where .= " AND nama_makanan LIKE '%$keyword_sql%'";
}

$menus = mysqli_query($db, "SELECT * FROM makanan WHERE $where ORDER BY id DESC");

// Daftar kategori manual
$daftar_kategori = [
    'Makanan' => 'fa-utensils',
    'Minuman' => 'fa-mug-hot',
    'Snack'   => 'fa-cookie-bite'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-green-50 text-gray-800">

<!-- Navbar -->
<nav class="bg-green-900 text-white px-4 py-3 flex justify-between items-center">
    <div class="flex items-center gap-2">
        <span class="text-xl font-bold">KEDAISANTUY</span>
    </div>
    <div class="flex items-center gap-4">
        <a href="login.php" class="flex items-center gap-1 bg-white text-green-800 px-3 py-1.5 rounded-full text-sm font-medium shadow hover:shadow-md transition">
    <i class="fas fa-user text-sm"></i>
</a>

        <a href="keranjang.php" class="relative">
            <i class="fas fa-shopping-cart text-white text-xl"></i>
            <?php if ($keranjang_total > 0): ?>
                <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs px-1.5 rounded-full"><?= $keranjang_total; ?></span>
            <?php endif; ?>
        </a>
    </div>
</nav>

<!-- Search -->
<div class="bg-green-900 px-4 py-4">
    <form method="GET" class="flex items-center gap-2">
        <input
            type="text"
            name="cari"
            placeholder="Cari menu..."
            value="<?= htmlspecialchars($keyword); ?>"
            class="w-full px-4 py-2 rounded-full text-sm shadow focus:outline-none"
        >
        <button type="submit" class="text-white text-xl">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<!-- Kategori -->
<div class="px-4 py-6 bg-green-50 rounded-t-3xl -mt-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Kategori Menu</h2>
    </div>

    <div class="flex gap-6">
        <!-- Semua -->
        <a href="index.php?kategori=Semua" class="flex flex-col items-center text-center">
            <div class="<?= $kategori_terpilih == 'Semua' ? 'bg-blue-100 text-blue-700' : 'bg-white text-blue-800'; ?> p-4 rounded-full border">
                <i class="fas fa-list text-xl"></i>
            </div>
            <span class="text-sm mt-1 font-medium <?= $kategori_terpilih == 'Semua' ? 'text-blue-700' : 'text-blue-800'; ?>">Semua</span>
        </a>

        <?php foreach ($daftar_kategori as $kategori => $ikon): ?>
            <a href="index.php?kategori=<?= urlencode($kategori); ?>" class="flex flex-col items-center text-center">
                <div class="<?= $kategori_terpilih == $kategori ? 'bg-blue-100 text-blue-700' : 'bg-white text-blue-800'; ?> p-4 rounded-full border">
                    <i class="fas <?= $ikon ?> text-xl"></i>
                </div>
                <span class="text-sm mt-1 font-medium <?= $kategori_terpilih == $kategori ? 'text-blue-700' : 'text-blue-800'; ?>"><?= $kategori ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Daftar Menu -->
<div class="px-4 pb-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 bg-green-50">
    <?php if (mysqli_num_rows($menus) > 0): ?>
        <?php while ($menu = mysqli_fetch_assoc($menus)): ?>
            <div x-data="{ open: false, jumlah: 1 }">
                <!-- Card -->
                <div @click="open = true" class="bg-white rounded-xl shadow p-4 cursor-pointer hover:shadow-lg transition">
                    <div class="relative">
                        <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" class="w-full h-40 object-cover rounded-lg">
                        <?php if ($menu['kategori'] === 'Snack'): ?>
                            <span class="absolute top-2 left-2 bg-lime-600 text-white text-xs px-2 py-0.5 rounded font-semibold shadow">
                                Recommended
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-3">
                        <h3 class="font-semibold text-green-900 text-sm"><?= htmlspecialchars($menu['nama_makanan']); ?></h3>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($menu['deskripsi']); ?></p>
                        <p class="text-green-800 font-bold mt-1">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>
                    </div>
                </div>

                <!-- Modal -->
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-y-full opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-end justify-center"
                    style="display: none;"
                >
                    <div class="bg-white w-full max-w-md p-6 rounded-t-3xl">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-lg font-semibold"><?= htmlspecialchars($menu['nama_makanan']); ?></h2>
                            <button @click="open = false" class="text-gray-500 hover:text-red-500">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" class="w-full h-40 object-cover rounded mb-3">
                        <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($menu['deskripsi']); ?></p>
                        <p class="font-bold text-black text-base mb-4">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>
                        <form action="keranjang.php" method="post" class="flex flex-col gap-3">
                            <input type="hidden" name="menu_id" value="<?= $menu['id']; ?>">
                            <label class="text-sm text-gray-700">Jumlah</label>
                            <input type="number" name="jumlah" x-model="jumlah" min="1" class="w-24 border p-2 rounded text-center" required>
                            <button type="submit" class="bg-green-700 text-white py-2 rounded-lg hover:bg-green-800 transition">
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-full text-center text-gray-500">Menu tidak ditemukan.</div>
    <?php endif; ?>
</div>

</body>
</html>
