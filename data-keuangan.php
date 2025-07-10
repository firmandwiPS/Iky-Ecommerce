<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["login"])) {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Login dulu ya!',
            showConfirmButton: true
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>";
    exit;
}

$title = 'Data Keuangan';
include 'layout/header.php';

// Function to format currency
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Get all transactions (both income and expense)
$query_masuk = mysqli_query($db, "SELECT id, tanggal, jumlah, keterangan FROM data_uang_masuk ORDER BY tanggal DESC");
$query_keluar = mysqli_query($db, "SELECT id, tanggal, jumlah, keterangan FROM data_uang_keluar ORDER BY tanggal DESC");

// Calculate totals
$total_masuk = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_masuk"))['total'] ?? 0;
$total_keluar = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_keluar"))['total'] ?? 0;
$saldo = $total_masuk - $total_keluar;

// Combine and sort transactions
$combined_data = [];

// Process income data
while ($row = mysqli_fetch_assoc($query_masuk)) {
    $row['jenis'] = 'Masuk';
    $row['color'] = 'success';
    $row['table'] = 'data_uang_masuk';
    $combined_data[] = $row;
}

// Process expense data
while ($row = mysqli_fetch_assoc($query_keluar)) {
    $row['jenis'] = 'Keluar';
    $row['color'] = 'danger';
    $row['table'] = 'data_uang_keluar';
    $combined_data[] = $row;
}

// Sort by date (newest first)
usort($combined_data, function($a, $b) {
    return strtotime($b['tanggal']) - strtotime($a['tanggal']);
});
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Keuangan</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
<!-- Summary Cards with Particle Effects -->
<div class="row mb-4 g-3">
    <div class="col-12 col-md-4">
        <div class="card bg-success text-white h-100 position-relative overflow-hidden">
            <!-- Particle effect for income -->
            <div class="income-particles"></div>
            <div class="card-body p-3 position-relative">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1">Total Uang Masuk</h5>
                        <p class="card-text h4 mb-0"><?= formatRupiah($total_masuk) ?></p>
                        <div class="mt-2">
                            <span class="badge bg-white text-success">
                                <i class="fas fa-arrow-up me-1"></i> Pemasukan
                            </span>
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-wallet fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card bg-danger text-white h-100 position-relative overflow-hidden">
            <!-- Particle effect for expense -->
            <div class="expense-particles"></div>
            <div class="card-body p-3 position-relative">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1">Total Uang Keluar</h5>
                        <p class="card-text h4 mb-0"><?= formatRupiah($total_keluar) ?></p>
                        <div class="mt-2">
                            <span class="badge bg-white text-danger">
                                <i class="fas fa-arrow-down me-1"></i> Pengeluaran
                            </span>
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card bg-primary text-white h-100 position-relative overflow-hidden">
            <!-- Particle effect for balance -->
            <div class="balance-particles"></div>
            <div class="card-body p-3 position-relative">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1">Saldo Akhir</h5>
                        <p class="card-text h4 mb-0"><?= formatRupiah($saldo) ?></p>
                        <div class="mt-2">
                            <span class="badge bg-white text-primary">
                                <i class="fas fa-balance-scale me-1"></i> Balance
                            </span>
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-piggy-bank fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Card styling */
    .card {
        min-height: 100%;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    /* Particle effects */
    .income-particles, 
    .expense-particles, 
    .balance-particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><circle fill="rgba(255,255,255,0.2)" cx="10" cy="10" r="1.5"/><circle fill="rgba(255,255,255,0.2)" cx="20" cy="20" r="1"/><circle fill="rgba(255,255,255,0.2)" cx="30" cy="30" r="1.5"/><circle fill="rgba(255,255,255,0.2)" cx="40" cy="40" r="1"/><circle fill="rgba(255,255,255,0.2)" cx="50" cy="50" r="1.5"/><circle fill="rgba(255,255,255,0.2)" cx="60" cy="60" r="1"/><circle fill="rgba(255,255,255,0.2)" cx="70" cy="70" r="1.5"/><circle fill="rgba(255,255,255,0.2)" cx="80" cy="80" r="1"/><circle fill="rgba(255,255,255,0.2)" cx="90" cy="90" r="1.5"/></svg>');
        opacity: 0.3;
        z-index: 0;
    }
    
    .income-particles {
        animation: floatParticles 15s linear infinite;
    }
    
    .expense-particles {
        animation: floatParticles 20s linear infinite reverse;
    }
    
    .balance-particles {
        animation: floatParticles 25s linear infinite alternate;
    }
    
    @keyframes floatParticles {
        0% { background-position: 0 0; }
        100% { background-position: 100px 100px; }
    }
    
    /* Badge styling */
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
        border-radius: 50px;
    }
    
    /* Mobile optimization */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem !important;
        }
        .card-title {
            font-size: 1rem !important;
        }
        .card-text {
            font-size: 1.25rem !important;
        }
        .card i {
            font-size: 1.5rem !important;
        }
    }
    
    /* Content positioning */
    .card-body {
        z-index: 1;
    }
