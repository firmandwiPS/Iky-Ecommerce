<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

// Tingkatkan limit PHP untuk menangani data besar
ini_set('pcre.backtrack_limit', '10000000');
ini_set('pcre.recursion_limit', '10000000');
ini_set('memory_limit', '512M');

include 'config/database.php';
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Buat folder temporary jika belum ada
if (!file_exists(__DIR__ . '/tmp')) {
    mkdir(__DIR__ . '/tmp', 0777, true);
}

// Ambil data uang masuk
$query_masuk = mysqli_query($db, "SELECT tanggal, jumlah, keterangan FROM data_uang_masuk ORDER BY tanggal DESC");
$total_masuk = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_masuk"))['total'] ?? 0;

// Ambil data uang keluar
$query_keluar = mysqli_query($db, "SELECT tanggal, jumlah, keterangan FROM data_uang_keluar ORDER BY tanggal DESC");
$total_keluar = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_keluar"))['total'] ?? 0;

$saldo = $total_masuk - $total_keluar;

require_once 'vendor/autoload.php';

// Konfigurasi mPDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'orientation' => 'P',
    'tempDir' => __DIR__ . '/tmp',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 15,
    'margin_bottom' => 15
]);

// HTML Header
$mpdf->WriteHTML('
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #3498db; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 1.1em; }
        .masuk { color: #28a745; }
        .keluar { color: #dc3545; }
        .saldo { font-weight: bold; color: #007bff; font-size: 1.2em; }
        .footer { text-align: center; margin-top: 20px; color: #6c757d; font-style: italic; font-size: 9pt; }
        .section-title { margin-top: 30px; color: #2c3e50; font-size: 12pt; }
    </style>
</head>
<body>
    <h1 style="text-align:center">Laporan Keuangan</h1>
    <p style="text-align:center">Periode: '.date('d/m/Y').'</p>
');

// Tabel Uang Masuk
$mpdf->WriteHTML('
    <h3 class="section-title">Uang Masuk</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
');

// Proses data uang masuk
$no = 1;
mysqli_data_seek($query_masuk, 0);
while ($row = mysqli_fetch_assoc($query_masuk)) {
    $mpdf->WriteHTML('
    <tr>
        <td>'.$no++.'</td>
        <td>'.date('d/m/Y', strtotime($row['tanggal'])).'</td>
        <td>'.htmlspecialchars($row['keterangan']).'</td>
        <td class="text-right">Rp'.number_format($row['jumlah'], 0, ',', '.').'</td>
    </tr>
    ');
}

$mpdf->WriteHTML('
            <tr>
                <td colspan="3" style="text-align:right;font-weight:bold">Total Uang Masuk</td>
                <td class="text-right" style="font-weight:bold">Rp'.number_format($total_masuk, 0, ',', '.').'</td>
            </tr>
        </tbody>
    </table>
');

// Tabel Uang Keluar
$mpdf->WriteHTML('
    <h3 class="section-title">Uang Keluar</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
');

// Proses data uang keluar
$no = 1;
mysqli_data_seek($query_keluar, 0);
while ($row = mysqli_fetch_assoc($query_keluar)) {
    $mpdf->WriteHTML('
    <tr>
        <td>'.$no++.'</td>
        <td>'.date('d/m/Y', strtotime($row['tanggal'])).'</td>
        <td>'.htmlspecialchars($row['keterangan']).'</td>
        <td class="text-right">Rp'.number_format($row['jumlah'], 0, ',', '.').'</td>
    </tr>
    ');
}

$mpdf->WriteHTML('
            <tr>
                <td colspan="3" style="text-align:right;font-weight:bold">Total Uang Keluar</td>
                <td class="text-right" style="font-weight:bold">Rp'.number_format($total_keluar, 0, ',', '.').'</td>
            </tr>
        </tbody>
    </table>
');

// Ringkasan
$mpdf->WriteHTML('
    <div class="summary">
        <div class="summary-item masuk">
            <span>Total Pemasukan:</span>
            <span>Rp'.number_format($total_masuk, 0, ',', '.').'</span>
        </div>
        <div class="summary-item keluar">
            <span>Total Pengeluaran:</span>
            <span>Rp'.number_format($total_keluar, 0, ',', '.').'</span>
        </div>
        <div class="summary-item saldo">
            <span>Saldo Akhir:</span>
            <span>Rp'.number_format($saldo, 0, ',', '.').'</span>
        </div>
    </div>
');

// Footer
$mpdf->WriteHTML('
    <div class="footer">
        Dicetak pada '.date('d/m/Y H:i:s').' oleh '.$_SESSION['username'].'
    </div>
</body>
</html>
');

// Output PDF
$mpdf->Output('laporan_keuangan_'.date('Ymd_His').'.pdf', 'D');