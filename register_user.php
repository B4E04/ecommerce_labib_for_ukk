<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config/database.php";

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $pass     = $_POST['password'];
    $re_pass  = $_POST['confirm_password'];

    // Validasi Konfirmasi Password
    if ($pass !== $re_pass) {
        $error = "Password Salah! (Konfirmasi tidak cocok)";
    } else {
        // Cek apakah email sudah ada
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Email sudah terdaftar";
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            mysqli_query($conn, "
        INSERT INTO users(nama,email,password,role,created_at)
        VALUES('$nama','$email','$hashed_pass','user',NOW())
      ");
            header("Location: login_user.php?msg=register_ok");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .box {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-wrapper {
            max-width: 120px;
            height: auto;
        }

        .logo-wrapper img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        h2 {
            color: #1a1a1a;
            font-size: 24px;
            margin-bottom: 8px;
            text-align: center;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 24px;
        }

        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            margin: 6px 0 14px 0;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #f9fafb;
        }

        input:focus {
            outline: none;
            border-color: #0d6efd;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #0d6efd;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
        }

        button:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
        }

        button:active {
            transform: scale(0.98);
        }

        p.footer {
            margin-top: 24px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: #0d6efd;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="box">
        <div class="logo-container">
            <div class="logo-wrapper">
                <img src="cartixlogo.png" alt="Logo Perusahaan">
            </div>
        </div>

        <h2>Daftar Akun</h2>
        <p class="subtitle">Buat akun untuk mulai berbelanja</p>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="post">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Contoh: Labib" required value="<?= isset($_POST['nama']) ? $_POST['nama'] : '' ?>">

            <label>Email</label>
            <input type="email" name="email" placeholder="nama@email.com" required value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">

            <label>Password</label>
            <input type="password" name="password" placeholder="Minimal 8 karakter" required>

            <label>Konfirmasi Password</label>
            <input type="password" name="confirm_password" placeholder="Ulangi password" required>

            <button name="register">Daftar Sekarang</button>
        </form>

        <p class="footer">Sudah punya akun? <a href="login_user.php">Login di sini</a></p>
    </div>

</body>

</html>