</style>

<!-- Action Buttons -->
<div class="d-flex justify-content-between mb-3">
    <div class="d-flex gap-2">
        <a href="uang-masuk.php" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Tambah Pemasukan</span>
        </a>
    </div>
    
    <div class="d-flex gap-2">
        <a href="keuangan-excel.php" class="btn btn-outline-success" title="Export Excel">
            <i class="fas fa-file-excel"></i>
            <span class="d-none d-md-inline">Excel</span>
        </a>
        <a href="keuangan-pdf.php" class="btn btn-outline-danger" title="Export PDF">
            <i class="fas fa-file-pdf"></i>
            <span class="d-none d-md-inline">PDF</span>
        </a>
        <a href="keuangan-word.php" class="btn btn-outline-primary" title="Export Word">
            <i class="fas fa-file-word"></i>
            <span class="d-none d-md-inline">Word</span>
        </a>
    </div>
    
    <div class="d-flex gap-2">
        <a href="uang-keluar.php" class="btn btn-danger">
            <i class="fas fa-minus-circle"></i> <span class="d-none d-sm-inline">Tambah Pengeluaran</span>
        </a>
    </div>
</div>
<!-- Transactions Table -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Riwayat Transaksi</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered mb-0">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="text-center">No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($combined_data)): ?>
                    <?php $no = 1; foreach ($combined_data as $transaksi): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= date('d/m/Y', strtotime($transaksi['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($transaksi['keterangan']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?= $transaksi['color'] ?>">
                                <?= $transaksi['jenis'] ?>
                            </span>
                        </td>
                        <td class="text-<?= $transaksi['color'] ?> text-center">
                            <?= formatRupiah($transaksi['jumlah']) ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Detail Button -->
                                <button class="btn btn-sm btn-info text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDetail<?= $transaksi['jenis'].$transaksi['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-warning text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= $transaksi['jenis'].$transaksi['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <!-- Delete Button -->
                                <a href="<?= $transaksi['jenis'] == 'Masuk' ? 'uang-masuk.php' : 'uang-keluar.php' ?>?hapus=<?= $transaksi['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Detail Modal -->
                    <div class="modal fade" id="modalDetail<?= $transaksi['jenis'].$transaksi['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">Detail Transaksi</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Jenis Transaksi</label>
                                        <p><span class="badge bg-<?= $transaksi['color'] ?>"><?= $transaksi['jenis'] ?></span></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tanggal</label>
                                        <p><?= date('d F Y', strtotime($transaksi['tanggal'])) ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Keterangan</label>
                                        <p><?= htmlspecialchars($transaksi['keterangan']) ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Jumlah</label>
                                        <p class="text-<?= $transaksi['color'] ?>"><?= formatRupiah($transaksi['jumlah']) ?></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="modalEdit<?= $transaksi['jenis'].$transaksi['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" action="<?= $transaksi['jenis'] == 'Masuk' ? 'uang-masuk.php' : 'uang-keluar.php' ?>">
                                    <input type="hidden" name="id" value="<?= $transaksi['id'] ?>">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Transaksi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal</label>
                                            <input type="date" name="tanggal" class="form-control" 
                                                   value="<?= $transaksi['tanggal'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <input type="text" name="keterangan" class="form-control" 
                                                   value="<?= htmlspecialchars($transaksi['keterangan']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" name="jumlah" class="form-control" 
                                                       value="<?= number_format($transaksi['jumlah'], 0, ',', '') ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit" class="btn btn-warning">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data transaksi</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Remove hover effect from table rows */
    .table tbody tr {
        transition: none;
    }
    
    .table tbody tr:hover {
        background-color: inherit !important;
    }
    
    /* Keep the striped effect if you want */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.02);
    }
</style>

        </div>
    </section>
</div>

<?php include 'layout/footer.php'; ?>