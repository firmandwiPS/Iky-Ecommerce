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

// Ambil semua data makanan
$data_makanan = select("SELECT * FROM makanan");

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_makanan_".date('Y-m-d').".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Makanan</title>
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
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
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
        .status-ya {
            background-color: #2ecc71;
            color: white;
        }
        .status-tidak {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Makanan</h1>
        <h3>Tanggal: <?= date('d/m/Y'); ?></h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                
                <th>Nama Makanan</th>
                <th class="text-right">Harga</th>
                <th>Kategori</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Recommended</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data_makanan as $makanan): ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= htmlspecialchars($makanan['nama_makanan']); ?></td>
                <td class="text-right">Rp<?= number_format($makanan['harga'], 0, ',', '.'); ?></td>
                <td><?= htmlspecialchars($makanan['kategori']); ?></td>
                <td class="text-center"><?= $makanan['stok']; ?></td>
                <td class="text-center">
                    <span class="status-badge status-<?= strtolower($makanan['recommended']); ?>">
                        <?= $makanan['recommended']; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        Data diambil pada <?= date('d/m/Y H:i:s'); ?> | <?= $_SESSION['username'] ?? 'System'; ?>
    </div>
</body>
</html>