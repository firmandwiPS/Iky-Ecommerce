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

?>


  <?php include 'layout/footer.php' ?>