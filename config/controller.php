<?php

function select($query)
{
 global $db;
 $result =mysqli_query($db, $query);
 $rows = [];
 while($row = mysqli_fetch_array($result))
 { 
    $rows[] = $row;
    }
    return $rows;
   return $result;
}

function execute($query)
{
    global $db;
    mysqli_query($db, $query);
    return mysqli_affected_rows($db);
}


function create_akun($post)
{
    global $db;

    $nama = strip_tags($post['nama']);
    $username = strip_tags($post['username']);
    $email = strip_tags($post['email']);
    $password = strip_tags($post['password']);
    $level = strip_tags($post['level']);

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    //query tambah data
    $query = "INSERT INTO akun VALUES(null, '$nama', '$username', '$email', '$password', '$level')";

    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function delete_akun($id_akun)
{
    global $db;

    //query hapus data akun
    $query = "DELETE FROM akun WHERE id_akun = $id_akun";

    mysqli_query($db, $query);

    return mysqli_affected_rows($db);


}


function update_akun($post)
{
    global $db;

    $id_akun = strip_tags($post['id_akun']);
    $nama = strip_tags($post['nama']);
    $username = strip_tags($post['username']);
    $email = strip_tags($post['email']);
    $password = strip_tags($post['password']);
    $level = strip_tags($post['level']);

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    //query ubah data
    $query = "UPDATE akun SET nama = '$nama', username = '$username', email = '$email', password = '$password', level = '$level' WHERE id_akun = $id_akun";

    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}


function tambah_makanan($data) {
    global $db;

    $nama       = htmlspecialchars($data['nama']);
    $harga      = (int)$data['harga'];
    $kategori   = htmlspecialchars($data['kategori']);
    $stok       = (int)$data['stok'];
    $deskripsi  = htmlspecialchars($data['deskripsi']);
    $recommended = htmlspecialchars($data['recommended']);

    // Upload gambar
    if ($_FILES['gambar']['error'] === 0) {
        $gambar = uniqid() . '-' . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'gambar/' . $gambar);
    } else {
        $gambar = 'default.png';
    }

    $query = "INSERT INTO makanan (nama_makanan, harga, kategori, stok, deskripsi, gambar, recommended)
              VALUES ('$nama', $harga, '$kategori', $stok, '$deskripsi', '$gambar', '$recommended')";

    return mysqli_query($db, $query);
}




function update_makanan($data) {
    global $db;

    $id         = (int)$data['id'];
    $nama       = htmlspecialchars($data['nama']);
    $harga      = (int)$data['harga'];
    $kategori   = htmlspecialchars($data['kategori']);
    $stok       = (int)$data['stok'];
    $deskripsi  = htmlspecialchars($data['deskripsi']);
    $recommended = htmlspecialchars($data['recommended']);

    // Ambil gambar lama
    $result = mysqli_query($db, "SELECT gambar FROM makanan WHERE id = $id");
    $oldImage = mysqli_fetch_assoc($result)['gambar'];

    if ($_FILES['gambar']['error'] === 0) {
        $gambarBaru = uniqid() . '-' . basename($_FILES['gambar']['name']);
        $uploadPath = 'gambar/' . $gambarBaru;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath)) {
            // Hapus gambar lama jika ada dan bukan default
            if (!empty($oldImage) && file_exists('gambar/' . $oldImage) && $oldImage !== 'default.png') {
                unlink('gambar/' . $oldImage);
            }
        } else {
            $gambarBaru = $oldImage;
        }
    } else {
        $gambarBaru = $oldImage;
    }

    $query = "UPDATE makanan SET
                nama_makanan = '$nama',
                harga = $harga,
                kategori = '$kategori',
                stok = $stok,
                deskripsi = '$deskripsi',
                gambar = '$gambarBaru',
                recommended = '$recommended'
              WHERE id = $id";

    return mysqli_query($db, $query);
}





