<?php 

session_start();

if (!isset($_SESSION["login"])) {
    echo"<script>
            alert('Logout ,dadah');
            document.location.href='login.php';
        </script>";
    exit;
}

$_SESSION = [];

session_unset();
session_destroy();
header("Location: login.php");