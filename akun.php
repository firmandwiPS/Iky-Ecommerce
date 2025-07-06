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

$title = 'Daftar Akun';
include 'layout/header.php';

$data_akun = select("SELECT * FROM akun");
$id_akun = intval($_SESSION['id_akun']);
$data_bylogin = select("SELECT * FROM akun WHERE id_akun = $id_akun");

// Tambah akun
if (isset($_POST['tambah'])) {
    if (create_akun($_POST) > 0) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Akun Berhasil Ditambahkan',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'akun.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Data Akun Gagal Ditambahkan',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'akun.php';
            });
        </script>";
    }
}

// Ubah akun
if (isset($_POST['ubah'])) {
    if (update_akun($_POST) > 0) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Akun Berhasil Diubah',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'akun.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Data Akun Gagal Diubah',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'akun.php';
            });
        </script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Akun</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($_SESSION['level'] == 1) : ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah Akun</span>
                    </button>
                    

                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Daftar Akun</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama</th>
                                <th class="d-none d-md-table-cell">Username</th>
                                <th class="d-none d-lg-table-cell">Email</th>
                                <th class="text-center">Password</th>
                                <th class="d-none d-sm-table-cell">Level</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php
                            $akuns = ($_SESSION['level'] == 1) ? $data_akun : $data_bylogin;
                            foreach ($akuns as $akun): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($akun['nama']); ?></td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($akun['username']); ?></td>
                                    <td class="d-none d-lg-table-cell"><?= htmlspecialchars($akun['email']); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">Ter-enkripsi</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <span class="badge <?= $akun['level'] == 1 ? 'bg-success' : 'bg-info' ?>">
                                            <?= $akun['level'] == 1 ? 'Admin' : 'Owner' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalUbah<?= $akun['id_akun']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($_SESSION['level'] == 1): ?>
                                                <button onclick="konfirmasiHapus(<?= $akun['id_akun']; ?>)" class="btn btn-sm btn-danger text-white">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
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
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="level" class="form-select" required>
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

<!-- Modal Ubah -->
<?php foreach ($data_akun as $akun): ?>
    <div class="modal fade" id="modalUbah<?= $akun['id_akun']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Ubah Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_akun" value="<?= $akun['id_akun']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($akun['nama']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($akun['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($akun['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <small>(Baru/Lama)</small></label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>

                        <?php if ($_SESSION['level'] == 1): ?>
                            <div class="mb-3">
                                <label class="form-label">Level</label>
                                <select name="level" class="form-select" required>
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
<?php endforeach; ?>

<style>
    /* Responsive table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Badge styling */
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    
    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .table th, .table td {
            padding: 0.5rem;
            font-size: 0.85rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>

<script>
function konfirmasiHapus(id) {
    Swal.fire({
        title: 'Yakin?',
        text: "Data akun akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'hapus-akun.php?id_akun=' + id;
        }
    });
}
</script>

<?php include 'layout/footer.php'; ?>