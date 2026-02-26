<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

if (isset($_POST['id_produk'])) {
    $id_produk = $_POST['id_produk'];
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1; // Ambil qty dari form

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Jika produk sudah ada, tambahkan dengan qty baru
    if (isset($_SESSION['keranjang'][$id_produk])) {
        $_SESSION['keranjang'][$id_produk] += $qty;
    } else {
        $_SESSION['keranjang'][$id_produk] = $qty;
    }

    header("Location: home.php"); // Kembali ke home agar bisa lihat badge bertambah
    exit;
}
