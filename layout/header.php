<?php 
include 'config/app.php'
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title; ?></title>

  <!-- DataTables -->
  <link rel="stylesheet" href="assets-template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets-template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="assets-template/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets-template/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="assets-template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="assets-template/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="assets-template/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets-template/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets-template/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="assets-template/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="assets-template/plugins/summernote/summernote-bs4.min.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.css">

  <!-- jQuery -->
  <script src="assets-template/plugins/jquery/jquery.min.js"></script>

  
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="assets-template/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div> -->

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Home</a>
      </li>
    </ul>


  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
<a href="index.php" class="brand-link bg-dark text-white" style="text-decoration: none;">
  <i class="fas fa-shopping-basket ml-2 mr-2"></i>
  <span class="brand-text font-weight-bold">Iky Ecommerce</span>
</a>



    <!-- Sidebar -->
    <div class="sidebar">


<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    
    <li class="nav-header">Daftar Menu</li>

    <!-- Dashboard -->
    <li class="nav-item">
      <a href="index.php" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <!-- Menu Makanan -->
    <li class="nav-item">
      <a href="makanan.php" class="nav-link">
        <i class="nav-icon fas fa-utensils"></i>
        <p>Menu Makanan</p>
      </a>
    </li>

    <!-- Data Akun -->
    <li class="nav-item">
      <a href="akun.php" class="nav-link">
        <i class="nav-icon fas fa-users"></i>
        <p>Data Akun</p>
      </a>
    </li>

    <!-- Logout -->
    <li class="nav-item">
      <a href="logout.php" class="nav-link" onclick="return confirm('Yakin Anda Ingin Keluar?');">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
      </a>
    </li>

  </ul>
</nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>