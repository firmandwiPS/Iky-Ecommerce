<?php

session_start();

if (!isset($_SESSION["login"])) {
    echo "<script>
            alert('Login Dulu!!');
            document.location.href='login.php';
        </script>";
    exit;
}

if ($_SESSION["level"] != 1 and $_SESSION["level"] != 3) {
    echo "<script>
            alert('Anda Tidak Memiliki Hak Akses');
            document.location.href='crud-modal.php';
        </script>";
    exit;
}

require __DIR__ . '/vendor/autoload.php';
require 'config/app.php';
  
use Spipu\Html2Pdf\Html2Pdf;

$data_mahasiswa = select("SELECT * FROM mahasiswa");

// Initialize the content variable
$content = '';

$content .= '<style type="text/css">
        .gambar {
            width: 50px;
        }
</style>';

$content .= '
<page>
    <table border="1" align="center">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Prodi</th>
            <th>Jenis Kelamin</th>
            <th>Telepon</th>
            <th>Email</th>
            <th>Foto</th>
        </tr>';

$no = 1;
foreach ($data_mahasiswa as $mahasiswa) {
    $content .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . $mahasiswa['nama'] . '</td>
        <td>' . $mahasiswa['prodi'] . '</td>
        <td>' . $mahasiswa['jk'] . '</td>
        <td>' . $mahasiswa['telepon'] . '</td>
        <td>' . $mahasiswa['email'] . '</td>
        <td><img src="http://localhost/crud-firman-php/assets/img/' . $mahasiswa['foto'] . '" class="gambar"></td>

    </tr>
    ';
}

$content .= '
    </table>
</page>';

$html2pdf = new Html2Pdf();
$html2pdf->writeHTML($content);
ob_start();

$html2pdf->output('Laporan-siswa.pdf');
?>
