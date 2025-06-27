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



