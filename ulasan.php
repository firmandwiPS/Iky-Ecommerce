<?php
include 'config/app.php';


$title = 'Ulasan MAkanan';
$session_id = session_id();
// Ambil ulasan dan makanan
$query_ulasan = mysqli_query($db, "
    SELECT u.*, m.nama_makanan 
    FROM ulasan u 
    JOIN makanan m ON u.makanan_id = m.id 
    ORDER BY u.tanggal DESC
");

$query_makanan = mysqli_query($db, "SELECT id, nama_makanan FROM makanan ORDER BY nama_makanan ASC");

// Proses kirim ulasan
if (isset($_POST['kirim'])) {
    $makanan_id = (int) $_POST['makanan_id'];
    $nama = mysqli_real_escape_string($db, $_POST['nama_pengulas']);
    $ulasan = mysqli_real_escape_string($db, $_POST['ulasan']);
    $kritik = mysqli_real_escape_string($db, $_POST['kritik'] ?? '');
    $saran = mysqli_real_escape_string($db, $_POST['saran'] ?? '');
    $rating = (int) $_POST['rating'];

    mysqli_query($db, "
        INSERT INTO ulasan (makanan_id, nama_pengulas, ulasan, kritik, saran, rating, tanggal)
        VALUES ($makanan_id, '$nama', '$ulasan', '$kritik', '$saran', $rating, NOW())
    ");

    // Redirect dengan query untuk trigger SweetAlert
    echo "<script>location.href='ulasan.php?success=true';</script>";
    exit;
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-blue-50 text-gray-800 min-h-screen" x-data="{ showModal: false }">

    <!-- Navbar Dinamis -->
    <nav x-data="{ atTop: true }"
        x-init="atTop = window.scrollY <= 0; window.addEventListener('scroll', () => { atTop = window.scrollY <= 0; });"
        :class="atTop ? 'bg-white shadow-none' : 'bg-white/90 shadow backdrop-blur-md'"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out">
        <div class="max-w-5xl mx-auto flex justify-between items-center px-4 py-3 text-blue-900">
            <!-- Logo + Title -->
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 text-blue-600 p-2 rounded-full">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold">Forum Ulasan Makanan</h1>
                    <p class="text-xs text-gray-500">Diskusi & pendapat dari para pengunjung</p>
                </div>
            </div>

            <!-- Right Buttons -->
            <div class="flex items-center gap-3">
                <!-- Tombol Tulis -->
                <button @click="showModal = true"
                    class="flex items-center gap-1 bg-blue-900 text-white px-3 py-1.5 rounded-lg  transition text-sm">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tulis</span>
                </button>
            </div>
        </div>
    </nav>


    <div class="pt-20 max-w-2xl mx-auto px-4 py-8">
        <?php if (mysqli_num_rows($query_ulasan) === 0): ?>
            <div class="bg-white p-6 rounded shadow text-center text-gray-600">
                Belum ada ulasan makanan saat ini.
            </div>
        <?php else: ?>
            <div class="flex flex-col gap-4">
                <?php
                $i = 0;
                while ($ulasan = mysqli_fetch_assoc($query_ulasan)):
                    $isRight = $i % 2 === 1;
                ?>
                    <div class="flex <?= $isRight ? 'justify-end' : 'justify-start' ?>">
                        <div class="flex items-start gap-3 max-w-[90%] <?= $isRight ? 'flex-row-reverse' : '' ?>">
                            <div class="w-10 h-10 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-sm shrink-0">
                                <?= strtoupper(substr($ulasan['nama_pengulas'], 0, 1)) ?>
                            </div>
                            <div class="bg-white px-4 py-3 rounded-2xl shadow-md <?= $isRight ? 'rounded-tr-none' : 'rounded-tl-none' ?>">
                                <div class="text-sm font-semibold text-blue-900 mb-1">
                                    <?= htmlspecialchars($ulasan['nama_pengulas']) ?>
                                    <span class="text-gray-400 text-xs"> • <?= date('d M Y, H:i', strtotime($ulasan['tanggal'])) ?></span>
                                </div>
                                <p class="text-sm"><?= nl2br(htmlspecialchars($ulasan['ulasan'])) ?></p>
                                <?php if (!empty($ulasan['kritik'])): ?>
                                    <p class="text-xs text-red-600 mt-2"><strong>Kritik:</strong> <?= nl2br(htmlspecialchars($ulasan['kritik'])) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($ulasan['saran'])): ?>
                                    <p class="text-xs text-green-600"><strong>Saran:</strong> <?= nl2br(htmlspecialchars($ulasan['saran'])) ?></p>
                                <?php endif; ?>
                                <div class="flex gap-1 mt-1">
                                    <?php for ($j = 1; $j <= 5; $j++): ?>
                                        <svg class="w-4 h-4 <?= $j <= $ulasan['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.975a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.388 2.463a1 1 0 00-.364 1.118l1.286 3.975c.3.921-.755 1.688-1.54 1.118l-3.388-2.463a1 1 0 00-1.176 0l-3.388 2.463c-.784.57-1.838-.197-1.539-1.118l1.286-3.975a1 1 0 00-.364-1.118L2.05 9.402c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.975z" />
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $i++;
                endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Tulis Ulasan -->
    <div
        x-show="showModal"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-end justify-center"
        style="display: none;">
        <div class="bg-white w-full sm:w-[500px] p-6 rounded-t-2xl shadow-lg pb-20 overflow-y-auto max-h-screen" @click.away="showModal = false">
            <h3 class="text-lg font-semibold mb-4">Tulis Ulasan Anda</h3>
            <form method="POST" class="space-y-3">
                <select name="makanan_id" required class="w-full border px-3 py-2 rounded focus:ring focus:ring-blue-300">
                    <option value="">Pilih Makanan</option>
                    <?php while ($makanan = mysqli_fetch_assoc($query_makanan)): ?>
                        <option value="<?= $makanan['id'] ?>"><?= htmlspecialchars($makanan['nama_makanan']) ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="nama_pengulas" placeholder="Nama Anda" required autocomplete="name"
                    class="w-full border px-3 py-2 rounded focus:ring focus:ring-blue-300">
                <textarea name="ulasan" placeholder="Ulasan Anda..." required rows="4"
                    class="w-full border px-3 py-2 rounded resize-none focus:ring focus:ring-blue-300"></textarea>
                <textarea name="kritik" placeholder="Kritik (opsional)" rows="2"
                    class="w-full border px-3 py-2 rounded resize-none focus:ring focus:ring-blue-300"></textarea>
                <textarea name="saran" placeholder="Saran (opsional)" rows="2"
                    class="w-full border px-3 py-2 rounded resize-none focus:ring focus:ring-blue-300"></textarea>
                <select name="rating" required class="w-full border px-3 py-2 rounded focus:ring focus:ring-blue-300">
                    <option value="">Pilih Rating</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> Bintang</option>
                    <?php endfor; ?>
                </select>
                <div class="flex justify-center gap-4 mt-6">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Batal</button>
                    <button type="submit" name="kirim" class="px-4 py-2 bg-blue-900 text-white rounded transition">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'layout/nav-bottom.php'; ?>

    <!-- SweetAlert2 success trigger -->
    <script>
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Terima kasih!',
                text: 'Ulasan Anda berhasil dikirim.',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                window.history.replaceState(null, null, 'ulasan.php');
            });
        <?php endif; ?>
    </script>
</body>

</html>