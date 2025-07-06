<?php 
include 'config/app.php';
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

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.css">

  <!-- jQuery -->
  <script src="assets-template/plugins/jquery/jquery.min.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="dasboard.php" class="nav-link">Home</a>
        </li>
    </ul>
    
    <!-- Real-time Clock -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <div class="nav-link">
                <i class="far fa-clock mr-1"></i>
                <span id="live-clock" class="font-weight-bold"></span>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<script>
// Function to update the clock
function updateClock() {
    const now = new Date();
    
    // Format options
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: true
    };
    
    // Format the date and time
    const formattedDateTime = now.toLocaleDateString('id-ID', options);
    
    // Update the clock element
    document.getElementById('live-clock').textContent = formattedDateTime;
}

// Update the clock immediately and then every second
updateClock();
setInterval(updateClock, 1000);
</script>

<style>
/* Optional styling for the clock */
#live-clock {
    font-family: 'Courier New', monospace;
    color: #1e3a8a; /* Match your theme color */
    font-size: 0.9rem;
    white-space: nowrap;
}

@media (max-width: 768px) {
    #live-clock {
        font-size: 0.8rem;
    }
}
</style>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar elevation-4" style="background-color: #1e3a8a;"> <!-- blue-900 -->
    
    <!-- Brand Logo -->
    <a href="dasboard.php" class="brand-link text-white text-decoration-none">
      <i class="fas fa-shopping-basket ml-3 mr-2"></i>
      <span class="brand-text font-weight-bold">Iky Ecommerce</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <li class="nav-header text-white">DAFTAR MENU</li>

    <!-- Dashboard -->
    <li class="nav-item">
      <a href="dasboard.php" class="nav-link text-white">
        <i class="nav-icon fas fa-tachometer-alt text-white"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <!-- Data Makanan -->
    <li class="nav-item">
      <a href="makanan.php" class="nav-link text-white">
        <i class="nav-icon fas fa-utensils text-white"></i>
        <p>Data Makanan</p>
      </a>
    </li>



    <!-- Pesanan -->
    <li class="nav-item">
      <a href="data-pesanan.php" class="nav-link text-white">
        <i class="nav-icon fas fa-shopping-cart text-white"></i>
        <p>Data Pesanan</p>
      </a>
    </li>

    <!-- Ulasan -->
    <li class="nav-item">
      <a href="data-ulasan.php" class="nav-link text-white">
        <i class="nav-icon fas fa-star text-white"></i>
        <p>Data Ulasan</p>
      </a>
    </li>

    <!-- Pengeluaran -->
    <li class="nav-item has-treeview">
      <a href="#" class="nav-link text-white">
        <i class="nav-icon fas fa-money-bill-wave text-white"></i>
        <p>
          Data Pengeluaran
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">

        <!-- Uang Masuk -->
        <li class="nav-item">
          <a href="uang-masuk.php" class="nav-link text-white">
            <i class="far fa-circle nav-icon text-white"></i>
            <p>Data Uang Masuk</p>
          </a>
        </li>

        <!-- Uang Keluar -->
        <li class="nav-item">
          <a href="uang-keluar.php" class="nav-link text-white">
            <i class="far fa-circle nav-icon text-white"></i>
            <p>Data Uang Keluar</p>
          </a>
        </li>

      </ul>
    </li>


    <!-- Data Akun -->
    <li class="nav-item">
      <a href="akun.php" class="nav-link text-white">
        <i class="nav-icon fas fa-users text-white"></i>
        <p>Data Akun</p>
      </a>
    </li>

    <!-- Logout -->
    <li class="nav-item">
      <a href="logout.php" class="nav-link text-white" onclick="return confirm('Yakin Anda Ingin Keluar?');">
        <i class="nav-icon fas fa-sign-out-alt text-white"></i>
        <p>Logout</p>
      </a>
    </li>

  </ul>
</nav>

      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

</div>
<!-- ./wrapper -->

<!-- Optional JS Scripts (for Bootstrap or other plugin initialization) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
