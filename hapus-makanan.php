<?php
include 'config/app.php';

// Check if ID parameter exists and is valid
if (!isset($_GET['id'])) {
    echo "<script>
            alert('ID tidak valid!');
            document.location.href = 'makanan.php';
          </script>";
    exit;
}

$id = (int)$_GET['id'];

// 1. Get image information before deleting data
$query = mysqli_query($db, "SELECT gambar FROM makanan WHERE id = $id");
if (!$query || mysqli_num_rows($query) === 0) {
    echo "<script>
            alert('Data tidak ditemukan!');
            document.location.href = 'makanan.php';
          </script>";
    exit;
}

$data = mysqli_fetch_assoc($query);
$gambar = $data['gambar'];

// 2. Delete data from database
$deleteQuery = mysqli_query($db, "DELETE FROM makanan WHERE id = $id");

if (!$deleteQuery) {
    echo "<script>
            alert('Gagal menghapus data dari database!');
            document.location.href = 'makanan.php';
          </script>";
    exit;
}

// 3. Delete associated image file if exists
if (!empty($gambar) && file_exists("gambar/" . $gambar)) {
    if (!unlink("gambar/" . $gambar)) {
        echo "<script>
                alert('Data berhasil dihapus tetapi gagal menghapus gambar!');
                document.location.href = 'makanan.php';
              </script>";
        exit;
    }
}

echo "<script>
        alert('Data dan gambar berhasil dihapus!');
        document.location.href = 'makanan.php';
      </script>";
?>
