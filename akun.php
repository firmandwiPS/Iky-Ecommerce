<?php
session_start();
if (!isset($_SESSION["login"])) {
    echo "<script>
            alert('Login Dulu!!');
            document.location.href='login.php';
          </script>";
    exit;
}

$title = 'Daftar Akun';
include 'layout/header.php';



$data_akun = select("SELECT * FROM akun");

$id_akun = intval($_SESSION['id_akun']); // Hindari SQL injection
$data_bylogin = select("SELECT * FROM akun WHERE id_akun = $id_akun");

// Tambah akun
if (isset($_POST['tambah'])) {
    if (create_akun($_POST) > 0) {
        echo "<script>
                alert('Data Akun Berhasil Ditambahkan');
                document.location.href = 'akun.php';
              </script>";
    } else {
        echo "<script>
                alert('Data Akun Gagal Ditambahkan');
                document.location.href = 'akun.php';
              </script>";
    }
}

// Ubah akun
if (isset($_POST['ubah'])) {
    if (update_akun($_POST) > 0) {
        echo "<script>
                alert('Data Akun Berhasil Diubah');
                document.location.href = 'akun.php';
              </script>";
    } else {
        echo "<script>
                alert('Data Akun Gagal Diubah');
                document.location.href = 'akun.php';
              </script>";
    }
}
?>

<!-- Konten Utama -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Akun</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($_SESSION['level'] == 1) : ?>
                <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus text-white"></i> Tambah
                </a>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Daftar Akun</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="text-center bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php
                            $akuns = ($_SESSION['level'] == 1) ? $data_akun : $data_bylogin;
                            foreach ($akuns as $akun): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($akun['nama']); ?></td>
                                    <td><?= htmlspecialchars($akun['username']); ?></td>
                                    <td><?= htmlspecialchars($akun['email']); ?></td>
                                    <td>Password Ter-enkripsi</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUbah<?= $akun['id_akun']; ?>">
                                            <i class="fas fa-edit text-white"></i>
                                        </button>
                                        <?php if ($_SESSION['level'] == 1): ?>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $akun['id_akun']; ?>">
                                                <i class="fas fa-trash-alt text-white"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="labelTambah" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="labelTambah">Tambah Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
                    <div class="mb-3">
                        <label>Level</label>
                        <select name="level" class="form-control" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="1">Admin</option>
                            <option value="2">Owner</option>
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

<!-- Modal Ubah & Hapus -->
<?php foreach ($data_akun as $akun): ?>
    <!-- Modal Ubah -->
    <div class="modal fade" id="modalUbah<?= $akun['id_akun']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Ubah Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_akun" value="<?= $akun['id_akun']; ?>">
                        <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($akun['nama']); ?>" required></div>
                        <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($akun['username']); ?>" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($akun['email']); ?>" required></div>
                        <div class="mb-3"><label>Password <small>(Baru/Lama)</small></label><input type="password" name="password" class="form-control" required minlength="6"></div>

                        <?php if ($_SESSION['level'] == 1): ?>
                            <div class="mb-3">
                                <label>Level</label>
                                <select name="level" class="form-control" required>
                                    <option value="1" <?= $akun['level'] == 1 ? 'selected' : '' ?>>Admin</option>
                                    <option value="2" <?= $akun['level'] == 2 ? 'selected' : '' ?>>Owner</option>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="level" value="<?= $akun['level']; ?>">
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="ubah" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="modalHapus<?= $akun['id_akun']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Hapus Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menghapus akun <strong><?= htmlspecialchars($akun['nama']); ?></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="hapus-akun.php?id_akun=<?= $akun['id_akun']; ?>" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include 'layout/footer.php'; ?>
