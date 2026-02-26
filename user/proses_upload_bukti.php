<?php
session_start();
include "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti_transfer'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $user_id = $_SESSION['user_id'];

    // Konfigurasi Upload
    $target_dir = "../assets/bukti_transfer/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = strtolower(pathinfo($_FILES["bukti_transfer"]["name"], PATHINFO_EXTENSION));
    $new_filename = "BUKTI_" . $id_transaksi . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    $upload_ok = 1;

    // Validasi 1: Cek apakah itu gambar asli
    $check = getimagesize($_FILES["bukti_transfer"]["tmp_name"]);
    if ($check === false) {
        $upload_ok = 0;
        echo "<script>alert('File bukan gambar!'); window.history.back();</script>";
        exit;
    }

    // Validasi 2: Ukuran file (Maks 2MB)
    if ($_FILES["bukti_transfer"]["size"] > 2000000) {
        $upload_ok = 0;
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.'); window.history.back();</script>";
        exit;
    }

    // Validasi 3: Format file
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg") {
        $upload_ok = 0;
        echo "<script>alert('Hanya format JPG, JPEG, & PNG yang diizinkan!'); window.history.back();</script>";
        exit;
    }

    // Proses Simpan
    if ($upload_ok == 1) {
        if (move_uploaded_file($_FILES["bukti_transfer"]["tmp_name"], $target_file)) {
            
            /* LOGIKA UNTUK ADMIN & PETUGAS:
               1. Status diubah menjadi 'menunggu verifikasi' (agar Admin tahu ada kerjaan baru).
               2. Nama file disimpan ke database.
            */
            $path_to_save = "assets/bukti_transfer/" . $new_filename;
            $update = mysqli_query($conn, "UPDATE transaksi SET 
                bukti_pembayaran = '$path_to_save', 
                status = 'menunggu verifikasi' 
                WHERE id = '$id_transaksi' AND user_id = '$user_id'");

            if ($update) {
                echo "<script>
                    alert('Bukti transfer berhasil dikirim! Admin akan segera memverifikasi pesanan Anda.');
                    window.location.href = 'pesanan_saya.php'; 
                </script>";
            } else {
                echo "<script>alert('Gagal memperbarui data di database.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah file ke server.'); window.history.back();</script>";
        }
    }
} else {
    header("Location: home.php");
    exit;
}
?>