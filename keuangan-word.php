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

// Get income data
$query_masuk = mysqli_query($db, "SELECT tanggal, jumlah, keterangan FROM data_uang_masuk ORDER BY tanggal DESC");
$total_masuk = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_masuk"))['total'] ?? 0;

// Get expense data
$query_keluar = mysqli_query($db, "SELECT tanggal, jumlah, keterangan FROM data_uang_keluar ORDER BY tanggal DESC");
$total_keluar = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_keluar"))['total'] ?? 0;

$saldo = $total_masuk - $total_keluar;

// Set headers for Word download
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=laporan_keuangan_".date('Y-m-d').".doc");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="ProgId" content="Word.Document">
    <meta name="Generator" content="Microsoft Word 15">
    <title>Laporan Keuangan</title>
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
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 20pt;
            padding: 12pt;
            background-color: #f8f9fa;
            border-radius: 4pt;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6pt;
            font-size: 11pt;
        }
        .masuk {
            color: #28a745;
        }
        .keluar {
            color: #dc3545;
        }
        .saldo {
            font-weight: bold;
            color: #007bff;
            font-size: 12pt;
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
        .section-title {
            margin-top: 24pt;
            color: #2c3e50;
            font-size: 12pt;
            font-weight: bold;
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
        <h1>LAPORAN KEUANGAN</h1>
        <h3>Periode: <?= date('d/m/Y'); ?></h3>
    </div>
    
    <!-- Income Table -->
    <h3 class="section-title">UANG MASUK</h3>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">Tanggal</th>
                <th width="55%">Keterangan</th>
                <th width="25%" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            mysqli_data_seek($query_masuk, 0);
            while ($row = mysqli_fetch_assoc($query_masuk)): 
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                <td class="text-right">Rp<?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-right" style="font-weight:bold">Total Uang Masuk</td>
                <td class="text-right" style="font-weight:bold">Rp<?= number_format($total_masuk, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>
    
    <!-- Expense Table -->
    <h3 class="section-title">UANG KELUAR</h3>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">Tanggal</th>
                <th width="55%">Keterangan</th>
                <th width="25%" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            mysqli_data_seek($query_keluar, 0);
            while ($row = mysqli_fetch_assoc($query_keluar)): 
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                <td class="text-right">Rp<?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-right" style="font-weight:bold">Total Uang Keluar</td>
                <td class="text-right" style="font-weight:bold">Rp<?= number_format($total_keluar, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>
    
    <!-- Summary -->
    <div class="summary">
        <div class="summary-item masuk">
            <span>Total Pemasukan:</span>
            <span>Rp<?= number_format($total_masuk, 0, ',', '.'); ?></span>
        </div>
        <div class="summary-item keluar">
            <span>Total Pengeluaran:</span>
            <span>Rp<?= number_format($total_keluar, 0, ',', '.'); ?></span>
        </div>
        <div class="summary-item saldo">
            <span>Saldo Akhir:</span>
            <span>Rp<?= number_format($saldo, 0, ',', '.'); ?></span>
        </div>
    </div>
    
    <div class="footer">
        Dicetak pada <?= date('d/m/Y H:i:s'); ?> oleh <?= $_SESSION['username'] ?? 'System'; ?>
    </div>
</body>
</html>