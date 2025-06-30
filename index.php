<?php
session_start();
include 'config/app.php';

$errorAuth = false;
$errorRecaptcha = false;

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    $secret_key = "6LfD7ggqAAAAALNBUQexKPIdtNNwegV148xucQME";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-blue-50 flex items-center justify-center min-h-screen px-4">

<div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 md:p-8">
    <div class="flex justify-center mb-6">
        <img src="assets/img/logo.svg" alt="Logo" class="w-16 h-16">
    </div>

    <h2 class="text-center text-2xl font-bold text-blue-900 mb-4">Silakan Login</h2>

    <?php if ($errorAuth || $errorRecaptcha): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 text-sm px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm text-blue-900 font-medium mb-1" for="username">
                <i class="fas fa-user mr-1"></i> Username
            </label>
            <input type="text" name="username" id="username" required
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm text-blue-900 font-medium mb-1" for="password">
                <i class="fas fa-lock mr-1"></i> Password
            </label>
            <input type="password" name="password" id="password" required
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div class="flex justify-center">
            <div class="g-recaptcha" data-sitekey="6LfD7ggqAAAAAI6xTRycQzsNyt5f2b2fq0vi5XTN"></div>
        </div>

        <button type="submit" name="login"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium py-2 rounded-lg transition">
            Login
        </button>
    </form>

    <div class="text-center text-sm text-gray-500 mt-4">
        Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Daftar</a>
    </div>
</div>

</body>
</html>
