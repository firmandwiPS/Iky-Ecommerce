<?php
session_start();

if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit;
}

$title = 'Dashboard';
include 'layout/header.php';

// Koneksi database dengan error handling
$db = mysqli_connect('localhost', 'root', '', 'toko-iki');
if (!$db) {
  die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi untuk menjalankan query dengan error handling
function runQuery($db, $sql) {
  $result = mysqli_query($db, $sql);
  if (!$result) {
    die("Query error: " . mysqli_error($db));
  }
  return $result;
}

// Query data untuk info boxes
$total_makanan = mysqli_fetch_array(runQuery($db, "SELECT COUNT(*) FROM makanan"))[0];
$pesanan_hari_ini = mysqli_fetch_array(runQuery($db, "SELECT COUNT(*) FROM pesanan WHERE DATE(created_at) = CURDATE()"))[0];
$pendapatan_bulan_ini = mysqli_fetch_array(runQuery($db, "SELECT COALESCE(SUM(total), 0) FROM pesanan WHERE MONTH(created_at) = MONTH(CURRENT_DATE())"))[0];
$total_ulasan = mysqli_fetch_array(runQuery($db, "SELECT COUNT(*) FROM ulasan"))[0];

// Query data untuk diagram
$data_makanan = runQuery($db, "SELECT nama_makanan, stok FROM makanan ORDER BY stok DESC LIMIT 5");
$data_pesanan = runQuery($db, "SELECT DATE_FORMAT(created_at, '%Y-%m') AS bulan, COUNT(*) AS total_pesanan, COALESCE(SUM(total), 0) AS total_pendapatan FROM pesanan WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY bulan ORDER BY bulan");
$data_rating = runQuery($db, "SELECT m.nama_makanan, COALESCE(AVG(u.rating), 0) AS rata_rating, COUNT(u.id) AS total_ulasan FROM ulasan u JOIN makanan m ON u.makanan_id = m.id GROUP BY m.nama_makanan ORDER BY rata_rating DESC LIMIT 5");
$data_status_pesanan = runQuery($db, "SELECT status, COUNT(*) as jumlah FROM pesanan GROUP BY status");
$data_pesanan_harian = runQuery($db, "SELECT DATE(created_at) as tanggal, COUNT(*) as jumlah FROM pesanan WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) GROUP BY DATE(created_at) ORDER BY tanggal");
$data_kategori = runQuery($db, "SELECT kategori, COUNT(*) as jumlah FROM makanan GROUP BY kategori");
$data_rekomendasi = runQuery($db, "SELECT nama_makanan, recommended FROM makanan WHERE recommended = 'Ya' LIMIT 5");

// Query untuk ringkasan keuangan masuk
$query_masuk = "SELECT 
                COALESCE(SUM(jumlah), 0) as total_masuk_minggu,
                (SELECT COALESCE(SUM(jumlah), 0) FROM data_uang_masuk WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1) - 1) as total_masuk_minggu_lalu,
                (SELECT COALESCE(SUM(jumlah), 0) FROM data_uang_masuk WHERE MONTH(tanggal) = MONTH(CURRENT_DATE())) as total_masuk_bulan_ini
              FROM data_uang_masuk 
              WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)";
$result_masuk = runQuery($db, $query_masuk);
$data_masuk = mysqli_fetch_array($result_masuk);

// Query untuk ringkasan keuangan keluar
$query_keluar = "SELECT 
                COALESCE(SUM(jumlah), 0) as total_keluar_minggu,
                (SELECT COALESCE(SUM(jumlah), 0) FROM data_uang_keluar WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1) - 1) as total_keluar_minggu_lalu,
                (SELECT COALESCE(SUM(jumlah), 0) FROM data_uang_keluar WHERE MONTH(tanggal) = MONTH(CURRENT_DATE())) as total_keluar_bulan_ini
              FROM data_uang_keluar 
              WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)";
$result_keluar = runQuery($db, $query_keluar);
$data_keluar = mysqli_fetch_array($result_keluar);

