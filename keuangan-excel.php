<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

include './config/database.php';
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data uang masuk
$query_masuk = mysqli_query($db, "SELECT tanggal, jumlah, keterangan FROM data_uang_masuk ORDER BY tanggal DESC");
$total_masuk = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_masuk"))['total'] ?? 0;

// Ambil data uang keluar
$query_keluar = mysqli_query($db, "SELECT tanggal, jumlah, keterangan FROM data_uang_keluar ORDER BY tanggal DESC");
$total_keluar = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_keluar"))['total'] ?? 0;

$saldo = $total_masuk - $total_keluar;

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_keuangan_".date('Y-m-d').".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .header h3 {
            color: #7f8c8d;
            margin-top: 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 1.1em;
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
            font-size: 1.2em;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan</h1>
        <h3>Periode: <?= date('d/m/Y'); ?></h3>
    </div>
    
    <!-- Tabel Uang Masuk -->
    <h3>Uang Masuk</h3>
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
            <?php 
            $no = 1; 
            $total_masuk = 0;
            mysqli_data_seek($query_masuk, 0);
            while ($row = mysqli_fetch_assoc($query_masuk)): 
                $total_masuk += $row['jumlah'];
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                <td class="text-right">Rp<?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Uang Masuk</strong></td>
                <td class="text-right"><strong>Rp<?= number_format($total_masuk, 0, ',', '.'); ?></strong></td>
            </tr>
        </tbody>
    </table>
    
    <!-- Tabel Uang Keluar -->
    <h3 style="margin-top: 30px;">Uang Keluar</h3>
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
            <?php 
            $no = 1; 
            $total_keluar = 0;
            mysqli_data_seek($query_keluar, 0);
            while ($row = mysqli_fetch_assoc($query_keluar)): 
                $total_keluar += $row['jumlah'];
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                <td class="text-right">Rp<?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Uang Keluar</strong></td>
                <td class="text-right"><strong>Rp<?= number_format($total_keluar, 0, ',', '.'); ?></strong></td>
            </tr>
        </tbody>
    </table>
    
    <!-- Ringkasan -->
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
            <span>Rp<?= number_format(($total_masuk - $total_keluar), 0, ',', '.'); ?></span>
        </div>
    </div>
    
    <div class="footer">
        Dicetak pada <?= date('d/m/Y H:i:s'); ?> oleh <?= $_SESSION['username'] ?? 'System'; ?>
    </div>
</body>
</html>