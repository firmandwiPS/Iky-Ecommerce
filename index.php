<?php
session_start();
include 'config/app.php';

$title = 'KEDAISANTUY';
$session_id = session_id();

$daftar_kategori = [
    'Makanan' => 'fa-utensils',
    'Minuman' => 'fa-mug-hot',
    'Snack'   => 'fa-cookie-bite'
];

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

$keranjang_total = 0;
$result = mysqli_query($db, "SELECT SUM(jumlah) as total FROM keranjang WHERE session_id = '$session_id'");
if ($row = mysqli_fetch_assoc($result)) {
    $keranjang_total = $row['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-green-50 text-gray-800">

<!-- Navbar -->
<nav class="bg-green-900 text-white px-4 py-3 flex justify-between items-center">
    <div class="text-xl font-bold">KEDAISANTUY</div>
    <div class="flex items-center gap-4">
        <a href="login.php" class="bg-white text-green-800 px-3 py-1.5 rounded-full text-sm font-medium shadow">
            <i class="fas fa-user"></i>
        </a>
        <a href="keranjang.php" class="relative">
            <i class="fas fa-shopping-cart text-xl"></i>
            <?php if ($keranjang_total > 0): ?>
                <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs px-1.5 rounded-full"><?= $keranjang_total ?></span>
            <?php endif; ?>
        </a>
    </div>
</nav>

<!-- Pencarian -->
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
    <h2 class="text-xl font-bold mb-4">Kategori Menu</h2>
    <div class="flex gap-6 overflow-x-auto">
        <a href="index.php?kategori=Semua" class="flex flex-col items-center text-center">
            <div class="<?= $kategori_terpilih == 'Semua' ? 'bg-blue-100 text-blue-700' : 'bg-white text-blue-800'; ?> p-4 rounded-full border">
                <i class="fas fa-list text-xl"></i>
            </div>
            <span class="text-sm mt-1 font-medium">Semua</span>
        </a>
        <?php foreach ($daftar_kategori as $kategori => $ikon): ?>
            <a href="index.php?kategori=<?= urlencode($kategori); ?>" class="flex flex-col items-center text-center">
                <div class="<?= $kategori_terpilih == $kategori ? 'bg-blue-100 text-blue-700' : 'bg-white text-blue-800'; ?> p-4 rounded-full border">
                    <i class="fas <?= $ikon ?> text-xl"></i>
                </div>
                <span class="text-sm mt-1 font-medium"><?= $kategori ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Daftar Menu -->
<div class="px-4 pb-20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 bg-green-50">
    <?php if (mysqli_num_rows($menus) > 0): ?>
        <?php while ($menu = mysqli_fetch_assoc($menus)): ?>
            <div 
                x-data="{
                    open: false,
                    jumlah: 1,
                    tambahKeranjang() {
                        fetch('keranjang.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `menu_id=<?= $menu['id']; ?>&jumlah=${this.jumlah}`
                        })
                        .then(res => {
                            if (res.ok) {
                                localStorage.setItem('notif_keranjang', '1');
                                location.reload();
                            }
                        });
                    }
                }"
            >
                <div @click="open = true" class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition cursor-pointer">
                    <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-green-900 text-base"><?= htmlspecialchars($menu['nama_makanan']); ?></h3>
                        <p class="text-sm text-gray-500 line-clamp-2"><?= htmlspecialchars($menu['deskripsi']); ?></p>
                        <p class="text-green-700 font-bold mt-2">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>
                    </div>
                </div>

                <!-- Modal -->
                <div
                    x-show="open"
                    @keydown.escape.window="open = false"
                    @click.outside="open = false"
                    class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-end justify-center"
                    x-transition
                    style="display: none;"
                >
                    <div class="bg-white w-full max-w-md p-6 rounded-t-3xl">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="text-lg font-semibold"><?= htmlspecialchars($menu['nama_makanan']); ?></h2>
                            <button @click="open = false" class="text-gray-500 hover:text-red-500 transition">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" class="w-full h-44 object-cover rounded mb-3">
                        <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($menu['deskripsi']); ?></p>
                        <p class="font-bold text-black text-base mb-4">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>

                        <form @submit.prevent="tambahKeranjang" class="flex flex-col gap-3">
                            <label class="text-sm text-gray-700 block mb-1">Jumlah</label>
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" @click="if(jumlah > 1) jumlah--" class="bg-gray-200 text-xl w-10 h-10 rounded-full">−</button>
                                <input type="number" name="jumlah" x-model="jumlah" min="1" class="text-xl w-20 text-center border rounded p-2" required>
                                <button type="button" @click="jumlah++" class="bg-gray-200 text-xl w-10 h-10 rounded-full">+</button>
                            </div>
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

<!-- Notifikasi -->
<div
    x-data="{ showNotif: false }"
    x-init="
        if (localStorage.getItem('notif_keranjang')) {
            showNotif = true;
            setTimeout(() => {
                showNotif = false;
                localStorage.removeItem('notif_keranjang');
            }, 2000);
        }
    "
    x-show="showNotif"
    x-transition
    class="fixed bottom-6 inset-x-0 flex justify-center z-50"
    style="display: none;"
>
    <div class="bg-green-600 text-white py-2 px-4 rounded-lg shadow-lg">
        ✅ Menu berhasil ditambahkan ke keranjang!
    </div>
</div>

</body>
</html>
