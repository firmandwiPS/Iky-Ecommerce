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

// Ambil recommended menu
$recommendedMenus = mysqli_query($db, "SELECT * FROM makanan WHERE $where AND recommended = 'Ya' ORDER BY id DESC");
// Ambil semua menu termasuk yang recommended
$allMenus = mysqli_query($db, "SELECT * FROM makanan WHERE $where ORDER BY id DESC");

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-blue-50 text-gray-800">

<!-- Navbar -->
<nav x-data="{ atTop: true }" x-init="atTop = window.scrollY <= 0; window.addEventListener('scroll', () => { atTop = window.scrollY <= 0; });"
     :class="atTop ? 'bg-blue-900 bg-opacity-100' : 'bg-blue-900 bg-opacity-70 backdrop-blur-md shadow-md'"
     class="fixed top-0 left-0 right-0 z-50 text-white px-4 py-3 flex justify-between items-center transition-all duration-300 ease-in-out">
    <div class="text-xl font-bold">KEDAISANTUY</div>
    <div class="flex items-center gap-4">
        <a href="login.php" class="bg-white text-blue-800 px-3 py-1.5 rounded-full text-sm font-medium shadow"><i class="fas fa-user"></i></a>
        <a href="keranjang.php" class="relative"><i class="fas fa-shopping-cart text-xl"></i>
            <?php if ($keranjang_total > 0): ?>
                <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs px-1.5 rounded-full"><?= $keranjang_total ?></span>
            <?php endif; ?>
        </a>
    </div>
</nav>

<div class="pt-20 pb-6 bg-blue-900">
    <!-- Pencarian Sticky -->
    <div class="bg-blue-900 px-4 py-4 sticky top-[64px] z-40 shadow-md transition-all duration-300 ease-in-out transform"
         x-data="{ visible: true }"
         x-init="let lastScroll = window.pageYOffset;
                window.addEventListener('scroll', () => {
                    const current = window.pageYOffset;
                    visible = current < lastScroll || current < 80;
                    lastScroll = current;
                });"
         :class="{ 'translate-y-0 opacity-100': visible, '-translate-y-full opacity-0': !visible }">
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="cari" placeholder="Cari menu..." value="<?= htmlspecialchars($keyword); ?>"
                   class="w-full px-4 py-2 rounded-full text-sm shadow focus:outline-none">
            <button type="submit" class="text-white text-xl">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<!-- Kategori -->
<div class="px-4 py-6 bg-blue-50 rounded-t-3xl -mt-4">
    <h2 class="text-xl font-bold mb-4">Kategori Menu</h2>
    <div class="flex gap-6 overflow-x-auto pb-1">
        <a href="index.php?kategori=Semua" class="flex flex-col items-center min-w-[64px]">
            <div class="<?= $kategori_terpilih == 'Semua' ? 'bg-blue-100 text-blue-700' : 'bg-white text-blue-800'; ?> w-16 h-16 flex items-center justify-center rounded-full border shadow-sm">
                <i class="fas fa-list text-xl"></i>
            </div>
            <span class="text-sm mt-1 font-medium">Semua</span>
        </a>
        <?php foreach ($daftar_kategori as $kategori => $ikon): ?>
            <a href="index.php?kategori=<?= urlencode($kategori); ?>" class="flex flex-col items-center min-w-[64px]">
                <div class="<?= $kategori_terpilih == $kategori ? 'bg-blue-100 text-blue-700' : 'bg-white text-blue-800'; ?> w-16 h-16 flex items-center justify-center rounded-full border shadow-sm">
                    <i class="fas <?= $ikon ?> text-xl"></i>
                </div>
                <span class="text-sm mt-1 font-medium"><?= htmlspecialchars($kategori) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Menu Recommended -->
