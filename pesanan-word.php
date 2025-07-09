<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

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

// Ambil semua data pesanan diurutkan dari yang terbaru
$data_pesanan = select("SELECT * FROM pesanan ORDER BY created_at DESC");

// Set headers for Word download
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=laporan_pesanan_".date('Y-m-d').".doc");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="ProgId" content="Word.Document">
    <meta name="Generator" content="Microsoft Word 15">
    <title>Laporan Data Pesanan</title>
    <style>
        body {
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 11pt;
            margin: 1cm;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 16pt;
            border-bottom: 1px solid #3498db;
            padding-bottom: 8pt;
        }
        .header h1 {
            color: #2c3e50;
            margin-bottom: 4pt;
            font-size: 16pt;
        }
        .header h3 {
            color: #7f8c8d;
            margin-top: 0;
            font-size: 12pt;
            font-weight: normal;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12pt;
            font-size: 10pt;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            padding: 6pt;
            text-align: left;
            border: 1px solid #2c3e50;
        }
        td {
            padding: 5pt;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 16pt;
            color: #7f8c8d;
            font-style: italic;
            font-size: 9pt;
            border-top: 1px solid #eee;
            padding-top: 8pt;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9pt;
            font-weight: bold;
        }
        .status-diproses {
            background-color: #f39c12;
            color: white;
        }
        .status-dikirim {
            background-color: #3498db;
            color: white;
        }
        .status-selesai {
            background-color: #2ecc71;
            color: white;
        }
        .status-dibatalkan {
            background-color: #e74c3c;
            color: white;
        }
        .wrap-text {
            word-wrap: break-word;
            max-width: 200px;
        }
        .grand-total {
            font-weight: bold;
            background-color: #f8f9fa;
        }
    </style>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA PESANAN</h1>
        <h3>Periode: <?= date('d F Y'); ?></h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="8%">ID Pesanan</th>
                <th width="15%">Nama Pelanggan</th>
                <th width="12%">No. WhatsApp</th>
                <th width="30%">Detail Pesanan</th>
                <th width="10%" class="text-right">Total</th>
                <th width="10%">Status</th>
                <th width="10%">Tanggal Pesanan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $grand_total = 0;
            foreach ($data_pesanan as $pesanan): 
                $grand_total += $pesanan['total'];
                $status_class = 'status-' . strtolower(str_replace(' ', '', $pesanan['status']));
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $pesanan['id']; ?></td>
                <td><?= htmlspecialchars($pesanan['nama_pelanggan']); ?></td>
                <td><?= htmlspecialchars($pesanan['no_wa']); ?></td>
                <td class="wrap-text"><?= nl2br(htmlspecialchars($pesanan['detail'])); ?></td>
                <td class="text-right">Rp<?= number_format($pesanan['total'], 0, ',', '.'); ?></td>
                <td class="text-center">
                    <span class="status-badge <?= $status_class; ?>">
                        <?= $pesanan['status']; ?>
                    </span>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($pesanan['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            <!-- Grand Total Row -->
            <tr class="grand-total">
                <td colspan="5" class="text-right"><strong>TOTAL KESELURUHAN:</strong></td>
                <td class="text-right">Rp<?= number_format($grand_total, 0, ',', '.'); ?></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        Dicetak pada <?= date('d/m/Y H:i:s'); ?> oleh <?= $_SESSION['username'] ?? 'System'; ?>
    </div>
</body>
</html>