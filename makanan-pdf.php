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

function select($sql) {
    global $db;
    $result = mysqli_query($db, $sql);
    if (!$result) {
        die("Query error: " . mysqli_error($db));
    }
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Buat folder temporary jika belum ada
if (!file_exists(__DIR__ . '/tmp')) {
    mkdir(__DIR__ . '/tmp', 0777, true);
}

// Ambil data makanan
$data_makanan = select("SELECT * FROM makanan");

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

// Path ke folder gambar (relative dari file ini)
$gambar_path = __DIR__ . '/gambar/';

// HTML Header
$mpdf->WriteHTML('
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #3498db; color: white; padding: 8px; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .text-center { text-align: center; }
        .product-img { max-width: 40px; max-height: 40px; }
    </style>
</head>
<body>
    <h1 style="text-align:center">Data Makanan</h1>
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Recommended</th>
            </tr>
        </thead>
        <tbody>
');

// Proses data per baris
$no = 1;
foreach ($data_makanan as $makanan) {
    $image_path = $gambar_path . $makanan['gambar'];
    
    // Cek apakah file gambar ada
    if (!empty($makanan['gambar']) && file_exists($image_path)) {
        // Gunakan tag image dengan path langsung
        $image_html = '<img src="' . $image_path . '" class="product-img">';
    } else {
        $image_html = '-';
    }
    
    $html = '
    <tr>
        <td class="text-center">'.$no++.'</td>
        <td class="text-center">'.$image_html.'</td>
        <td>'.htmlspecialchars($makanan['nama_makanan']).'</td>
        <td>Rp'.number_format($makanan['harga'], 0, ',', '.').'</td>
        <td>'.htmlspecialchars($makanan['kategori']).'</td>
        <td class="text-center">'.$makanan['stok'].'</td>
        <td class="text-center">'.$makanan['recommended'].'</td>
    </tr>';
    
    $mpdf->WriteHTML($html);
}

// HTML Footer
$mpdf->WriteHTML('
        </tbody>
    </table>
    <p style="text-align:center;font-size:9pt">
        Dicetak pada '.date('d/m/Y H:i:s').'
    </p>
</body>
</html>
');

// Output PDF
$mpdf->Output('data_makanan_'.date('Ymd_His').'.pdf', 'D');