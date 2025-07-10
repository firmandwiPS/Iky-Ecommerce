<?php
session_start();
include 'config/app.php'; // pastikan koneksi DB disini

$errorAuth = false;
$errorRecaptcha = false;

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    $secret_key = "6LfD7ggqAAAAALNBUQexKPIdtNNwegV148xucQME"; // ganti dengan secret key kamu

    $verifikasi = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);
    $response = json_decode($verifikasi);

    if ($response->success) {
        $result = mysqli_query($db, "SELECT * FROM akun WHERE username = '$username'");
        if (mysqli_num_rows($result) == 1) {
            $hasil = mysqli_fetch_assoc($result);
            if (password_verify($password, $hasil['password'])) {
                $_SESSION['login'] = true;
                $_SESSION['id_akun'] = $hasil['id_akun'];
                $_SESSION['nama'] = $hasil['nama'];
                $_SESSION['username'] = $hasil['username'];
                $_SESSION['email'] = $hasil['email'];
                $_SESSION['level'] = $hasil['level'];
                header("Location: dasboard.php");
                exit;
            } else {
                $errorAuth = true;
                $errorMessage = "Password salah!";
            }
        } else {
            $errorAuth = true;
            $errorMessage = "Username tidak ditemukan!";
        }
    } else {
        $errorRecaptcha = true;
        $errorMessage = "Verifikasi reCAPTCHA gagal!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | KEDAISANTUY</title>
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-blue-50 flex items-center justify-center min-h-screen px-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 md:p-8">
        <h2 class="text-center text-2xl font-bold text-blue-900 mb-4">Silakan Login</h2>
        <?php if ($errorAuth || $errorRecaptcha): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 text-sm px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>
        <form method="POST" class="space-y-6 animate-fade-in">
            <!-- Username -->
            <div class="relative">
                <input type="text" name="username" id="username" required placeholder=" "
                    class="peer w-full px-4 pt-6 pb-2 rounded-lg border border-gray-300 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all duration-200">
                <label for="username"
                    class="absolute left-4 top-2 text-sm text-gray-500 transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-focus:top-2 peer-focus:text-sm peer-focus:text-blue-600">
                    <i class="fas fa-user mr-2 text-gray-400"></i> Username
                </label>
            </div>
            <!-- Password -->
            <div class="relative">
                <input type="password" name="password" id="password" required placeholder=" "
                    class="peer w-full px-4 pt-6 pb-2 pr-12 rounded-lg border border-gray-300 shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all duration-200">
                <label for="password"
                    class="absolute left-4 top-2 text-sm text-gray-500 transition-all duration-200 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-focus:top-2 peer-focus:text-sm peer-focus:text-blue-600">
                    <i class="fas fa-lock mr-2 text-gray-400"></i> Password
                </label>
                <!-- Eye toggle -->
                <button type="button" id="togglePassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors duration-150">
                    <i class="far fa-eye" id="eyeIcon"></i>
                </button>
            </div>
            <!-- reCAPTCHA -->
            <div class="flex justify-center">
                <div class="g-recaptcha" data-sitekey="6LfD7ggqAAAAAI6xTRycQzsNyt5f2b2fq0vi5XTN"></div>
            </div>
            <div class="flex gap-3 mt-4">
                <!-- Tombol Kembali ke Beranda -->
                <a href="index.php"
                    class="flex-1 basis-1/3 flex items-center justify-center gap-2 border border-gray-300 text-gray-600 hover:text-blue-600 hover:border-blue-500 text-sm font-medium py-2.5 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <!-- Tombol Masuk Sekarang (lebih panjang) -->
                <button type="submit" name="login"
                    class="flex-1 basis-2/3 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-lg shadow-sm transition-all duration-200">
                    <i class="fas fa-sign-in-alt text-white"></i> Masuk Sekarang
                </button>
            </div>
        </form>
    </div>

    <!-- Show/Hide Password Script -->
    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordField = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");

        togglePassword.addEventListener("click", () => {
            const type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;

            // Toggle eye icon
            eyeIcon.classList.toggle("fa-eye");
            eyeIcon.classList.toggle("fa-eye-slash");
        });
    </script>

</body>

</html>