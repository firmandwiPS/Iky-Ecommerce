<?php
session_start();

if (!isset($_SESSION["login"])) {
    echo "<script>
            alert('Login Dulu!!');
            document.location.href='login.php';
        </script>";
    exit;
}

$title = 'Data Ulasan';
include 'layout/header.php';

// Get all reviews with food names
$data_ulasan = select("SELECT u.*, m.nama_makanan 
                      FROM ulasan u 
                      JOIN makanan m ON u.makanan_id = m.id 
                      ORDER BY u.tanggal DESC");

// Get all foods for dropdown
$data_makanan = select("SELECT * FROM makanan ORDER BY nama_makanan ASC");

// Add review
if (isset($_POST['tambah'])) {
    $makanan_id = $_POST['makanan_id'];
    $nama_pengulas = $_POST['nama_pengulas'];
    $ulasan = $_POST['ulasan'];
    $kritik = $_POST['kritik'] ?? null;
    $saran = $_POST['saran'] ?? null;
    $rating = $_POST['rating'];

    $query = "INSERT INTO ulasan (makanan_id, nama_pengulas, ulasan, kritik, saran, rating) 
              VALUES ('$makanan_id', '$nama_pengulas', '$ulasan', '$kritik', '$saran', '$rating')";

    if (execute($query) > 0) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Ulasan berhasil ditambahkan!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'data-ulasan.php';
                });
            </script>";
    }
}

// Update review
if (isset($_POST['ubah'])) {
    $id = $_POST['id'];
    $makanan_id = $_POST['makanan_id'];
    $nama_pengulas = $_POST['nama_pengulas'];
    $ulasan = $_POST['ulasan'];
    $kritik = $_POST['kritik'] ?? null;
    $saran = $_POST['saran'] ?? null;
    $rating = $_POST['rating'];

    $query = "UPDATE ulasan SET 
                makanan_id = '$makanan_id',
                nama_pengulas = '$nama_pengulas',
                ulasan = '$ulasan',
                kritik = '$kritik',
                saran = '$saran',
                rating = '$rating'
              WHERE id = $id";

    if (execute($query) > 0) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Ulasan berhasil diubah!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'data-ulasan.php';
                });
            </script>";
    }
}

// Delete review
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];

    if (execute("DELETE FROM ulasan WHERE id = $id") > 0) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Dihapus!',
                    text: 'Ulasan telah dihapus!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'data-ulasan.php';
                });
            </script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Ulasan</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah Ulasan</span>
                </button>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Daftar Ulasan</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Makanan</th>
                                <th>Pengulas</th>
                                <th class="d-none d-md-table-cell">Ulasan</th>
                                <th class="text-center">Rating</th>
                                <th class="d-none d-lg-table-cell">Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($data_ulasan as $ulasan): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($ulasan['nama_makanan']); ?></td>
                                    <td><?= htmlspecialchars($ulasan['nama_pengulas']); ?></td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="ulasan-text">
                                            <strong>Ulasan:</strong> <?= htmlspecialchars($ulasan['ulasan']); ?><br>
                                            <?php if ($ulasan['kritik']): ?>
                                                <strong>Kritik:</strong> <?= htmlspecialchars($ulasan['kritik']); ?><br>
                                            <?php endif; ?>
                                            <?php if ($ulasan['saran']): ?>
                                                <strong>Saran:</strong> <?= htmlspecialchars($ulasan['saran']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        // Display star rating
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $ulasan['rating']) {
                                                echo '<i class="fas fa-star text-warning"></i>';
                                            } else {
                                                echo '<i class="far fa-star text-warning"></i>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?= date('d-m-Y H:i', strtotime($ulasan['tanggal'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalUbah<?= $ulasan['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger text-white" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $ulasan['id']; ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Ubah -->
                                <div class="modal fade" id="modalUbah<?= $ulasan['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <input type="hidden" name="id" value="<?= $ulasan['id']; ?>">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Ubah Ulasan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Makanan</label>
                                                        <select name="makanan_id" class="form-select" required>
                                                            <option value="">-- Pilih Makanan --</option>
                                                            <?php foreach ($data_makanan as $makanan): ?>
                                                                <option value="<?= $makanan['id'] ?>" <?= $makanan['id'] == $ulasan['makanan_id'] ? 'selected' : '' ?>>
                                                                    <?= $makanan['nama_makanan'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Pengulas</label>
                                                        <input type="text" name="nama_pengulas" value="<?= htmlspecialchars($ulasan['nama_pengulas']); ?>" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Ulasan</label>
                                                        <textarea name="ulasan" class="form-control" rows="3" required><?= htmlspecialchars($ulasan['ulasan']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Kritik</label>
                                                        <textarea name="kritik" class="form-control" rows="2"><?= htmlspecialchars($ulasan['kritik']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Saran</label>
                                                        <textarea name="saran" class="form-control" rows="2"><?= htmlspecialchars($ulasan['saran']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Rating</label>
                                                        <select name="rating" class="form-select" required>
                                                            <option value="1" <?= $ulasan['rating'] == 1 ? 'selected' : '' ?>>1 Bintang</option>
                                                            <option value="2" <?= $ulasan['rating'] == 2 ? 'selected' : '' ?>>2 Bintang</option>
                                                            <option value="3" <?= $ulasan['rating'] == 3 ? 'selected' : '' ?>>3 Bintang</option>
                                                            <option value="4" <?= $ulasan['rating'] == 4 ? 'selected' : '' ?>>4 Bintang</option>
                                                            <option value="5" <?= $ulasan['rating'] == 5 ? 'selected' : '' ?>>5 Bintang</option>
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

                                <!-- Modal Hapus -->
                                <div class="modal fade" id="modalHapus<?= $ulasan['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <input type="hidden" name="id" value="<?= $ulasan['id']; ?>">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Yakin ingin menghapus ulasan dari <strong><?= htmlspecialchars($ulasan['nama_pengulas']); ?></strong>?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="hapus" class="btn btn-danger">Hapus</button>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Ulasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Makanan</label>
                        <select name="makanan_id" class="form-select" required>
                            <option value="">-- Pilih Makanan --</option>
                            <?php foreach ($data_makanan as $makanan): ?>
                                <option value="<?= $makanan['id'] ?>"><?= $makanan['nama_makanan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pengulas</label>
                        <input type="text" name="nama_pengulas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ulasan</label>
                        <textarea name="ulasan" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kritik</label>
                        <textarea name="kritik" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Saran</label>
                        <textarea name="saran" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select" required>
                            <option value="">-- Pilih Rating --</option>
                            <option value="1">1 Bintang</option>
                            <option value="2">2 Bintang</option>
                            <option value="3">3 Bintang</option>
                            <option value="4">4 Bintang</option>
                            <option value="5">5 Bintang</option>
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

<style>
    .ulasan-text {
        max-width: 300px;
        word-wrap: break-word;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

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
</style>

<?php include 'layout/footer.php'; ?>