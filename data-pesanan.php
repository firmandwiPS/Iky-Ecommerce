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

$title = 'Data Pesanan';
include 'layout/header.php';

// Ambil data pesanan
$data_pesanan = select("SELECT * FROM pesanan ORDER BY created_at DESC");
$data_makanan = select("SELECT * FROM makanan ORDER BY nama_makanan ASC");

// Tambah pesanan
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_pelanggan'];
    $no_wa = $_POST['no_wa'];
    $status = $_POST['status'];

    // Ambil array dari input menu dan jumlah
    $menus = $_POST['menu'];
    $jumlahs = $_POST['jumlah'];

    // Proses detail dan total
    $detail = [];
    $total = 0;

    for ($i = 0; $i < count($menus); $i++) {
        $makanan = htmlspecialchars($menus[$i]);
        $jumlah = (int) $jumlahs[$i];

        // Ambil harga dari database berdasarkan nama makanan
        $m = select("SELECT harga FROM makanan WHERE nama_makanan = '$makanan' LIMIT 1");
        if ($m) {
            $harga = $m[0]['harga'];
            $subtotal = $harga * $jumlah;
            $total += $subtotal;

            $detail[] = "$makanan x$jumlah";
        }
    }

    $detail_str = implode(", ", $detail);

    $query = "INSERT INTO pesanan (nama_pelanggan, no_wa, detail, total, status)
              VALUES ('$nama', '$no_wa', '$detail_str', '$total', '$status')";

    if (execute($query) > 0) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Pesanan berhasil ditambahkan!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'data-pesanan.php';
            });
        </script>";
    }
}

// Ubah pesanan
if (isset($_POST['ubah'])) {
    $id = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($db, $_POST['nama_pelanggan']);
    $no_wa = mysqli_real_escape_string($db, $_POST['no_wa']);
    $status = mysqli_real_escape_string($db, $_POST['status']);
    $menus = $_POST['menu'] ?? [];
    $jumlahs = $_POST['jumlah'] ?? [];

    $detail = [];
    $total = 0;

    for ($i = 0; $i < count($menus); $i++) {
        $makanan = mysqli_real_escape_string($db, $menus[$i]);
        $jumlah = (int)$jumlahs[$i];

        if ($makanan && $jumlah > 0) {
            $result = mysqli_query($db, "SELECT harga FROM makanan WHERE nama_makanan = '$makanan' LIMIT 1");
            if ($result && mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $harga = (int)$data['harga'];
                $subtotal = $jumlah * $harga;
                $total += $subtotal;
                $detail[] = "$makanan x$jumlah";
            }
        }
    }

    $detail_str = implode(', ', $detail);

    $query = "UPDATE pesanan SET 
                nama_pelanggan = '$nama',
                no_wa = '$no_wa',
                detail = '$detail_str',
                total = $total,
                status = '$status'
              WHERE id = $id";

    if (mysqli_query($db, $query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Pesanan berhasil diubah!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'data-pesanan.php';
            });
        </script>";
    } else {
        echo "<script>alert('Gagal mengubah pesanan.');</script>";
    }
}

