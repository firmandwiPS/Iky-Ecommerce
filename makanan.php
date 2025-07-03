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

<!-- SweetAlert2 CDN -->


<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Makanan</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus text-white"></i> Tambah
            </a>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Daftar Makanan</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="text-center bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Nama Makanan</th>
                                <th>Harga</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Deskripsi</th>
                                <th>Gambar</th>
                                <th>Recommended</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($data_makanan as $makanan): ?>
                                <tr class="text-center">
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($makanan['nama_makanan']); ?></td>
                                    <td>Rp<?= number_format($makanan['harga'], 0, ',', '.'); ?></td>
                                    <td><?= htmlspecialchars($makanan['kategori']); ?></td>
                                    <td><?= $makanan['stok']; ?></td>
                                    <td><?= htmlspecialchars($makanan['deskripsi']); ?></td>
                                    <td><img src="gambar/<?= $makanan['gambar']; ?>" width="60" class="img-thumbnail"></td>
                                    <td><?= $makanan['recommended']; ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <button type="button" class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalUbah<?= $makanan['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="konfirmasiHapus(<?= $makanan['id']; ?>)" class="btn btn-sm btn-danger text-white">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Ubah -->
                                <div class="modal fade" id="modalUbah<?= $makanan['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Ubah Makanan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $makanan['id']; ?>">
                                                    <div class="mb-3">
                                                        <label>Nama Makanan</label>
                                                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($makanan['nama_makanan']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Harga</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" name="harga" class="form-control" value="Rp<?= number_format($makanan['harga'], 0, ',', '.'); ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Kategori</label>
                                                        <select name="kategori" class="form-control" required>
                                                            <option value="Makanan" <?= $makanan['kategori'] == 'Makanan' ? 'selected' : '' ?>>Makanan</option>
                                                            <option value="Minuman" <?= $makanan['kategori'] == 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                                                            <option value="Snack" <?= $makanan['kategori'] == 'Snack' ? 'selected' : '' ?>>Snack</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Stok</label>
                                                        <input type="number" name="stok" class="form-control" value="<?= $makanan['stok']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Deskripsi</label>
                                                        <textarea name="deskripsi" class="form-control" required><?= htmlspecialchars($makanan['deskripsi']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Gambar (kosongkan jika tidak diubah)</label>
                                                        <input type="file" name="gambar" class="form-control">
                                                        <input type="hidden" name="gambar_lama" value="<?= $makanan['gambar']; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Recommended</label>
                                                        <select name="recommended" class="form-control" required>
                                                            <option value="Ya" <?= $makanan['recommended'] == 'Ya' ? 'selected' : '' ?>>Ya</option>
                                                            <option value="Tidak" <?= $makanan['recommended'] == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                                                        </select>
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
                                <!-- End Modal Ubah -->

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Makanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Makanan</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="harga" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Snack">Snack</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Gambar</label>
                        <input type="file" name="gambar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Recommended</label>
                        <select name="recommended" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Ya">Ya</option>
                            <option value="Tidak">Tidak</option>
                        </select>
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
<!-- End Modal Tambah -->

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
</script>

<?php include 'layout/footer.php'; ?>
