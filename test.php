<?php
session_start();
// KITA PAKSA LOGIN TANPA CEK DATABASE
$_SESSION['user_id'] = 1;
$_SESSION['user_nama'] = "Tester";

echo "Session berhasil dibuat. Mencoba pindah ke home...<br>";
echo "<a href='user/home.php'>Klik di sini untuk ke Home</a>";

// Coba redirect otomatis
header("Location: user/home.php");
exit;
?>