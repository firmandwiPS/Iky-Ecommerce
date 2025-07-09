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

// Ambil semua data makanan
$data_makanan = select("SELECT * FROM makanan");

// Set headers for Word download
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=data_makanan_".date('Y-m-d').".doc");
header("Pragma: no-cache");
header("Expires: 0");

// Path ke folder gambar
$gambar_path = $_SERVER['DOCUMENT_ROOT'] . '/toko-iki/gambar/';
?>

<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="ProgId" content="Word.Document">
    <meta name="Generator" content="Microsoft Word 15">
    <title>Laporan Data Makanan</title>
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
            vertical-align: middle;
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
        .status-ya {
            background-color: #2ecc71;
            color: white;
        }
        .status-tidak {
            background-color: #e74c3c;
            color: white;
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
        <h1>LAPORAN DATA MAKANAN</h1>
        <h3>Tanggal: <?= date('d/m/Y'); ?></h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="8%">Gambar</th>
                <th width="25%">Nama Makanan</th>
                <th width="15%" class="text-right">Harga</th>
                <th width="15%">Kategori</th>
                <th width="10%" class="text-center">Stok</th>
                <th width="12%" class="text-center">Recommended</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data_makanan as $makanan): ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center">
                    <?php 
                    if(!empty($makanan['gambar'])) {
                        $image_file = $gambar_path . $makanan['gambar'];
                        if (file_exists($image_file)) {
                            $image_url = 'http://' . $_SERVER['HTTP_HOST'] . '/toko-iki/gambar/' . $makanan['gambar'];
                            echo '<div style="width:30px;height:30px;margin:0 auto;overflow:hidden;display:flex;align-items:center;justify-content:center;">';
                            echo '<img src="'.$image_url.'" style="max-width:30px;max-height:30px;height:auto;width:auto;" alt="'.htmlspecialchars($makanan['nama_makanan']).'">';
                            echo '</div>';
                        } else {
                            echo '<span>-</span>';
                        }
                    } else {
                        echo '<span>-</span>';
                    }
                    ?>
                </td>
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
        Dicetak pada <?= date('d/m/Y H:i:s'); ?> oleh <?= $_SESSION['username'] ?? 'System'; ?>
    </div>
</body>
</html>