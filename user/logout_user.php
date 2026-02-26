<?php
session_start();
session_unset();
session_destroy();

// Keluar folder user (../) untuk menemukan login_user.php di folder utama
header("Location: ../login_user.php");
exit;
?>