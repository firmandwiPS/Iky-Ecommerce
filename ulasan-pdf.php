<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

// Increase PHP limits for large data
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

// Create temp directory if not exists
if (!file_exists(__DIR__ . '/tmp')) {
    mkdir(__DIR__ . '/tmp', 0777, true);
}

// Get review data with food information
$data_ulasan = select("SELECT u.*, m.nama_makanan 
                      FROM ulasan u 
                      JOIN makanan m ON u.makanan_id = m.id 
                      ORDER BY u.tanggal DESC");

require_once 'vendor/autoload.php';

// mPDF configuration
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'orientation' => 'L', // Landscape to fit more columns
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
        .rating {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
            background-color: #f39c12;
            color: white;
        }
        .wrap-text { word-wrap: break-word; max-width: 150px; }
        .footer { text-align: right; font-size: 8pt; margin-top: 20px; }
    </style>
</head>
<body>
    <h1 style="text-align:center;margin-bottom:5px">Laporan Data Ulasan Pelanggan</h1>
    <p style="text-align:center;margin-top:0;font-size:10pt">'.date('d F Y').'</p>
    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="10%">ID Ulasan</th>
                <th width="15%">Makanan</th>
                <th width="15%">Nama Pengulas</th>
                <th width="20%">Ulasan</th>
                <th width="15%">Kritik</th>
                <th width="15%">Saran</th>
                <th width="5%" class="text-center">Rating</th>
                <th width="10%">Tanggal Ulasan</th>
            </tr>
        </thead>
        <tbody>
');

// Process data row by row
$no = 1;
$total_ulasan = 0;
$total_rating = 0;

foreach ($data_ulasan as $ulasan) {
    // Format rating with stars
    $rating_html = '<span class="rating">'.$ulasan['rating'].'/5</span>';
    
    // Format review text with line breaks
    $ulasan_text = nl2br(htmlspecialchars($ulasan['ulasan']));
    $kritik_text = $ulasan['kritik'] ? nl2br(htmlspecialchars($ulasan['kritik'])) : '-';
    $saran_text = $ulasan['saran'] ? nl2br(htmlspecialchars($ulasan['saran'])) : '-';
    
    // Format date
    $tanggal_ulasan = date('d/m/Y H:i', strtotime($ulasan['tanggal']));
    
    $html = '
    <tr>
        <td class="text-center">'.$no++.'</td>
        <td>'.$ulasan['id'].'</td>
        <td>'.htmlspecialchars($ulasan['nama_makanan']).'</td>
        <td>'.htmlspecialchars($ulasan['nama_pengulas']).'</td>
        <td class="wrap-text">'.$ulasan_text.'</td>
        <td class="wrap-text">'.$kritik_text.'</td>
        <td class="wrap-text">'.$saran_text.'</td>
        <td class="text-center">'.$rating_html.'</td>
        <td>'.$tanggal_ulasan.'</td>
    </tr>';
    
    $mpdf->WriteHTML($html);
    $total_ulasan++;
    $total_rating += $ulasan['rating'];
}

// Calculate average rating
$average_rating = $total_ulasan > 0 ? round($total_rating / $total_ulasan, 1) : 0;

// HTML Footer with summary
$mpdf->WriteHTML('
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align:right;font-weight:bold;padding-top:10px">
                    Total Ulasan: '.$total_ulasan.' | Rata-rata Rating:
                </td>
                <td style="text-align:center;font-weight:bold;padding-top:10px">
                    <span class="rating">'.$average_rating.'/5</span>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <div class="footer">
        Dicetak pada '.date('d/m/Y H:i:s').' oleh '.htmlspecialchars($_SESSION['username'] ?? 'System').'
    </div>
</body>
</html>
');

// Output PDF
$mpdf->Output('laporan_ulasan_'.date('Ymd_His').'.pdf', 'D');