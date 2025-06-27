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

$id_akun = $_SESSION['id_akun'];
$data_bylogin = select("SELECT * FROM akun WHERE id_akun = $id_akun");

// jika tombol tambah di tekan jalankan ini
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

// jika tombol ubah di tekan jalankan ini
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

<div class="content-wrapper mt-5">
    <h1 class=""> Data Akun </h1>
    <hr>




<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm rounded">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title"> Tabel Data Akun</h3>
          </div>
          
          <!-- /.card-header -->
          <div class="card-body">
          <?php if ($_SESSION['level'] == 1 ) : ?>
    <button type="button" class="btn btn-primary mb-1 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambah"><i
            class="fa-solid fa-circle-plus "></i> Tambah</button>
<?php endif; ?>

            <!-- Table -->
            <div class="table-responsive">
              <table class="table table-bordered table-striped mt-3" id="table">
                <thead class="thead-light">
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
                  <?php if ($_SESSION['level'] == 1) : ?>
                    <?php foreach ($data_akun as $akun): ?>
                      <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($akun['nama']); ?></td>
                        <td><?= htmlspecialchars($akun['username']); ?></td>
                        <td><?= htmlspecialchars($akun['email']); ?></td>
                        <td>Password Ter-enkripsi</td>
                        <td width="20%" class="text-center">
                          <button type="button" class="btn btn-warning mb-1 rounded-pill" data-bs-toggle="modal"
                                  data-bs-target="#modalUbah<?= $akun['id_akun']; ?>">
                            <i class="fa-regular fa-pen-to-square"></i> Ubah
                          </button>
                          <button type="button" class="btn btn-danger mb-1 rounded-pill" data-bs-toggle="modal"
                                  data-bs-target="#modalHapus<?= $akun['id_akun']; ?>">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <?php foreach ($data_bylogin as $akun): ?>
                      <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($akun['nama']); ?></td>
                        <td><?= htmlspecialchars($akun['username']); ?></td>
                        <td><?= htmlspecialchars($akun['email']); ?></td>
                        <td>Password Ter-enkripsi</td>
                        <td width="20%" class="text-center">
                          <button type="button" class="btn btn-success mb-1 rounded-pill" data-bs-toggle="modal"
                                  data-bs-target="#modalUbah<?= $akun['id_akun']; ?>">
                            <i class="fa-regular fa-pen-to-square"></i> Ubah
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <!-- /.table-responsive -->
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col-12 -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>

<!-- Modal tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required
                            minlength="6">
                    </div>

                    <div class="mb-3">
                        <label for="level">Level</label>
                        <select name="level" id="level" class="form-control" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="1">Admin</option>
                            <!-- <option value="2"></option>
                            <option value="3">Operator Mahasiswa</option> -->
                        </select>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" name="tambah" class="btn btn-primary rounded-pill">Tambah</button>
            </div>
            </form>
        </div>
    </div>
</div>




<!-- Modal ubah -->
<?php foreach ($data_akun as $akun): ?>

    <div class="modal fade" id="modalUbah<?= $akun['id_akun']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Ubah Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <input type="hidden" name="id_akun" value="<?= $akun['id_akun']; ?>">

                        <div class="mb-3">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="<?= $akun['nama']; ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control"
                                value="<?= $akun['username']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= $akun['email']; ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="password">Password <small>(Masukan Password Baru/Lama)</small></label>
                            <input type="password" name="password" id="password" class="form-control" required
                                minlength="6">
                        </div>


                        <?php if ($_SESSION['level'] == 1) : ?>
                        <div class="mb-3">
                            <label for="level">Level</label>
                            <select name="level" id="level" class="form-control" required>
                                <?php $level = $akun['level']; ?>
                                <option value="1" <?php $level == '1' ? 'selected' : null ?>>Admin</option>
                                <!-- <option value="2" <?php $level == '2' ? 'selected' : null ?>>Operator Barang</option>
                                <option value="3" <?php $level == '3' ? 'selected' : null ?>>Operator Mahasiswa</option> -->
                            </select>
                        </div>
                            <?php else :?>
                                <input type="hidden" name="level" value="<?= $akun['level']; ?>">
                        <?php endif ;?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" name="ubah" class="btn btn-success rounded-pill">Ubah</button>
                </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal Hapus -->
<?php foreach ($data_akun as $akun): ?>
    <div class="modal fade" id="modalHapus<?= $akun['id_akun'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin Ingin Menghapus Data Akun : <?= $akun['nama']; ?> ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <a href="hapus-akun.php?id_akun=<?= $akun['id_akun'] ?>" class="btn btn-danger rounded-pill">Hapus</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>


<?php include 'layout/footer.php'; ?>