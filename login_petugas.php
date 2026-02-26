<?php
session_start();
include __DIR__ . "/config/database.php";

// Jika sudah login, redirect ke dashboard petugas
if (isset($_SESSION['admin'])) {
    header("Location: petugas/dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    // Gunakan trim untuk menghapus spasi tak sengaja
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    // Ambil data user dengan role admin
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND role='admin' LIMIT 1");

    if ($query && mysqli_num_rows($query) > 0) {
        $admin = mysqli_fetch_assoc($query);

        // PERBAIKAN: Menggunakan password_verify untuk mengecek hash
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['nama'];
            header("Location: petugas/dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email admin/petugas tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Petugas - Cartix</title>
    <style>
        /* CSS Anda tetap sama, tidak ada perubahan di sini */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', Arial, sans-serif;
        }

        body {
            background: #F5F5F7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            background: #fff;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            width: 380px;
            text-align: center;
        }

        .login-card img {
            height: 100px;
            margin-bottom: 30px;
        }

        .login-card h2 {
            margin-bottom: 30px;
            font-size: 22px;
            color: #111827;
            font-weight: 600;
        }

        .login-card input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .login-card input:focus {
            border-color: #2563EB;
        }

        .login-card button {
            width: 100%;
            padding: 12px;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .error {
            color: #DC2626;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #6B7280;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <img src="cartixlogo.png" alt="Cartix Logo">
        <h2>Login Petugas</h2>
        <form method="post">
            <?php if ($error != '') echo "<div class='error'>$error</div>"; ?>
            <input type="email" name="email" placeholder="Email Petugas" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <div class="footer">© 2026 Cartix</div>
    </div>
</body>

</html>