<?php if (mysqli_num_rows($recommendedMenus) > 0): ?>
    <div class="px-4 pb-2">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">⭐ Menu Recommended</h3>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-8 gap-4">
            <?php while ($menu = mysqli_fetch_assoc($recommendedMenus)): ?>
                <div x-data="{
                    open: false,
                    jumlah: 1,
                    tambahKeranjang() {
                        fetch('keranjang.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `menu_id=<?= $menu['id']; ?>&jumlah=${this.jumlah}`
                        }).then(res => {
                            if (res.ok) {
                                this.open = false;
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Menu ditambahkan ke keranjang.',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => location.reload());
                            }
                        });
                    }
                }">
                    <div @click="open = true" class="bg-white rounded-2xl shadow hover:shadow-lg transition-all duration-200 cursor-pointer overflow-hidden flex flex-col relative">
                        <!-- Gambar -->
                        <div class="relative">
                            <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" alt="<?= htmlspecialchars($menu['nama_makanan']); ?>"
                                class="w-full aspect-[3/2] object-cover" />
                            <div class="absolute top-0 left-0 bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow bg-opacity-40">
                                ⭐
                            </div>
                        </div>

                        <!-- Konten -->
                        <div class="p-3 flex flex-col justify-between flex-grow">
                            <!-- Judul dan Deskripsi -->
                            <div class="mb-2">
                                <h3 class="font-semibold text-blue-900 text-base line-clamp-1"><?= htmlspecialchars($menu['nama_makanan']); ?></h3>
                                <p class="text-sm text-gray-500 line-clamp-2"><?= htmlspecialchars($menu['deskripsi']); ?></p>
                            </div>

                            <!-- Harga -->
                            <p class="text-blue-700 font-bold text-sm mt-auto">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>
                        </div>
                    </div>


                    <!-- Modal dengan animasi -->
                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-20"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-20"
                        @keydown.escape.window="open = false"
                        @click.outside="open = false"
                        class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-end justify-center">
                        <div class="bg-white w-full max-w-md p-6 rounded-t-3xl pb-20">
                            <div class="flex justify-between items-center mb-3">
                                <h2 class="text-lg font-semibold"><?= htmlspecialchars($menu['nama_makanan']); ?></h2>
                                <button @click="open = false" class="text-gray-500 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
                            </div>
                            <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" class="w-full h-44 object-cover rounded mb-3">
                            <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($menu['deskripsi']); ?></p>
                            <p class="font-bold text-black text-base mb-4">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>
                            <form @submit.prevent="tambahKeranjang" class="flex flex-col gap-3">
                                <label class="text-sm text-gray-700">Jumlah</label>
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" @click="if(jumlah > 1) jumlah--" class="bg-gray-200 w-10 h-10 rounded-full">−</button>
                                    <input type="number" x-model="jumlah" min="1" class="text-xl w-20 text-center border rounded p-2">
                                    <button type="button" @click="jumlah++" class="bg-gray-200 w-10 h-10 rounded-full">+</button>
                                </div>
                                <button type="submit" class="bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800">Tambah ke Keranjang</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Semua Menu (termasuk yang recommended) -->
<?php if (mysqli_num_rows($allMenus) > 0): ?>
    <div class="px-4 pb-20 mt-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">📋 Semua Menu</h3>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-8 gap-4">
            <?php while ($menu = mysqli_fetch_assoc($allMenus)): ?>
                <div x-data="{
                    open: false,
                    jumlah: 1,
                    tambahKeranjang() {
                        fetch('keranjang.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `menu_id=<?= $menu['id']; ?>&jumlah=${this.jumlah}`
                        }).then(res => {
                            if (res.ok) {
                                this.open = false;
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Menu ditambahkan ke keranjang.',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => location.reload());
                            }
                        });
                    }
                }">
                
                <div @click="open = true" class="bg-white rounded-2xl shadow hover:shadow-lg transition-all duration-200 cursor-pointer overflow-hidden flex flex-col relative">
                    <!-- Gambar -->
                    <div class="relative">
                        <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" 
                            class="w-full aspect-[3/2] object-cover" 
                            alt="<?= htmlspecialchars($menu['nama_makanan']); ?>">
                    </div>

                    <!-- Konten -->
                    <div class="p-3 flex flex-col justify-between flex-grow">
                        <!-- Nama & Deskripsi -->
                        <div class="mb-2">
                            <h3 class="font-semibold text-blue-900 text-base line-clamp-1">
                                <?= htmlspecialchars($menu['nama_makanan']); ?>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                <?= htmlspecialchars($menu['deskripsi']); ?>
                            </p>
                        </div>

                        <!-- Harga -->
                        <p class="text-blue-700 font-bold text-sm mt-auto">
                            Rp <?= number_format($menu['harga'], 0, ',', '.'); ?>
                        </p>
                    </div>
                </div>


                    <!-- Modal dengan animasi -->
                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-20"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-20"
                        @keydown.escape.window="open = false"
                        @click.outside="open = false"
                        class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-end justify-center">
                        <div class="bg-white w-full max-w-md p-6 rounded-t-3xl pb-20">
                            <div class="flex justify-between items-center mb-3">
                                <h2 class="text-lg font-semibold"><?= htmlspecialchars($menu['nama_makanan']); ?></h2>
                                <button @click="open = false" class="text-gray-500 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
                            </div>
                            <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" class="w-full h-44 object-cover rounded mb-3">
                            <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($menu['deskripsi']); ?></p>
                            <p class="font-bold text-black text-base mb-4">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></p>
                            <form @submit.prevent="tambahKeranjang" class="flex flex-col gap-3">
                                <label class="text-sm text-gray-700">Jumlah</label>
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" @click="if(jumlah > 1) jumlah--" class="bg-gray-200 w-10 h-10 rounded-full">−</button>
                                    <input type="number" x-model="jumlah" min="1" class="text-xl w-20 text-center border rounded p-2">
                                    <button type="button" @click="jumlah++" class="bg-gray-200 w-10 h-10 rounded-full">+</button>
                                </div>
                                <button type="submit" class="bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800">Tambah ke Keranjang</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php else: ?>
    <div class="text-center text-gray-500 py-10">Menu tidak ditemukan.</div>
<?php endif; ?>


<?php include 'layout/nav-bottom.php'; ?>


</body>
</html>