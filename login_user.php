<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config/database.php";
session_start();

// Jika sudah login, lempar ke home
if (isset($_SESSION['user_id'])) {
    header("Location: user/home.php");
    exit;
}

$error = ""; 

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pass  = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND role='user'");

    if (mysqli_num_rows($q) == 1) {
        $u = mysqli_fetch_assoc($q);

        if (password_verify($pass, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['user_nama'] = $u['nama'];
            header("Location: user/home.php");
            exit;
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Email tidak ditemukan atau akun bukan tipe user.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User - Cartix</title>
    <style>
        /* CSS Disamakan persis dengan halaman Register */
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

        /* Tambahan untuk pesan sukses dari register */
        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #bbf7d0;
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

        <h2>Selamat Datang</h2>
        <p class="subtitle">Masukkan detail akun Anda untuk masuk</p>

        <?php if ($error != ""): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'register_ok'): ?>
            <div class="success">Pendaftaran berhasil! Silakan login.</div>
        <?php endif; ?>

        <form method="post">
            <label>Email</label>
            <input type="email" name="email" placeholder="nama@email.com" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit" name="login">Masuk Sekarang</button>
        </form>

        <p class="footer">Belum punya akun? <a href="register_user.php">Daftar di sini</a></p>
    </div>

</body>

</html>