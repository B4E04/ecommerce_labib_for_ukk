<?php
include "config/database.php";

// Konfigurasi Akun Petugas/Admin
$nama_petugas = "Petugas Cartix";
$email_petugas = "admin@gmail.com";
$password_baru = "admin123"; // Ini password yang akan diketik saat login

// Buat Hash Bcrypt
$hash_password = password_hash($password_baru, PASSWORD_DEFAULT);

// Hapus akun lama jika ada untuk menghindari duplikat
mysqli_query($conn, "DELETE FROM users WHERE email='$email_petugas'");

// Masukkan ke database dengan role 'admin'
$sql = "INSERT INTO users (nama, email, password, role) 
        VALUES ('$nama_petugas', '$email_petugas', '$hash_password', 'admin')";

if (mysqli_query($conn, $sql)) {
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h1>✅ Akun Petugas Berhasil Dibuat!</h1>";
    echo "<p>Email: <b>$email_petugas</b></p>";
    echo "<p>Password: <b>$password_baru</b></p>";
    echo "<br><a href='login_petugas.php' style='padding:10px 20px; background:#2563EB; color:white; text-decoration:none; border-radius:5px;'>Coba Login Sekarang</a>";
    echo "</div>";
} else {
    echo "Gagal membuat akun: " . mysqli_error($conn);
}