// Hapus pesanan
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    if (execute("DELETE FROM pesanan WHERE id = $id") > 0) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Dihapus!',
                text: 'Pesanan telah dihapus!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'data-pesanan.php';
            });
        </script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Pesanan</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="flex justify-between items-center mb-3">
                <div class="flex items-center">
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fas fa-plus"></i> <span class="hidden sm:inline">Tambah Pesanan</span>
                    </button>
                    
                    <!-- Compact Export Icons with spacing -->
                    <div class="flex space-x-2">  <!-- Added space-x-2 for horizontal spacing -->
                        <a href="pesanan-excel.php" class="btn btn-sm btn-outline-success" title="Export Excel">
                            <i class="fas fa-file-excel"></i>
                            <span class="hidden md:inline ml-1">Excel</span>
                        </a>
                        <a href="pesanan-pdf.php" class="btn btn-sm btn-outline-danger" title="Export PDF">
                            <i class="fas fa-file-pdf"></i>
                            <span class="hidden md:inline ml-1">PDF</span>
                        </a>
                        <a href="pesanan-word.php" class="btn btn-sm btn-outline-primary" title="Export Word">
                            <i class="fas fa-file-word"></i>
                            <span class="hidden md:inline ml-1">Word</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Daftar Pesanan</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Pelanggan</th>
                                <th class="d-none d-md-table-cell">No WA</th>
                                <th class="d-none d-sm-table-cell">Detail</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Status</th>
                                <th class="d-none d-lg-table-cell">Waktu</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($data_pesanan as $p): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span><?= htmlspecialchars($p['nama_pelanggan']); ?></span>
                                            <small class="text-muted d-md-none"><?= htmlspecialchars($p['no_wa']); ?></small>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($p['no_wa']); ?></td>
                                    <td class="d-none d-sm-table-cell">
                                        <div class="pesanan-detail">
                                            <?= nl2br(htmlspecialchars($p['detail'])); ?>
                                        </div>
                                    </td>
                                    <td class="text-nowrap text-center">
                                        Rp<?= number_format($p['total'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $status_class = '';
                                        if ($p['status'] == 'Selesai') $status_class = 'bg-success';
                                        elseif ($p['status'] == 'Dibatalkan') $status_class = 'bg-danger';
                                        else $status_class = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?= $status_class; ?>">
                                            <?= htmlspecialchars($p['status']); ?>
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?= date('d-m-Y H:i', strtotime($p['created_at'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $p['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalUbah<?= $p['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger text-white" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $p['id']; ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Detail -->
                                <div class="modal fade" id="modalDetail<?= $p['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">Detail Pesanan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <h6 class="fw-bold">Informasi Pelanggan</h6>
                                                    <div class="ps-3">
                                                        <p><strong>Nama:</strong> <?= htmlspecialchars($p['nama_pelanggan']); ?></p>
                                                        <p><strong>No. WA:</strong> <?= htmlspecialchars($p['no_wa']); ?></p>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <h6 class="fw-bold">Detail Pesanan</h6>
                                                    <div class="ps-3">
                                                        <?php 
                                                        $detail_items = explode(", ", $p['detail']);
                                                        echo '<ul class="mb-0">';
                                                        foreach ($detail_items as $item) {
                                                            echo '<li>' . htmlspecialchars($item) . '</li>';
                                                        }
                                                        echo '</ul>';
                                                        ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <h6 class="fw-bold">Informasi Pembayaran</h6>
                                                    <div class="ps-3">
                                                        <p><strong>Total:</strong> Rp<?= number_format($p['total'], 0, ',', '.'); ?></p>
                                                        <p>
                                                            <strong>Status:</strong> 
                                                            <span class="badge <?= $status_class; ?>">
                                                                <?= htmlspecialchars($p['status']); ?>
                                                            </span>
                                                        </p>
                                                        <p><strong>Waktu Pesan:</strong> <?= date('d-m-Y H:i', strtotime($p['created_at'])); ?></p>
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
                                <div class="modal fade" id="modalUbah<?= $p['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form method="post">
                                                <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Ubah Pesanan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Nama Pelanggan</label>
                                                            <input type="text" name="nama_pelanggan" value="<?= htmlspecialchars($p['nama_pelanggan']); ?>" class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">No WA</label>
                                                            <input type="text" name="no_wa" value="<?= htmlspecialchars($p['no_wa']); ?>" class="form-control" required>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Detail Pesanan</label>
                                                        <div id="menu-items-container-<?= $p['id'] ?>">
                                                            <?php
                                                            $detail_items = isset($p['detail']) ? explode(", ", $p['detail']) : [];
                                                            foreach ($detail_items as $item):
                                                                if (preg_match('/(.+?)\sx(\d+)/', $item, $match)) {
                                                                    $nama = trim($match[1]);
                                                                    $jumlah = (int)$match[2];
                                                                } else {
                                                                    $nama = trim($item);
                                                                    $jumlah = 1;
                                                                }
                                                            ?>
                                                                <div class="row mb-2 menu-row">
                                                                    <div class="col-md-6 mb-2 mb-md-0">
                                                                        <select name="menu[]" class="form-select" required>
                                                                            <option value="">-- Pilih Makanan --</option>
                                                                            <?php foreach ($data_makanan as $makanan): ?>
                                                                                <option value="<?= htmlspecialchars($makanan['nama_makanan']) ?>"
                                                                                    <?= $makanan['nama_makanan'] === $nama ? 'selected' : '' ?>>
                                                                                    <?= $makanan['nama_makanan'] ?> - Rp<?= number_format($makanan['harga'], 0, ',', '.') ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3 mb-2 mb-md-0">
                                                                        <input type="number" name="jumlah[]" value="<?= $jumlah ?>" min="1" class="form-control" required>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <button type="button" class="btn btn-danger w-100 btn-remove-menu">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-primary mt-2 btnTambahMenu" data-id="<?= $p['id'] ?>">
                                                            <i class="fas fa-plus"></i> Tambah Makanan
                                                        </button>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Total</label>
                                                            <input type="text" name="total" id="total-<?= $p['id'] ?>" value="Rp<?= number_format($p['total'], 0, ',', '.'); ?>" class="form-control" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="Sedang diproses" <?= $p['status'] == 'Sedang diproses' ? 'selected' : ''; ?>>Sedang diproses</option>
                                                                <option value="Selesai" <?= $p['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                                                <option value="Dibatalkan" <?= $p['status'] == 'Dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
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

                                <!-- Modal Hapus -->
                                <div class="modal fade" id="modalHapus<?= $p['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Yakin ingin menghapus pesanan dari <strong><?= htmlspecialchars($p['nama_pelanggan']); ?></strong>?</p>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No WA</label>
                            <input type="text" name="no_wa" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detail Pesanan</label>
                        <div id="detail-container">
                            <div class="row mb-2 detail-item">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <select name="menu[]" class="form-select" required>
                                        <option value="">-- Pilih Makanan --</option>
                                        <?php foreach ($data_makanan as $m): ?>
                                            <option value="<?= $m['nama_makanan'] ?>">
                                                <?= $m['nama_makanan'] ?> - Rp<?= number_format($m['harga'], 0, ',', '.') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" value="1" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger w-100 remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" id="add-item">
                            <i class="fas fa-plus"></i> Tambah Makanan
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total</label>
                            <input type="text" name="total" id="total-harga" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Sedang diproses">Sedang diproses</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
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

    /* Pesanan detail style */
    .pesanan-detail {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Badge status */
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
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
</style>

<script>
    // Data makanan untuk perhitungan
    const dataMakanan = <?= json_encode($data_makanan); ?>;

    // ==================== FUNGSI UNTUK MODAL TAMBAH ====================

    // Fungsi untuk menghitung total di modal tambah
    function calculateTotal() {
        let total = 0;

        // Hitung total dari semua item
        $('.detail-item').each(function() {
            const menu = $(this).find('select[name="menu[]"]').val();
            const jumlah = parseInt($(this).find('input[name="jumlah[]"]').val()) || 0;

            // Cari harga dari dataMakanan
            const item = dataMakanan.find(m => m.nama_makanan === menu);
            if (item) {
                total += item.harga * jumlah;
            }
        });

        // Format total ke Rupiah
        $('#total-harga').val('Rp' + total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ',-');
    }

    // Tambah item makanan di modal tambah
    $('#add-item').click(function() {
        const newItem = `
    <div class="row mb-2 detail-item">
        <div class="col-md-6 mb-2 mb-md-0">
            <select name="menu[]" class="form-select" required>
                <option value="">-- Pilih Makanan --</option>
                ${dataMakanan.map(m => 
                    `<option value="${m.nama_makanan}">
                        ${m.nama_makanan} - Rp${m.harga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}
                    </option>`
                ).join('')}
            </select>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" value="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger w-100 remove-item">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>`;

        $('#detail-container').append(newItem);
        calculateTotal();
    });

    // Hapus item makanan di modal tambah
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.detail-item').remove();
        calculateTotal();
    });

    // Hitung ulang saat ada perubahan di modal tambah
    $(document).on('change', 'select[name="menu[]"], input[name="jumlah[]"]', function() {
        calculateTotal();
    });

    // Hitung total awal di modal tambah
    calculateTotal();

    // ==================== FUNGSI UNTUK MODAL UBAH ====================

    // Fungsi untuk menambahkan baris makanan baru di modal ubah
    function tambahBarisMakanan(containerId) {
        const container = document.getElementById(`menu-items-container-${containerId}`);

        const row = document.createElement('div');
        row.className = 'row mb-2 menu-row';

        // Kolom Pilih Makanan
        const colSelect = document.createElement('div');
        colSelect.className = 'col-md-6 mb-2 mb-md-0';

        const select = document.createElement('select');
        select.name = 'menu[]';
        select.className = 'form-select';
        select.required = true;

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '-- Pilih Makanan --';
        select.appendChild(defaultOption);

        dataMakanan.forEach(makanan => {
            const option = document.createElement('option');
            option.value = makanan.nama_makanan;
            option.textContent = `${makanan.nama_makanan} - Rp${makanan.harga.toLocaleString('id-ID')}`;
            select.appendChild(option);
        });

        colSelect.appendChild(select);

        // Kolom Jumlah
        const colJumlah = document.createElement('div');
        colJumlah.className = 'col-md-3 mb-2 mb-md-0';

        const jumlahInput = document.createElement('input');
        jumlahInput.type = 'number';
        jumlahInput.name = 'jumlah[]';
        jumlahInput.className = 'form-control';
        jumlahInput.min = 1;
        jumlahInput.value = 1;
        jumlahInput.required = true;
        colJumlah.appendChild(jumlahInput);

        // Kolom Tombol Hapus
        const colBtn = document.createElement('div');
        colBtn.className = 'col-md-2';

        const btnRemove = document.createElement('button');
        btnRemove.type = 'button';
        btnRemove.className = 'btn btn-danger w-100 btn-remove-menu';
        btnRemove.innerHTML = '<i class="fas fa-trash"></i>';
        colBtn.appendChild(btnRemove);

        // Gabungkan semua kolom
        row.appendChild(colSelect);
        row.appendChild(colJumlah);
        row.appendChild(colBtn);

        // Tambahkan ke container
        container.appendChild(row);

        // Tambahkan event listeners
        select.addEventListener('change', () => hitungTotal(containerId));
        jumlahInput.addEventListener('input', () => hitungTotal(containerId));

        btnRemove.addEventListener('click', function() {
            row.remove();
            hitungTotal(containerId);
        });
    }

    // Fungsi untuk menghitung total di modal ubah
    function hitungTotal(id) {
        let total = 0;
        const container = document.getElementById(`menu-items-container-${id}`);
        const rows = container.querySelectorAll('.menu-row');

        rows.forEach(row => {
            const select = row.querySelector('select[name="menu[]"]');
            const input = row.querySelector('input[name="jumlah[]"]');

            if (select && input) {
                const menu = select.value;
                const jumlah = parseInt(input.value) || 0;

                const item = dataMakanan.find(m => m.nama_makanan === menu);
                if (item) {
                    total += item.harga * jumlah;
                }
            }
        });

        const totalInput = document.getElementById(`total-${id}`);
        totalInput.value = 'Rp' + total.toLocaleString('id-ID');
    }

    // Event listener untuk tombol tambah makanan di modal ubah
    document.querySelectorAll('.btnTambahMenu').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            tambahBarisMakanan(id);
        });
    });

    // Event delegation untuk tombol hapus yang sudah ada di modal ubah
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-menu')) {
            const row = e.target.closest('.menu-row');
            const container = row.closest('[id^="menu-items-container-"]');
            const id = container.id.replace('menu-items-container-', '');
            row.remove();
            hitungTotal(id);
        }
    });

    // Hitung total awal untuk setiap modal ubah
    document.querySelectorAll('[id^="menu-items-container-"]').forEach(container => {
        const id = container.id.replace('menu-items-container-', '');
        hitungTotal(id);
    });

    // Event listener untuk select dan input yang sudah ada di modal ubah
    document.addEventListener('change', function(e) {
        if (e.target.matches('select[name="menu[]"]')) {
            const container = e.target.closest('[id^="menu-items-container-"]');
            const id = container.id.replace('menu-items-container-', '');
            hitungTotal(id);
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name="jumlah[]"]')) {
            const container = e.target.closest('[id^="menu-items-container-"]');
            const id = container.id.replace('menu-items-container-', '');
            hitungTotal(id);
        }
    });
</script>

<?php include 'layout/footer.php'; ?>