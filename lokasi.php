<?php
session_start();
include 'config/app.php';

$title = 'Lokasi';
$session_id = session_id();

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
    <title><?= $title ?> | Accomer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-gradient-to-b from-blue-100 to-blue-50 min-h-screen text-gray-800">

    <!-- Judul Halaman -->
    <header class="pt-6 pb-3 text-center">
        <h1 class="text-3xl font-bold text-blue-900 drop-shadow">📍 Lokasi Kami</h1>
        <p class="text-gray-600 text-sm mt-1">Temukan kami & area pengiriman</p>
    </header>

    <!-- Google Maps -->
    <div class="max-w-3xl mx-auto px-4 mt-4">
        <div class="overflow-hidden rounded-2xl shadow-lg border border-blue-200">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.424072763089!2d107.0984913!3d-6.2465511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTQnNDcuNiJTIDEwN8KwMDYnMDMuOCJF!5e0!3m2!1sid!2sid!4v1720130400000!5m2!1sid!2sid"
                width="100%"
                height="350"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>
    </div>

    <!-- Informasi -->
    <div class="max-w-3xl mx-auto mt-6 px-4 space-y-5 mb-32">

        <!-- Alamat -->
        <div class="bg-white p-5 rounded-2xl shadow hover:shadow-md transition">
            <div class="flex items-center mb-3">
                <i class="fas fa-store text-blue-600 mr-3 text-xl"></i>
                <h2 class="text-lg font-semibold text-blue-800">Alamat Toko</h2>
            </div>
            <p class="text-sm text-gray-700 pl-7">
                Kab. Bekasi, Wanajaya, Kec. Cibitung, Kabupaten Bekasi, Jawa Barat 17520<br>
                <strong>Telepon:</strong> <a href="tel:+6281217415421" class="text-blue-600 hover:underline">0812-1741-5421</a>
            </p>
        </div>

        <!-- Jangkauan -->
        <div class="bg-white p-5 rounded-2xl shadow hover:shadow-md transition">
            <div class="flex items-center mb-3">
                <i class="fas fa-truck text-green-600 mr-3 text-xl"></i>
                <h2 class="text-lg font-semibold text-blue-800">Jangkauan Pengiriman</h2>
            </div>
            <ul class="list-disc pl-10 text-sm text-gray-700">
                <li>Wilayah: Cibitung, Tambun, dan sekitarnya</li>
            </ul>
        </div>

        <!-- Jam Operasional -->
        <div class="bg-white p-5 rounded-2xl shadow hover:shadow-md transition">
            <div class="flex items-center mb-3">
                <i class="fas fa-clock text-yellow-600 mr-3 text-xl"></i>
                <h2 class="text-lg font-semibold text-blue-800">Jam Operasional</h2>
            </div>
            <p class="text-sm text-gray-700 pl-7">
                Setiap Hari (Senin – Minggu): 09.00 – 20.00 WIB
            </p>
        </div>

        <!-- Tombol Arahkan -->
        <div class="text-center mt-6">
            <a href="https://www.google.com/maps/dir/?api=1&destination=-6.2465511,107.1010662" target="_blank"
                class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-green-700 transition">
                <i class="fas fa-map-marked-alt mr-2"></i> Arahkan ke Toko
            </a>
        </div>
    </div>

    <?php include 'layout/nav-bottom.php'; ?>

</body>

</html>

