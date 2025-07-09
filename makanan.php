<?php
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

$title = 'Data Makanan';
include 'layout/header.php';

// Ambil semua data makanan
$data_makanan = select("SELECT * FROM makanan");

// Tambah makanan
if (isset($_POST['tambah'])) {
    $_POST['harga'] = str_replace(['Rp', '.', ' '], '', $_POST['harga']);
    $_POST['harga'] = (int)$_POST['harga'];

    if (tambah_makanan($_POST) > 0) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'makanan.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Data gagal ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'makanan.php';
            });
        </script>";
    }
}

// Ubah makanan
if (isset($_POST['ubah'])) {
    $_POST['harga'] = str_replace(['Rp', '.', ' '], '', $_POST['harga']);
    $_POST['harga'] = (int)$_POST['harga'];

    if (update_makanan($_POST) > 0) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil diubah!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'makanan.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Data gagal diubah!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'makanan.php';
            });
        </script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Makanan</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah Makanan</span>
                        </button>
                        
                        <!-- Compact Export Icons -->
                        <div class="flex space-x-2">  <!-- Added space-x-2 for horizontal spacing -->
                            <a href="makanan-excel.php" class="btn btn-sm btn-outline-success" title="Export Excel">
                                <i class="fas fa-file-excel"></i>
                                <span class="hidden md:inline ml-1">Excel</span>
                            </a>
                            <a href="makanan-pdf.php" class="btn btn-sm btn-outline-danger" title="Export PDF">
                                <i class="fas fa-file-pdf"></i>
                                <span class="hidden md:inline ml-1">PDF</span>
                            </a>
                            <a href="makanan-word.php" class="btn btn-sm btn-outline-primary" title="Export Word">
                                <i class="fas fa-file-word"></i>
                                <span class="hidden md:inline ml-1">Word</span>
                            </a>
                        </div>
                    </div>
                </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Daftar Makanan</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0" >
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Makanan</th>
                                <th class="text-center">Harga</th>
                                <th class="d-none d-md-table-cell">Kategori</th>
                                <th class="text-center">Stok</th>
                                <th class="d-none d-lg-table-cell">Deskripsi</th>
                                <th class="text-center">Gambar</th>
                                <th class="text-center">Recommended</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($data_makanan as $makanan): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($makanan['nama_makanan']); ?></td>
                                    <td class="text-nowrap text-center">Rp<?= number_format($makanan['harga'], 0, ',', '.'); ?></td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($makanan['kategori']); ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $makanan['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $makanan['stok']; ?>
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <div class="deskripsi-makanan">
                                            <?= htmlspecialchars($makanan['deskripsi']); ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <img src="gambar/<?= $makanan['gambar']; ?>" width="50" height="50" class="img-thumbnail">
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $makanan['recommended'] == 'Ya' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $makanan['recommended']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Detail Button -->
                                            <button type="button" class="btn btn-sm btn-info text-white" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalDetail<?= $makanan['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-sm btn-warning text-white" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalUbah<?= $makanan['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button onclick="konfirmasiHapus(<?= $makanan['id']; ?>)" 
                                                    class="btn btn-sm btn-danger text-white">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Detail -->
                                <div class="modal fade" id="modalDetail<?= $makanan['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">Detail Makanan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Nama Makanan</label>
                                                        <p><?= htmlspecialchars($makanan['nama_makanan']); ?></p>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Harga</label>
                                                        <p>Rp<?= number_format($makanan['harga'], 0, ',', '.'); ?></p>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Kategori</label>
                                                        <p><?= htmlspecialchars($makanan['kategori']); ?></p>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Stok</label>
                                                        <p><span class="badge <?= $makanan['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                            <?= $makanan['stok']; ?>
                                                        </span></p>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Deskripsi</label>
                                                    <p><?= htmlspecialchars($makanan['deskripsi']); ?></p>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Gambar</label>
                                                        <img src="gambar/<?= $makanan['gambar']; ?>" width="150" class="img-thumbnail">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Recommended</label>
                                                        <p><span class="badge <?= $makanan['recommended'] == 'Ya' ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= $makanan['recommended']; ?>
                                                        </span></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Ubah -->
                                <div class="modal fade" id="modalUbah<?= $makanan['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Ubah Makanan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $makanan['id']; ?>">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Nama Makanan</label>
                                                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($makanan['nama_makanan']); ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Harga</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp</span>
                                                                <input type="text" name="harga" class="form-control" value="Rp<?= number_format($makanan['harga'], 0, ',', '.'); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Kategori</label>
                                                            <select name="kategori" class="form-select" required>
                                                                <option value="Makanan" <?= $makanan['kategori'] == 'Makanan' ? 'selected' : '' ?>>Makanan</option>
                                                                <option value="Minuman" <?= $makanan['kategori'] == 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                                                                <option value="Snack" <?= $makanan['kategori'] == 'Snack' ? 'selected' : '' ?>>Snack</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Stok</label>
                                                            <input type="number" name="stok" class="form-control" value="<?= $makanan['stok']; ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($makanan['deskripsi']); ?></textarea>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Gambar (kosongkan jika tidak diubah)</label>
                                                            <input type="file" name="gambar" class="form-control">
                                                            <input type="hidden" name="gambar_lama" value="<?= $makanan['gambar']; ?>">
                                                            <small class="text-muted">Current: <?= $makanan['gambar']; ?></small>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Recommended</label>
                                                            <select name="recommended" class="form-select" required>
                                                                <option value="Ya" <?= $makanan['recommended'] == 'Ya' ? 'selected' : '' ?>>Ya</option>
                                                                <option value="Tidak" <?= $makanan['recommended'] == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="ubah" class="btn btn-success">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Makanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Makanan</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="harga" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Makanan">Makanan</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Snack">Snack</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gambar</label>
                            <input type="file" name="gambar" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Recommended</label>
                            <select name="recommended" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Responsive table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Deskripsi makanan */
    .deskripsi-makanan {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Badge status */
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }

    /* Gambar thumbnail */
    .img-thumbnail {
        max-width: 60px;
        height: auto;
        object-fit: cover;
    }

    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .table th,
        .table td {
            padding: 0.5rem;
            font-size: 0.85rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Export button styling */
    .dropdown-toggle::after {
        margin-left: 0.5em;
    }
</style>

<script>
    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Yakin?',
            text: "Data akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hapus-makanan.php?id=' + id;
            }
        });
    }

    // Format input harga
    document.querySelectorAll('input[name="harga"]').forEach(input => {
        input.addEventListener('input', function(e) {
            // Hapus semua karakter non-digit
            let value = this.value.replace(/[^\d]/g, '');

            // Format sebagai Rupiah
            if (value.length > 0) {
                value = 'Rp' + parseInt(value).toLocaleString('id-ID');
            }

            this.value = value;
        });
    });

    // Initialize DataTable with export buttons
    $(document).ready(function() {
        $('#dataTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.pageMargins = [20, 20, 20, 20];
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-info',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                }
            ],
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });

        // Manual export triggers
        $('#exportExcel').click(function() {
            $('#dataTable').DataTable().button('.buttons-excel').trigger();
        });

        $('#exportPdf').click(function() {
            $('#dataTable').DataTable().button('.buttons-pdf').trigger();
        });

        $('#exportWord').click(function() {
            // Word export requires a different approach
            window.location.href = 'export-word.php';
        });
    });
</script>

<?php include 'layout/footer.php'; ?> 