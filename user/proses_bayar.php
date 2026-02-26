<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $nama_pembeli = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $hp = mysqli_real_escape_string($conn, $_POST['hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    $total_final = $_POST['total_final'];
    $tanggal = date("Y-m-d H:i:s");

    // 1. Simpan ke tabel TRANSAKSI (Data utama untuk Admin)
    $sql_transaksi = "INSERT INTO transaksi (user_id, nama_pembeli, hp, alamat, total, metode_pembayaran, status, tanggal_transaksi) 
                      VALUES ('$user_id', '$nama_pembeli', '$hp', '$alamat', '$total_final', '$metode', 'menunggu', '$tanggal')";
    
    if (mysqli_query($conn, $sql_transaksi)) {
        $transaksi_id = mysqli_insert_id($conn); // Mengambil ID transaksi yang baru saja masuk

        // 2. Simpan rincian barang ke TRANSAKSI_DETAIL
        foreach ($_SESSION['keranjang'] as $produk_id => $qty) {
            $res_p = mysqli_query($conn, "SELECT harga FROM produk WHERE id = '$produk_id'");
            $row_p = mysqli_fetch_assoc($res_p);
            $harga_saat_ini = $row_p['harga'];

            $sql_detail = "INSERT INTO transaksi_detail (transaksi_id, produk_id, qty, harga) 
                           VALUES ('$transaksi_id', '$produk_id', '$qty', '$harga_saat_ini')";
            mysqli_query($conn, $sql_detail);
        }

        // 3. Bersihkan keranjang belanja
        unset($_SESSION['keranjang']);

        echo "<script>
                alert('Pesanan Berhasil dikirim ke Admin!');
                window.location='pembayaran.php?id=$transaksi_id';
              </script>";
    } else {
        echo "Gagal: " . mysqli_error($conn);
    }
}
?>