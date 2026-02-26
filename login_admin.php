<?php
session_start();
include __DIR__ . "/config/database.php";

// Jika sudah login sebagai admin, langsung lempar ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: admin/dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    // Cari user dengan role 'admin'
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND role='admin' LIMIT 1");

    if ($query && mysqli_num_rows($query) > 0) {
        $admin = mysqli_fetch_assoc($query);

        // --- LOGIKA PERBAIKAN OTOMATIS (Mencegah Password Salah karena Plain Text) ---
        // Jika password di DB sama persis dengan yang diketik (berarti belum di-hash)
        if ($password === $admin['password']) {
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id='".$admin['id']."'");
            // Update variabel agar verifikasi di bawah berhasil
            $admin['password'] = $new_hash;
        }
        // ----------------------------------------------------------------------------

        // Verifikasi Password menggunakan Bcrypt
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['nama'];

            header("Location: admin/dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Akun Admin tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Cartix</title>
    <style>
        /* ===== RESET & BASE ===== */
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

        /* ===== FORM CARD ===== */
        .login-card {
            background: #fff;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            width: 380px;
            text-align: center;
        }

        .login-card img {
            height: 80px;
            margin-bottom: 20px;
            object-fit: contain;
        }

        .login-card h2 {
            margin-bottom: 30px;
            font-size: 22px;
            color: #111827;
            font-weight: 600;
        }

        /* ===== INPUTS ===== */
        .login-card input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .login-card input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* ===== BUTTON ===== */
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
            transition: background 0.2s;
            margin-top: 10px;
        }

        .login-card button:hover {
            background: #1D4ED8;
        }

        /* ===== ERROR ===== */
        .error {
            background: #FEE2E2;
            color: #DC2626;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #FECACA;
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
        <img src="cartixlogo.png" alt="Cartix Logo" onerror="this.src='https://via.placeholder.com/150?text=Cartix'">

        <h2>Login Admin</h2>

        <?php if ($error != ''): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email Admin" required autofocus>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="login">Masuk ke Dashboard</button>
        </form>

        <div class="footer">© 2026 Cartix</div>
    </div>

</body>
</html>