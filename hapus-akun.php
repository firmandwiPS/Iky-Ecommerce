<?php


include 'config/app.php';
$id_akun = (int)$_GET['id_akun'];

session_start();

if (!isset($_SESSION["login"])) {
    echo"<script>
            alert('Login Dulu!!');
            document.location.href='login.php';
        </script>";
    exit;
  }

if (delete_akun($id_akun) > 0) {
        echo"<script>
                        alert('Data Akun Berhasil Di Hapus');
                        document.location.href='akun.php';
                    </script>";
            }else {
                echo"<script>
                        alert('Data Akun Gagal Di Hapus');
                        document.location.href='akun.php';
                    </script>";
}
?>