<?php
include 'config/app.php';

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title; ?></title>

<!-- Favicon Keranjang Belanja Putih -->
<!-- Versi hitam-putih dengan desain lebih clean -->
<link rel="apple-touch-icon" sizes="180x180" href="https://cdn-icons-png.flaticon.com/512/3144/3144456.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://cdn-icons-png.flaticon.com/512/3144/3144456.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://cdn-icons-png.flaticon.com/512/3144/3144456.png">

<!-- Versi putih dengan background transparan -->
<link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4AkEEjIZJ4HjZgAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAJklEQVQ4y2NgGAXDFmzatMmKAQ38f//+vT8qLzKQZRgqYAQDAF8DEW0QZvWZAAAAAElFTkSuQmCC" />

<!-- Manifest dan theme color -->
<link rel="manifest" href="site.webmanifest">
<meta name="theme-color" content="#ffffff">

<!-- Untuk Windows -->
<meta name="msapplication-TileImage" content="https://cdn-icons-png.flaticon.com/512/3144/3144456.png">
<meta name="msapplication-TileColor" content="#ffffff">

<!-- Fallback untuk browser lama -->
<link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/3144/3144456.png">



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
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">



<script src="https://cdn.tailwindcss.com"></script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- Export Libraries -->
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

  <!-- jQuery -->
  <script src="assets-template/plugins/jquery/jquery.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Di bagian head -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Sebelum penutup body -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">



<!-- Preloader Toko admin panel -->
<div class="preloader flex-column justify-content-center align-items-center bg-dark">
  <div class="industrial-loader">
    <div class="gear gear-large animation__rotate">
      <svg viewBox="0 0 100 100" width="60" height="60">
        <path d="M50 15L55 30L70 30L60 40L70 55L55 60L50 75L45 60L30 55L40 40L30 30L45 30Z" fill="#f39c12"/>
      </svg>
    </div>
    <div class="gear gear-small animation__rotate-reverse">
      <svg viewBox="0 0 100 100" width="40" height="40">
        <path d="M50 15L55 30L70 30L60 40L70 55L55 60L50 75L45 60L30 55L40 40L30 30L45 30Z" fill="#e74c3c"/>
      </svg>
    </div>
    <div class="industrial-text mt-3 text-white font-weight-bold">
      <span class="letter-animation">
        <span>A</span>
        <span>D</span>
        <span>M</span>
        <span>I</span>
        <span>N</span>
        <span>&nbsp;</span>
        <span>P</span>
        <span>A</span>
        <span>N</span>
        <span>E</span>
        <span>L</span>
      </span>
    </div>
    <div class="loading-bar mt-2">
      <div class="bar animation__loading"></div>
    </div>
  </div>
</div>