// Query data keuangan untuk chart
$data_keuangan = runQuery($db, "SELECT 
                                tanggal, 
                                (SELECT COALESCE(SUM(jumlah), 0) FROM data_uang_masuk WHERE tanggal = d.tanggal) as total_masuk,
                                (SELECT COALESCE(SUM(jumlah), 0) FROM data_uang_keluar WHERE tanggal = d.tanggal) as total_keluar,
                                (SELECT COALESCE(SUM(jumlah), 0) FROM data_uang_masuk WHERE tanggal <= d.tanggal) - 
                                (SELECT COALESCE(SUM(jumlah), 0) FROM data_uang_keluar WHERE tanggal <= d.tanggal) as saldo
                              FROM 
                                (SELECT DISTINCT tanggal FROM (
                                  SELECT tanggal FROM data_uang_masuk 
                                  UNION 
                                  SELECT tanggal FROM data_uang_keluar
                                ) as combined_dates
                                ORDER BY tanggal DESC
                                LIMIT 7) as d
                              ORDER BY tanggal");

// Persiapan data untuk chart keuangan
$keuangan_labels = []; 
$masuk_data = []; 
$keluar_data = []; 
$saldo_data = [];

while($row = mysqli_fetch_array($data_keuangan)) {
    $keuangan_labels[] = date('d M', strtotime($row['tanggal']));
    $masuk_data[] = $row['total_masuk'];
    $keluar_data[] = $row['total_keluar'];
    $saldo_data[] = $row['saldo'];
}

// Hitung persentase perubahan keuangan
$total_masuk_minggu = $data_masuk['total_masuk_minggu'] ?? 0;
$total_masuk_minggu_lalu = $data_masuk['total_masuk_minggu_lalu'] ?? 0;
$persen_masuk_minggu = $total_masuk_minggu_lalu > 0 ? 
    (($total_masuk_minggu - $total_masuk_minggu_lalu) / $total_masuk_minggu_lalu * 100) : 0;

$total_keluar_minggu = $data_keluar['total_keluar_minggu'] ?? 0;
$total_keluar_minggu_lalu = $data_keluar['total_keluar_minggu_lalu'] ?? 0;
$persen_keluar_minggu = $total_keluar_minggu_lalu > 0 ? 
    (($total_keluar_minggu - $total_keluar_minggu_lalu) / $total_keluar_minggu_lalu * 100) : 0;

// Persiapan data untuk chart lainnya
$status_labels = []; $status_data = [];
$status_colors = [
    'Sedang diproses' => 'rgba(0, 123, 255, 0.7)',
    'Siap diantar' => 'rgba(40, 167, 69, 0.7)',
    'Selesai' => 'rgba(108, 117, 125, 0.7)',
    'Dibatalkan' => 'rgba(220, 53, 69, 0.7)'
];

while($row = mysqli_fetch_array($data_status_pesanan)) {
    $status_labels[] = $row['status'];
    $status_data[] = $row['jumlah'];
}

// Data untuk chart pesanan harian
$pesanan_labels = []; $pesanan_data = [];
while($row = mysqli_fetch_array($data_pesanan_harian)) {
    $pesanan_labels[] = date('d M', strtotime($row['tanggal']));
    $pesanan_data[] = $row['jumlah'];
}

// Data untuk chart kategori
$kategori_labels = []; $kategori_data = [];
while($row = mysqli_fetch_array($data_kategori)) {
    $kategori_labels[] = $row['kategori'];
    $kategori_data[] = $row['jumlah'];
}

// Data untuk makanan rekomendasi
$rekomendasi_labels = []; $rekomendasi_data = [];
while($row = mysqli_fetch_array($data_rekomendasi)) {
    $rekomendasi_labels[] = $row['nama_makanan'];
    $rekomendasi_data[] = 1;
}

// Reset pointer hasil query
mysqli_data_seek($data_makanan, 0);
mysqli_data_seek($data_pesanan, 0);
mysqli_data_seek($data_keuangan, 0);
mysqli_data_seek($data_rating, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .chart-container {
            position: relative;
            min-height: 250px;
        }
        .chart-container canvas {
            width: 100% !important;
            height: 100% !important;
        }
        .info-box {
            transition: transform 0.3s;
        }
        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            background-color: #f8f9fa;
        }
        .trend-up {
            color: #28a745;
        }
        .trend-down {
            color: #dc3545;
        }
        .trend-neutral {
            color: #6c757d;
        }
        .finance-card .card-body {
            padding: 1.5rem;
        }
        .finance-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .finance-card .trend-icon {
            font-size: 1.2rem;
            margin-left: 0.5rem;
        }
        @media (max-width: 768px) {
            .chart-container {
                min-height: 200px;
            }
            .info-box {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active"><i class="fas fa-home"></i> Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-utensils"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Menu Makanan</span>
                                <span class="info-box-number"><?php echo $total_makanan; ?></span>
                                <small>Total menu tersedia</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-shopping-cart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pesanan Hari Ini</span>
                                <span class="info-box-number"><?php echo $pesanan_hari_ini; ?></span>
                                <small><?php echo date('d M Y'); ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pendapatan Bulan Ini</span>
                                <span class="info-box-number">Rp <?php echo number_format($pendapatan_bulan_ini, 0, ',', '.'); ?></span>
                                <small><?php echo date('F Y'); ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-star"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ulasan Pelanggan</span>
                                <span class="info-box-number"><?php echo $total_ulasan; ?></span>
                                <small>Total rating diterima</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris Pertama: Pendapatan dan Stok -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Pendapatan 6 Bulan Terakhir</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="pendapatanChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Stok Makanan Terbanyak</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="stokChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris Kedua: Keuangan dan Rating -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-money-bill-trend-up mr-2"></i>Aliran Keuangan 7 Hari Terakhir</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="keuanganChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-star-half-alt mr-2"></i>Rating Makanan Terbaik</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="ratingChart"></canvas>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Berdasarkan rata-rata rating dari <?php echo $total_ulasan; ?> ulasan pelanggan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris Ketiga: Status Pesanan dan Kategori -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-clipboard-list mr-2"></i>Status Pesanan Terkini</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="statusPesananChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-tags mr-2"></i>Kategori Menu</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="kategoriChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris Keempat: Ringkasan Keuangan -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card finance-card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i>Ringkasan Keuangan Masuk</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3>Rp <?= number_format($total_masuk_minggu, 0, ',', '.') ?></h3>
                                <p class="mb-1">Minggu Ini</p>
                                <p class="<?= $persen_masuk_minggu > 0 ? 'trend-up' : ($persen_masuk_minggu < 0 ? 'trend-down' : 'trend-neutral') ?>">
                                    <?= number_format(abs($persen_masuk_minggu), 1) ?>%
                                    <i class="fas fa-arrow-<?= $persen_masuk_minggu > 0 ? 'up' : ($persen_masuk_minggu < 0 ? 'down' : 'right') ?> trend-icon"></i>
                                    vs minggu lalu (Rp <?= number_format($total_masuk_minggu_lalu, 0, ',', '.') ?>)
                                </p>
                                <hr>
                                <h5>Rp <?= number_format($data_masuk['total_masuk_bulan_ini'] ?? 0, 0, ',', '.') ?></h5>
                                <p class="mb-0">Total bulan ini</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card finance-card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i>Ringkasan Keuangan Keluar</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3>Rp <?= number_format($total_keluar_minggu, 0, ',', '.') ?></h3>
                                <p class="mb-1">Minggu Ini</p>
                                <p class="<?= $persen_keluar_minggu > 0 ? 'trend-up' : ($persen_keluar_minggu < 0 ? 'trend-down' : 'trend-neutral') ?>">
                                    <?= number_format(abs($persen_keluar_minggu), 1) ?>%
                                    <i class="fas fa-arrow-<?= $persen_keluar_minggu > 0 ? 'up' : ($persen_keluar_minggu < 0 ? 'down' : 'right') ?> trend-icon"></i>
                                    vs minggu lalu (Rp <?= number_format($total_keluar_minggu_lalu, 0, ',', '.') ?>)
                                </p>
                                <hr>
                                <h5>Rp <?= number_format($data_keluar['total_keluar_bulan_ini'] ?? 0, 0, ',', '.') ?></h5>
                                <p class="mb-0">Total bulan ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris Kelima: Pesanan Harian dan Rekomendasi -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-day mr-2"></i>Tren Pesanan 14 Hari Terakhir</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="pesananHarianChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-thumbs-up mr-2"></i>Menu Rekomendasi</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="rekomendasiChart"></canvas>
                                </div>
                                <div class="mt-3">
                                    <ul class="list-group">
                                        <?php mysqli_data_seek($data_rekomendasi, 0); ?>
                                        <?php while($row = mysqli_fetch_array($data_rekomendasi)): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?php echo $row['nama_makanan']; ?>
                                                <span class="badge bg-primary rounded-pill"><i class="fas fa-check"></i></span>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Fungsi untuk format mata uang
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // 1. Chart Pendapatan 6 Bulan Terakhir
    const pendapatanCtx = document.getElementById('pendapatanChart').getContext('2d');
    const pendapatanChart = new Chart(pendapatanCtx, {
        type: 'bar',
        data: {
            labels: <?php 
                $labels = [];
                mysqli_data_seek($data_pesanan, 0);
                while($row = mysqli_fetch_array($data_pesanan)) {
                    $labels[] = date('M Y', strtotime($row['bulan']));
                }
                echo json_encode($labels);
            ?>,
            datasets: [
                {
                    label: 'Total Pesanan',
                    data: <?php 
                        $data = [];
                        mysqli_data_seek($data_pesanan, 0);
                        while($row = mysqli_fetch_array($data_pesanan)) {
                            $data[] = $row['total_pesanan'];
                        }
                        echo json_encode($data);
                    ?>,
                    backgroundColor: 'rgba(60, 141, 188, 0.7)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Total Pendapatan',
                    data: <?php 
                        $data = [];
                        mysqli_data_seek($data_pesanan, 0);
                        while($row = mysqli_fetch_array($data_pesanan)) {
                            $data[] = $row['total_pendapatan'];
                        }
                        echo json_encode($data);
                    ?>,
                    type: 'line',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    backgroundColor: 'rgba(210, 214, 222, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(210, 214, 222, 1)',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label.includes('Pendapatan')) {
                                return label + ': ' + formatRupiah(context.raw);
                            }
                            return label + ': ' + context.raw;
                        }
                    }
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Jumlah Pesanan'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'Pendapatan (Rp)'
                    },
                    ticks: {
                        callback: function(value) {
                            return formatRupiah(value);
                        }
                    }
                }
            }
        }
    });

    // 2. Chart Stok Makanan
    const stokCtx = document.getElementById('stokChart').getContext('2d');
    const stokChart = new Chart(stokCtx, {
        type: 'doughnut',
        data: {
            labels: <?php 
                $labels = [];
                mysqli_data_seek($data_makanan, 0);
                while($row = mysqli_fetch_array($data_makanan)) {
                    $labels[] = $row['nama_makanan'];
                }
                echo json_encode($labels);
            ?>,
            datasets: [{
                data: <?php 
                    $data = [];
                    mysqli_data_seek($data_makanan, 0);
                    while($row = mysqli_fetch_array($data_makanan)) {
                        $data[] = $row['stok'];
                    }
                    echo json_encode($data);
                ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' porsi';
                        }
                    }
                }
            }
        }
    });

    // 3. Chart Aliran Keuangan
    const keuanganCtx = document.getElementById('keuanganChart').getContext('2d');
    const keuanganChart = new Chart(keuanganCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($keuangan_labels); ?>,
            datasets: [
                {
                    label: 'Uang Masuk',
                    data: <?php echo json_encode($masuk_data); ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Uang Keluar',
                    data: <?php echo json_encode($keluar_data); ?>,
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Saldo',
                    data: <?php echo json_encode($saldo_data); ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatRupiah(context.raw);
                        }
                    }
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return formatRupiah(value);
                        }
                    }
                }
            }
        }
    });

    // 4. Chart Rating Makanan
    const ratingCtx = document.getElementById('ratingChart').getContext('2d');
    const ratingChart = new Chart(ratingCtx, {
        type: 'bar',
        data: {
            labels: <?php 
                $labels = [];
                mysqli_data_seek($data_rating, 0);
                while($row = mysqli_fetch_array($data_rating)) {
                    $labels[] = $row['nama_makanan'];
                }
                echo json_encode($labels);
            ?>,
            datasets: [
                {
                    label: 'Rating Rata-rata',
                    data: <?php 
                        $data = [];
                        mysqli_data_seek($data_rating, 0);
                        while($row = mysqli_fetch_array($data_rating)) {
                            $data[] = $row['rata_rating'];
                        }
                        echo json_encode($data);
                    ?>,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Jumlah Ulasan',
                    data: <?php 
                        $data = [];
                        mysqli_data_seek($data_rating, 0);
                        while($row = mysqli_fetch_array($data_rating)) {
                            $data[] = $row['total_ulasan'];
                        }
                        echo json_encode($data);
                    ?>,
                    type: 'line',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label.includes('Rating')) {
                                return label + ': ' + context.raw.toFixed(1) + ' bintang';
                            }
                            return label + ': ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    title: {
                        display: true,
                        text: 'Rating (1-5)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Ulasan'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // 5. Chart Status Pesanan
    const statusPesananCtx = document.getElementById('statusPesananChart').getContext('2d');
    const statusPesananChart = new Chart(statusPesananCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_data); ?>,
                backgroundColor: [
                    <?php 
                        foreach($status_labels as $status) {
                            echo "'" . $status_colors[$status] . "',";
                        }
                    ?>
                ],
                borderColor: [
                    <?php 
                        foreach($status_labels as $status) {
                            echo "'" . str_replace('0.7', '1', $status_colors[$status]) . "',";
                        }
                    ?>
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' pesanan';
                        }
                    }
                }
            }
        }
    });

    // 6. Chart Kategori Menu
    const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
    const kategoriChart = new Chart(kategoriCtx, {
        type: 'polarArea',
        data: {
            labels: <?php echo json_encode($kategori_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($kategori_data); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' menu';
                        }
                    }
                }
            }
        }
    });

    // 7. Chart Pesanan Harian
    const pesananHarianCtx = document.getElementById('pesananHarianChart').getContext('2d');
    const pesananHarianChart = new Chart(pesananHarianCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($pesanan_labels); ?>,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: <?php echo json_encode($pesanan_data); ?>,
                backgroundColor: 'rgba(108, 117, 125, 0.2)',
                borderColor: 'rgba(108, 117, 125, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' pesanan';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // 8. Chart Rekomendasi
    const rekomendasiCtx = document.getElementById('rekomendasiChart').getContext('2d');
    const rekomendasiChart = new Chart(rekomendasiCtx, {
        type: 'radar',
        data: {
            labels: <?php echo json_encode($rekomendasi_labels); ?>,
            datasets: [{
                label: 'Menu Rekomendasi',
                data: <?php echo json_encode($rekomendasi_data); ?>,
                backgroundColor: 'rgba(255, 193, 7, 0.2)',
                borderColor: 'rgba(255, 193, 7, 1)',
                pointBackgroundColor: 'rgba(255, 193, 7, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 1
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Responsive behavior
    function handleResize() {
        pendapatanChart.resize();
        stokChart.resize();
        keuanganChart.resize();
        ratingChart.resize();
        statusPesananChart.resize();
        kategoriChart.resize();
        pesananHarianChart.resize();
        rekomendasiChart.resize();
    }

    window.addEventListener('resize', handleResize);
});
</script>

<?php include 'layout/footer.php'; ?>