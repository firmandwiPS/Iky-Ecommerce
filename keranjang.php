<?php
session_start();
include 'config/app.php';

$title = 'Keranjang Belanja';
$session_id = session_id();

// Tambah item ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {
    $menu_id = (int) $_POST['menu_id'];
    $jumlah = max(1, (int) $_POST['jumlah']);

    $cek = mysqli_query($db, "SELECT * FROM keranjang WHERE session_id = '$session_id' AND menu_id = $menu_id");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($db, "UPDATE keranjang SET jumlah = jumlah + $jumlah WHERE session_id = '$session_id' AND menu_id = $menu_id");
    } else {
        mysqli_query($db, "INSERT INTO keranjang (session_id, menu_id, jumlah) VALUES ('$session_id', $menu_id, $jumlah)");
    }

    $_SESSION['pesan_sukses'] = 'Pesanan berhasil ditambahkan ke keranjang.';
    $_SESSION['sweetalert'] = 'tambah';
    header("Location: keranjang.php");
    exit;
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $hapus_id = (int) $_GET['hapus'];
    mysqli_query($db, "DELETE FROM keranjang WHERE id = $hapus_id AND session_id = '$session_id'");
    $_SESSION['pesan_sukses'] = 'Item berhasil dihapus dari keranjang.';
    $_SESSION['sweetalert'] = 'hapus';
    header("Location: keranjang.php");
    exit;
}

// Ambil isi keranjang
$menus = [];
$total_harga = 0;

$result = mysqli_query($db, "
    SELECT k.id AS keranjang_id, m.*, k.jumlah, (m.harga * k.jumlah) AS total 
    FROM keranjang k 
    JOIN makanan m ON k.menu_id = m.id 
    WHERE k.session_id = '$session_id'
");

while ($row = mysqli_fetch_assoc($result)) {
    $menus[] = $row;
    $total_harga += $row['total'];
}
$keranjang_total = 0; // Tambahkan ini sebelum loop

while ($row = mysqli_fetch_assoc($result)) {
    $menus[] = $row;
    $total_harga += $row['total'];
    $keranjang_total += $row['jumlah']; // Hitung total item
}


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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-blue-50 text-gray-800" x-data="{ showCheckout: false }">

<!-- SweetAlert2 -->
<?php if (isset($_SESSION['sweetalert'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($_SESSION['sweetalert'] === 'hapus'): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dihapus!',
                    text: 'Item telah dihapus dari keranjang.',
                    timer: 2000,
                    showConfirmButton: false
                });
            <?php elseif ($_SESSION['sweetalert'] === 'tambah'): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Item ditambahkan ke keranjang.',
                    timer: 2000,
                    showConfirmButton: false
                });
            <?php elseif ($_SESSION['sweetalert'] === 'checkout'): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Pesanan Diproses!',
                    text: 'Pesanan kamu berhasil diproses.',
                    timer: 2500,
                    showConfirmButton: false
                });
            <?php endif; ?>
        });
    </script>
    <?php 
    unset($_SESSION['sweetalert']); 
    unset($_SESSION['pesan_sukses']);
    ?>
<?php endif; ?>

<!-- Navbar -->
<nav class="bg-blue-900 p-4 shadow-md sticky top-0 z-50">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <a href="index.php" class="flex items-center text-white font-semibold no-underline hover:no-underline">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <div class="flex items-center text-white gap-2 text-lg font-semibold">
            <i class="fas fa-shopping-cart text-xl text-white"></i>
            <span>Keranjang</span>
        </div>
    </div>
</nav>


<div class="max-w-4xl mx-auto px-4 py-6 bg-blue-50 ">
    <?php if (empty($menus)): ?>
        <div class="text-center text-gray-500 mt-20">
            <i class="fas fa-shopping-cart text-5xl mb-4"></i>
            <p>Keranjang kamu masih kosong.</p>
            <a href="index.php" class="inline-block mt-6 bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                Lihat Menu
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <?php foreach ($menus as $menu): ?>
                <div class="flex items-center gap-4 p-4 border-b" x-data="{ showModal: false }">
                    <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" class="w-20 h-20 object-cover rounded-lg">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($menu['nama_makanan']); ?></h3>
                        <p class="text-sm text-gray-600">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?> x <?= $menu['jumlah']; ?></p>
                        <p class="text-sm font-semibold text-gray-700 mt-1">Subtotal: Rp <?= number_format($menu['total'], 0, ',', '.'); ?></p>
                    </div>
                    <button @click="showModal = true" class="text-red-500 hover:text-red-700" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>

                    <!-- Modal Hapus -->
                    <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50" style="display: none;">
                        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full text-center">
                            <h2 class="text-lg font-semibold mb-4">Hapus Item?</h2>
                            <p class="text-sm text-gray-600 mb-4">
                                Yakin ingin menghapus <strong><?= htmlspecialchars($menu['nama_makanan']); ?></strong> dari keranjang?
                            </p>
                            <div class="flex justify-center gap-4">
                                <button @click="showModal = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">Batal</button>
                                <a href="?hapus=<?= $menu['keranjang_id']; ?>" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Hapus</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-right mt-6">
            <p class="text-lg font-bold">Total: Rp <?= number_format($total_harga, 0, ',', '.'); ?></p>
            <button @click="showCheckout = true" class="mt-4 inline-block bg-blue-900 text-white px-6 py-2 rounded transition">
                Checkout Sekarang
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Checkout -->
<div 
    x-show="showCheckout"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-y-full opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-full opacity-0"
    class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-end justify-center"
    style="display: none;"
>
    <form method="POST" action="checkout.php" class="bg-white rounded-t-2xl shadow-lg p-6 w-full max-w-md pb-20">
        <div class="text-center">
            <div class="w-12 h-1 bg-gray-400 rounded mx-auto mb-4"></div>
            <h2 class="text-lg font-semibold mb-4">Konfirmasi Pesanan</h2>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="nama" required class="mt-1 w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
            <input type="text" name="wa" required placeholder="08xxxx" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex justify-center gap-4 mt-6">
            <button type="button" @click="showCheckout = false" class="px-4 py-2 bg-red-600 text-white rounded transition">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded transition">
                Proses Pesanan
            </button>
        </div>
    </form>
</div>



<?php include 'layout/nav-bottom.php'; ?>

</body>
</html>
