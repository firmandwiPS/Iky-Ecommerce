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

// Ambil data pesanan dengan sorting terbaru
$data_pesanan = select("SELECT * FROM pesanan ORDER BY created_at DESC");

require_once 'vendor/autoload.php';

// Konfigurasi mPDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'orientation' => 'L', // Landscape untuk menampung lebih banyak kolom
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
        body { font-family: Arial; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #3498db; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; vertical-align: top; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
        }
        .status-diproses { background-color: #f39c12; color: white; }
        .status-dikirim { background-color: #3498db; color: white; }
        .status-selesai { background-color: #2ecc71; color: white; }
        .status-dibatalkan { background-color: #e74c3c; color: white; }
        .wrap-text { word-wrap: break-word; max-width: 200px; }
    </style>
</head>
<body>
    <h1 style="text-align:center;margin-bottom:5px">Laporan Data Pesanan</h1>
    <p style="text-align:center;margin-top:0;font-size:10pt">'.date('d F Y').'</p>
    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="10%">ID Pesanan</th>
                <th width="15%">Nama Pelanggan</th>
                <th width="12%">No. WhatsApp</th>
                <th width="25%">Detail Pesanan</th>
                <th width="10%" class="text-right">Total</th>
                <th width="10%">Status</th>
                <th width="13%">Tanggal Pesanan</th>
            </tr>
        </thead>
        <tbody>
');

// Proses data per baris
$no = 1;
$grand_total = 0;
foreach ($data_pesanan as $pesanan) {
    // Format status dengan class yang sesuai
    $status_class = 'status-' . strtolower(str_replace(' ', '', $pesanan['status']));
    $status_html = '<span class="status ' . $status_class . '">' . $pesanan['status'] . '</span>';
    
    // Format detail pesanan dengan line breaks
    $detail_pesanan = nl2br(htmlspecialchars($pesanan['detail']));
    
    // Format tanggal
    $tanggal_pesanan = date('d/m/Y H:i', strtotime($pesanan['created_at']));
    
    // Format total harga
    $total_harga = 'Rp' . number_format($pesanan['total'], 0, ',', '.');
    
    $html = '
    <tr>
        <td class="text-center">'.$no++.'</td>
        <td>'.$pesanan['id'].'</td>
        <td>'.htmlspecialchars($pesanan['nama_pelanggan']).'</td>
        <td>'.htmlspecialchars($pesanan['no_wa']).'</td>
        <td class="wrap-text">'.$detail_pesanan.'</td>
        <td class="text-right">'.$total_harga.'</td>
        <td>'.$status_html.'</td>
        <td>'.$tanggal_pesanan.'</td>
    </tr>';
    
    $mpdf->WriteHTML($html);
    $grand_total += $pesanan['total'];
}

// HTML Footer dengan grand total
$mpdf->WriteHTML('
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right;font-weight:bold;padding-top:10px">Total Seluruh Pesanan:</td>
                <td style="text-align:right;font-weight:bold;padding-top:10px">Rp'.number_format($grand_total, 0, ',', '.').'</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    <p style="text-align:right;font-size:8pt;margin-top:20px">
        Dicetak pada '.date('d/m/Y H:i:s').' oleh '.htmlspecialchars($_SESSION['username'] ?? 'System').'
    </p>
</body>
</html>
');

// Output PDF
$mpdf->Output('laporan_pesanan_'.date('Ymd_His').'.pdf', 'D');