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

// Ambil semua data pesanan
$data_pesanan = select("SELECT * FROM pesanan ORDER BY created_at DESC");

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_pesanan_".date('Y-m-d').".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Pesanan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
            font-style: italic;
            font-size: 0.9em;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Pesanan</h1>
        <h3>Tanggal: <?= date('d/m/Y'); ?></h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>ID Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>No. WhatsApp</th>
                <th>Detail Pesanan</th>
                <th class="text-right">Total</th>
                <th>Status</th>
                <th>Tanggal Pesanan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data_pesanan as $pesanan): ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= htmlspecialchars($pesanan['id']); ?></td>
                <td><?= htmlspecialchars($pesanan['nama_pelanggan']); ?></td>
                <td><?= htmlspecialchars($pesanan['no_wa']); ?></td>
                <td><?= nl2br(htmlspecialchars($pesanan['detail'])); ?></td>
                <td class="text-right">Rp<?= number_format($pesanan['total'], 0, ',', '.'); ?></td>
                <td>
                    <span class="status-badge status-<?= strtolower(str_replace(' ', '', $pesanan['status'])); ?>">
                        <?= $pesanan['status']; ?>
                    </span>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($pesanan['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        Data diambil pada <?= date('d/m/Y H:i:s'); ?> | <?= $_SESSION['username'] ?? 'System'; ?>
    </div>
</body>
</html>