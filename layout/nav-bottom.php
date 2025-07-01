<!-- Bottom Nav -->
<nav class="fixed bottom-0 inset-x-0 bg-white bg-opacity-70 backdrop-blur-md border-t shadow-md z-50">
    <div class="flex justify-between items-center text-sm text-gray-500">
        <!-- Home -->
        <a href="index.php" class="flex flex-col items-center justify-center w-full py-2 text-blue-900 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-blue-600 font-semibold' : '' ?>">
            <i class="fas fa-home text-lg mb-1"></i>
            <span class="text-xs">Beranda</span>
        </a>
        <!-- Ulasan -->
        <a href="ulasan.php" class="flex flex-col items-center justify-center w-full py-2 <?= basename($_SERVER['PHP_SELF']) == 'ulasan.php' ? 'text-yellow-500 font-semibold' : 'text-yellow-500' ?>">
            <i class="fas fa-star text-yellow-500 text-lg mb-1"></i>
            <span class="text-xs">Ulasan</span>
        </a>

        <a href="keranjang.php" class="relative -mt-6">
            <div class="w-14 h-14 bg-blue-900 text-white rounded-full flex items-center justify-center shadow-lg  ">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>
            <?php if ($keranjang_total > 0): ?>
                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1.5 rounded-full"><?= $keranjang_total ?></span>
            <?php endif; ?>
        </a>
        <a href="lokasi.php" class="flex flex-col items-center justify-center w-full py-2 <?= basename($_SERVER['PHP_SELF']) == 'lokasi.php' ? 'text-red-600  font-semibold' : 'text-red-600' ?>">
            <i class="fas fa-map-marker-alt text-lg mb-1"></i>
            <span class="text-xs">Lokasi</span>
        </a>
        <a href="https://wa.me/6281234567890" target="_blank" class="flex flex-col items-center justify-center w-full py-2 text-green-600">
            <i class="fab fa-whatsapp text-lg mb-1"></i>
            <span class="text-xs">Chat Owner</span>
        </a>
    </div>
</nav>