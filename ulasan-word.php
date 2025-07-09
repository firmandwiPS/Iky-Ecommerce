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

// Function to execute query and fetch data
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

// Get all review data
$data_ulasan = select("SELECT * FROM ulasan ORDER BY tanggal DESC");

// Set headers for Word download
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=laporan_ulasan_".date('Y-m-d').".doc");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Ulasan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            margin: 1cm;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #3498db;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 18pt;
        }
        .header h3 {
            color: #7f8c8d;
            margin-top: 0;
            font-size: 12pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #3498db;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #2c3e50;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .text-center {
            text-align: center;
        }
        .wrap-text {
            word-wrap: break-word;
            max-width: 200px;
        }
        .rating {
            background-color: #f39c12;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .footer {
            text-align: right;
            margin-top: 20px;
            font-style: italic;
            color: #7f8c8d;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA ULASAN PELANGGAN</h1>
        <h3>Periode: <?= date('d F Y'); ?></h3>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">ID Ulasan</th>
                <th width="15%">ID Makanan</th>
                <th width="15%">Nama Pengulas</th>
                <th width="25%">Ulasan</th>
                <th width="15%">Kritik</th>
                <th width="15%">Saran</th>
                <th width="5%">Rating</th>
                <th width="10%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_rating = 0;
            foreach ($data_ulasan as $ulasan):
                $total_rating += $ulasan['rating'];
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $ulasan['id']; ?></td>
                <td><?= $ulasan['makanan_id']; ?></td>
                <td><?= htmlspecialchars($ulasan['nama_pengulas']); ?></td>
                <td class="wrap-text"><?= nl2br(htmlspecialchars($ulasan['ulasan'])); ?></td>
                <td class="wrap-text"><?= $ulasan['kritik'] ? nl2br(htmlspecialchars($ulasan['kritik'])) : '-'; ?></td>
                <td class="wrap-text"><?= $ulasan['saran'] ? nl2br(htmlspecialchars($ulasan['saran'])) : '-'; ?></td>
                <td class="text-center">
                    <span class="rating"><?= $ulasan['rating']; ?>/5</span>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($ulasan['tanggal'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align: right; font-weight: bold;">Rata-rata Rating:</td>
                <td class="text-center" style="font-weight: bold;">
                    <span class="rating">
                        <?= count($data_ulasan) > 0 ? round($total_rating/count($data_ulasan), 1) : 0; ?>/5
                    </span>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada <?= date('d/m/Y H:i:s'); ?> oleh <?= htmlspecialchars($_SESSION['username'] ?? 'System'); ?>
    </div>
</body>
</html>