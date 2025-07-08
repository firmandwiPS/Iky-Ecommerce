<?php
ob_start();
session_start();

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

$title = 'Uang Keluar';
include 'layout/header.php';

// Process add data
if (isset($_POST['tambah'])) {
    $tanggal = mysqli_real_escape_string($db, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($db, $_POST['keterangan']);
    $jumlah = str_replace(['Rp', '.', ' '], '', $_POST['jumlah']);
    $jumlah = (int)$jumlah;

    $query = "INSERT INTO data_uang_keluar (tanggal, keterangan, jumlah) 
              VALUES ('$tanggal', '$keterangan', '$jumlah')";

    if (mysqli_query($db, $query)) {
        $_SESSION['success'] = 'Data uang keluar berhasil ditambahkan';
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'uang-keluar.php';
            });
        </script>";
    } else {
        $_SESSION['error'] = 'Gagal menambahkan data: ' . mysqli_error($db);
    }
}

// Process edit data
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($db, $_POST['id']);
    $tanggal = mysqli_real_escape_string($db, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($db, $_POST['keterangan']);
    $jumlah = str_replace(['Rp', '.', ' '], '', $_POST['jumlah']);
    $jumlah = (int)$jumlah;

    $query = "UPDATE data_uang_keluar SET 
              tanggal = '$tanggal',
              keterangan = '$keterangan',
              jumlah = '$jumlah'
              WHERE id = '$id'";

    if (mysqli_query($db, $query)) {
        $_SESSION['success'] = 'Data berhasil diubah';
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil diubah!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'uang-keluar.php';
            });
        </script>";
    } else {
        $_SESSION['error'] = 'Gagal mengubah data: ' . mysqli_error($db);
    }
}

// Process delete data
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($db, $_GET['hapus']);
    $query = "DELETE FROM data_uang_keluar WHERE id = '$id'";

    if (mysqli_query($db, $query)) {
        $_SESSION['success'] = 'Data berhasil dihapus';
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil dihapus!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'uang-keluar.php';
            });
        </script>";
    } else {
        $_SESSION['error'] = 'Gagal menghapus data: ' . mysqli_error($db);
    }
}

// Query data
$query = mysqli_query($db, "SELECT * FROM data_uang_keluar ORDER BY tanggal DESC");
$total_result = mysqli_fetch_assoc(mysqli_query($db, "SELECT SUM(jumlah) as total FROM data_uang_keluar"));
$total_keluar = $total_result['total'] ?? 0;
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Uang Keluar</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah Data</span>
                </button>
            </div>

            <div class="alert alert-info">
                Total Uang Keluar: <strong>Rp <?= number_format($total_keluar, 0, ',', '.'); ?></strong>
            </div>

            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title">Daftar Uang Keluar</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-danger text-white">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($query) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                    <td><?= htmlspecialchars($row['keterangan']); ?></td>
                                    <td class="text-danger text-center">
                                        Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Detail Button -->
                                            <button class="btn btn-sm btn-info text-white" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalDetail<?= $row['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            <button class="btn btn-sm btn-warning text-white" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEdit<?= $row['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <a href="uang-keluar.php?hapus=<?= $row['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Detail Modal -->
                                <div class="modal fade" id="modalDetail<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">Detail Uang Keluar</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Tanggal</label>
                                                    <p><?= date('d F Y', strtotime($row['tanggal'])); ?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Keterangan</label>
                                                    <p><?= htmlspecialchars($row['keterangan']); ?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Jumlah</label>
                                                    <p class="text-danger">Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Ditambahkan Pada</label>
                                                    <p><?= date('d/m/Y H:i', strtotime($row['created_at'] ?? 'now')); ?></p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title">Edit Uang Keluar</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal</label>
                                                        <input type="date" name="tanggal" class="form-control" 
                                                               value="<?= $row['tanggal']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Keterangan</label>
                                                        <input type="text" name="keterangan" class="form-control" 
                                                               value="<?= htmlspecialchars($row['keterangan']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Jumlah</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" name="jumlah" class="form-control" 
                                                                   value="Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?>" required>
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
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data uang keluar</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Modal -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tambah Uang Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="jumlah" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Format input harga
    document.querySelectorAll('input[name="jumlah"]').forEach(input => {
        input.addEventListener('input', function(e) {
            // Hapus semua karakter non-digit
            let value = this.value.replace(/[^\d]/g, '');

            // Format sebagai Rupiah
            if (value.length > 0) {
                value = parseInt(value).toLocaleString('id-ID');
            }

            this.value = value;
        });
    });
</script>

<?php include 'layout/footer.php'; ?>
<?php ob_end_flush(); ?>