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

                header("Location: index.php");
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


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .login-container .form-floating input {
            border-radius: 10px;
        }
        .login-container .btn-primary {
            border-radius: 10px;
            border: none;
        }
        .login-container .btn-primary:hover {
            background: #2575ff;
        }
    </style>
</head>
<body class="bg-primary">
    <div class="login-container">
        <img src="assets/img/logo.svg" alt="Logo" width="80" class="mb-3">
        <h2 class="mb-4">Silahkan Login</h2>
        <?php if ($errorAuth || $errorRecaptcha) : ?>
    <div class="alert alert-danger" role="alert">
        <?= $errorMessage; ?>
    </div>
<?php endif; ?>
        <form action="" method="POST">
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username" required>
                <label for="floatingInput"><i class="fas fa-user"></i> Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword"><i class="fas fa-lock"></i> Password</label>
            </div>
            <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="6LfD7ggqAAAAAI6xTRycQzsNyt5f2b2fq0vi5XTN"></div>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit" name="login">Login</button>
        </form>
    </div>

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>