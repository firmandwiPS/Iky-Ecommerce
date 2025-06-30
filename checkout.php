<?php
session_start();
include 'config/app.php';

$session_id = session_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($db, $_POST['nama']);
    $wa = mysqli_real_escape_string($db, $_POST['wa']);

    $result = mysqli_query($db, "
        SELECT k.jumlah, m.nama_makanan, m.harga
        FROM keranjang k
        JOIN makanan m ON k.menu_id = m.id
        WHERE k.session_id = '$session_id'
    ");

    $detail_pesanan = "";
    $total = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $subtotal = $row['harga'] * $row['jumlah'];
        $detail_pesanan .= "{$row['nama_makanan']} x {$row['jumlah']} = Rp " . number_format($subtotal, 0, ',', '.') . "\n";
        $total += $subtotal;
    }

    mysqli_query($db, "INSERT INTO pesanan (nama_pelanggan, no_wa, detail, total) VALUES ('$nama', '$wa', '$detail_pesanan', $total)");
    mysqli_query($db, "DELETE FROM keranjang WHERE session_id = '$session_id'");

    $pesan_wa = urlencode("
        Assalamualaikum Admin 

        Saya ingin melakukan pemesanan makanan dengan detail sebagai berikut:

        *Nama Pelanggan :* $nama
        *Nomor WhatsApp Pelanggan :* $wa

        *Pesanan:*
            $detail_pesanan

        *Total Bayar:* Rp " . number_format($total, 0, ',', '.') . "

        Mohon untuk diproses ya kak 
        Terima kasih banyak!
        ");

    header("Location: https://wa.me/62895703109379?text=$pesan_wa");
    exit;
} else {
    header("Location: keranjang.php");
    exit;
}
?>