<?php
session_start();
include 'config/app.php';

$session_id = session_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($db, $_POST['nama']);
    $wa = mysqli_real_escape_string($db, $_POST['wa']);
    $status = 'Sedang diproses'; // Default status

    // Get cart items
    $result = mysqli_query($db, "
        SELECT k.jumlah, m.nama_makanan, m.harga
        FROM keranjang k
        JOIN makanan m ON k.menu_id = m.id
        WHERE k.session_id = '$session_id'
    ");

    // Prepare order details in the same format as data-pesanan.php
    $detail_items = [];
    $total = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $subtotal = $row['harga'] * $row['jumlah'];
        $detail_items[] = "{$row['nama_makanan']} x{$row['jumlah']}";
        $total += $subtotal;
    }

    // Format detail string like in data-pesanan.php
    $detail_pesanan = implode(", ", $detail_items);

    // Insert into pesanan table with same structure as data-pesanan.php
    $query = "INSERT INTO pesanan (nama_pelanggan, no_wa, detail, total, status, created_at)
              VALUES ('$nama', '$wa', '$detail_pesanan', $total, '$status', NOW())";

    if (mysqli_query($db, $query)) {
        // Clear cart
        mysqli_query($db, "DELETE FROM keranjang WHERE session_id = '$session_id'");

        // Prepare WhatsApp message with formatted details
        $formatted_details = str_replace(", ", "\n", $detail_pesanan);
        $pesan_wa = urlencode("
Assalamualaikum Admin 

Saya ingin melakukan pemesanan makanan dengan detail sebagai berikut:

*Nama Pelanggan :* $nama
*Nomor WhatsApp Pelanggan :* $wa

*Pesanan:*
$formatted_details

*Total Bayar:* Rp " . number_format($total, 0, ',', '.') . "

Mohon untuk diproses ya kak 
Terima kasih banyak!
        ");

        header("Location: https://wa.me/6281217415421?text=$pesan_wa");
        exit;
    } else {
        // Error handling
        header("Location: keranjang.php?error=1");
        exit;
    }
} else {
    header("Location: keranjang.php");
    exit;
}
