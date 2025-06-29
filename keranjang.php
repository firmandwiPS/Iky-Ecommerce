<?php
session_start();
include 'config/app.php';

$title = 'Keranjang Belanja';

// Tambah ke keranjang jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = (int) $_POST['menu_id'];
    $jumlah = (int) $_POST['jumlah'];

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    if (isset($_SESSION['keranjang'][$menu_id])) {
        $_SESSION['keranjang'][$menu_id] += $jumlah;
    } else {
        $_SESSION['keranjang'][$menu_id] = $jumlah;
    }

    $_SESSION['pesan_sukses'] = '✅ Pesanan berhasil ditambahkan ke keranjang. Terima kasih!';
    header("Location: keranjang.php");
    exit;
}

// Ambil keranjang
$keranjang = $_SESSION['keranjang'] ?? [];
$menus = [];
$total_harga = 0;

if (!empty($keranjang)) {
    $menu_ids = implode(',', array_map('intval', array_keys($keranjang)));
    $result = mysqli_query($db, "SELECT * FROM makanan WHERE id IN ($menu_ids)");
    while ($row = mysqli_fetch_assoc($result)) {
        $row['jumlah'] = $keranjang[$row['id']];
        $row['total'] = $row['jumlah'] * $row['harga'];
        $menus[] = $row;
        $total_harga += $row['total'];
    }
}

// Hapus item
if (isset($_GET['hapus'])) {
    $hapus_id = (int)$_GET['hapus'];
    unset($_SESSION['keranjang'][$hapus_id]);
    header("Location: keranjang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Navbar -->
<nav class="p-4 bg-white shadow flex justify-between items-center sticky top-0 z-10">
    <a href="index.php" class="text-lg font-bold text-blue-600">← Kembali</a>
    <div class="text-lg font-semibold">Keranjang</div>
</nav>

<!-- Konten -->
<div class="max-w-4xl mx-auto p-4">
    <?php if (isset($_SESSION['pesan_sukses'])): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg shadow">
            <?= $_SESSION['pesan_sukses']; ?>
        </div>
        <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>

    <?php if (empty($menus)): ?>
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-shopping-cart text-4xl mb-4"></i>
            <p>Keranjang belanja kamu kosong.</p>
            <a href="index.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Lihat Menu</a>
        </div>
    <?php else: ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <?php foreach ($menus as $menu): ?>
                <div class="flex items-center border-b p-4">
                    <img src="gambar/<?= htmlspecialchars($menu['gambar']); ?>" class="w-20 h-20 object-cover rounded-lg mr-4">
                    <div class="flex-1">
                        <h3 class="font-semibold"><?= htmlspecialchars($menu['nama_makanan']); ?></h3>
                        <p class="text-sm text-gray-500">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?> x <?= $menu['jumlah']; ?></p>
                        <p class="text-sm text-gray-700 font-semibold mt-1">Subtotal: Rp <?= number_format($menu['total'], 0, ',', '.'); ?></p>
                    </div>
                    <a href="?hapus=<?= $menu['id']; ?>" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-6 text-right">
            <p class="text-lg font-semibold">Total: Rp <?= number_format($total_harga, 0, ',', '.'); ?></p>
            <a href="checkout.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
                Checkout
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