<style>
  /* Animasi Dasar */
  @keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  @keyframes rotate-reverse {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(-360deg); }
  }
  
  @keyframes loading {
    0% { width: 0%; }
    100% { width: 100%; }
  }
  
  @keyframes letter-bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
  }
  
  /* Style Preloader */
  .preloader {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    background: linear-gradient(135deg, #2c3e50, #34495e) !important;
  }
  
  .industrial-loader {
    position: relative;
    text-align: center;
  }
  
  .gear {
    display: inline-block;
    margin: 0 10px;
  }
  
  .gear-large {
    filter: drop-shadow(0 0 5px rgba(243, 156, 18, 0.7));
  }
  
  .gear-small {
    position: absolute;
    top: 25px;
    right: 20px;
    filter: drop-shadow(0 0 5px rgba(231, 76, 60, 0.7));
  }
  
  .animation__rotate {
    animation: rotate 3s linear infinite;
  }
  
  .animation__rotate-reverse {
    animation: rotate-reverse 2s linear infinite;
  }
  
  .animation__loading {
    animation: loading 2.5s ease-in-out infinite;
  }
  
  .letter-animation span {
    display: inline-block;
    font-size: 1.5rem;
    animation: letter-bounce 1.5s ease infinite;
  }
  
  .letter-animation span:nth-child(1) { animation-delay: 0.1s; color: #f39c12; }
  .letter-animation span:nth-child(2) { animation-delay: 0.2s; color: #e74c3c; }
  .letter-animation span:nth-child(3) { animation-delay: 0.3s; color: #3498db; }
  .letter-animation span:nth-child(4) { animation-delay: 0.4s; color: #2ecc71; }
  .letter-animation span:nth-child(5) { animation-delay: 0.5s; color: #9b59b6; }
  .letter-animation span:nth-child(6) { animation-delay: 0.6s; color: #1abc9c; }
  .letter-animation span:nth-child(7) { animation-delay: 0.7s; color: #d35400; }
  .letter-animation span:nth-child(8) { animation-delay: 0.8s; color: #f1c40f; }
  .letter-animation span:nth-child(9) { animation-delay: 0.1s; color: #f39c12; }
  .letter-animation span:nth-child(10) { animation-delay: 0.2s; color: #e74c3c; }
  .letter-animation span:nth-child(11) { animation-delay: 0.5s; color: #9b59b6; }
  
  .loading-bar {
    width: 200px;
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    overflow: hidden;
    margin: 0 auto;
  }
  
  .loading-bar .bar {
    height: 100%;
    background: linear-gradient(90deg, #f39c12, #e74c3c);
    border-radius: 2px;
  }
</style>

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
        color: #1e3a8a;
        /* Match your theme color */
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
    <aside class="main-sidebar elevation-4" style="background-color: #1e3a8a;"
    
    > <!-- blue-900 -->

      <!-- Brand Logo -->
<!-- Brand Logo -->
<a href="dasboard.php" class="brand-link text-decoration-none">
  <div class="d-flex align-items-center">
    <div class="brand-icon bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm">
      <!-- Ganti class text-gradient-primary dengan text-primary -->
      <i class="fas fa-shopping-basket text-primary"></i>
    </div>
    <div class="brand-text ml-2">
      <span class="font-weight-bold d-block">Admin Panel</span>
      <small class="text-white opacity-75 d-block">Toko Iki Management</small>
    </div>
  </div>
</a>

<style>
  :root {
    --gradient-start: #6a11cb;
    --gradient-end: #2575fc;
    --hover-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    --primary-color: #6a11cb; /* Warna untuk icon */
  }
  
  .brand-link {
    padding: 12px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    display: inline-block;
    width: 100%;
  }
  
  .brand-link:hover {
    transform: translateY(-2px);
    box-shadow: var(--hover-shadow);
    background: linear-gradient(135deg, #5a0cb5, #1a65e8);
  }
  
  .brand-icon {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
  }
  
  .text-primary {
    color: var(--primary-color) !important;
  }
  
  .brand-text span {
    font-size: 1.2rem;
    line-height: 1.2;
    color: white;
  }
  
  .brand-text small {
    font-size: 0.7rem;
  }
</style>

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

<li class="nav-item has-treeview" id="data-pengeluaran-menu">
  <a href="#" class="nav-link" data-widget="treeview">
    <i class="nav-icon fas fa-money-bill-wave text-white"></i>
    <p class="text-white">
      Data Pengeluaran
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview" style="padding-left: 15px;">
    <!-- Data Keuangan -->
    <li class="nav-item">
      <a href="data-keuangan.php" class="nav-link <?php echo ($currentPage == 'data-keuangan.php') ? 'active' : ''; ?>" style="<?php echo ($currentPage == 'data-keuangan.php') ? 'background-color: rgba(255,255,255,0.1);' : ''; ?>">
        <div class="d-flex align-items-center">
          <i class="fas fa-wallet nav-icon mr-2" style="color: #17a2b8;"></i>
          <div>
            <p class="mb-0 text-white">Data Keuangan</p>
          </div>
        </div>
      </a>
    </li>

    <!-- Uang Masuk -->
    <li class="nav-item mt-1">
      <a href="uang-masuk.php" class="nav-link <?php echo ($currentPage == 'uang-masuk.php') ? 'active' : ''; ?>" style="<?php echo ($currentPage == 'uang-masuk.php') ? 'background-color: rgba(255,255,255,0.1);' : ''; ?>">
        <div class="d-flex align-items-center">
          <i class="fas fa-arrow-circle-down nav-icon mr-2" style="color: #28a745;"></i>
          <div>
            <p class="mb-0 text-white">Data Uang Masuk</p>
          </div>
        </div>
      </a>
    </li>

    <!-- Uang Keluar -->
    <li class="nav-item mt-1">
      <a href="uang-keluar.php" class="nav-link <?php echo ($currentPage == 'uang-keluar.php') ? 'active' : ''; ?>" style="<?php echo ($currentPage == 'uang-keluar.php') ? 'background-color: rgba(255,255,255,0.1);' : ''; ?>">
        <div class="d-flex align-items-center">
          <i class="fas fa-arrow-circle-up nav-icon mr-2" style="color: #dc3545;"></i>
          <div>
            <p class="mb-0 text-white">Data Uang Keluar</p>
          </div>
        </div>
      </a>
    </li>
  </ul>
</li>


<script>
// Fungsi untuk menyimpan state menu
function saveMenuState(menuId, isOpen) {
  localStorage.setItem(menuId, isOpen);
}

// Fungsi untuk memuat state menu
function loadMenuState(menuId) {
  return localStorage.getItem(menuId) === 'true';
}

// Inisialisasi saat dokumen siap
document.addEventListener('DOMContentLoaded', function() {
  // Menu Data Pengeluaran
  const pengeluaranMenu = document.getElementById('data-pengeluaran-menu');
  
  // Cek apakah salah satu submenu aktif
  const isSubmenuActive = pengeluaranMenu.querySelector('.nav-link.active') !== null;
  
  // Load state dari localStorage atau set default berdasarkan submenu aktif
  const savedState = loadMenuState('data-pengeluaran-menu');
  const shouldOpen = savedState !== null ? savedState : isSubmenuActive;
  
  if (shouldOpen) {
    pengeluaranMenu.classList.add('menu-open');
  } else {
    pengeluaranMenu.classList.remove('menu-open');
  }
  
  // Tambahkan event listener untuk toggle
  const toggleLink = pengeluaranMenu.querySelector('a[data-widget="treeview"]');
  toggleLink.addEventListener('click', function(e) {
    e.preventDefault();
    const isCurrentlyOpen = pengeluaranMenu.classList.contains('menu-open');
    pengeluaranMenu.classList.toggle('menu-open');
    saveMenuState('data-pengeluaran-menu', !isCurrentlyOpen);
  });
  
  // Auto buka menu jika submenu aktif
  if (isSubmenuActive) {
    pengeluaranMenu.classList.add('menu-open');
    saveMenuState('data-pengeluaran-menu', true);
  }
});

// Untuk menu lainnya jika ada
// document.querySelectorAll('.nav-item.has-treeview').forEach(menu => {
//   const menuId = menu.id || menu.querySelector('a').textContent.trim();
//   ...
// });
</script>

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
</body>

</html>