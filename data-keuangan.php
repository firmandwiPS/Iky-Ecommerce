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
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Uang Masuk</h5>
                            <p class="card-text h4"><?= formatRupiah($total_masuk) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Uang Keluar</h5>
                            <p class="card-text h4"><?= formatRupiah($total_keluar) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Saldo Akhir</h5>
                            <p class="card-text h4"><?= formatRupiah($saldo) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between mb-3">
                <a href="uang-masuk.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Tambah Pemasukan</span>
                </a>
                <a href="uang-keluar.php" class="btn btn-danger">
                    <i class="fas fa-minus-circle"></i> <span class="d-none d-sm-inline">Tambah Pengeluaran</span>
                </a>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Riwayat Transaksi</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0">
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
        </div>
    </section>
</div>

<?php include 'layout/footer.php'; ?>