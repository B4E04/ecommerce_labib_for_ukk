<?php
session_start();
include "../config/database.php";

$id_transaksi = $_GET['id'] ?? '';

if (empty($id_transaksi)) {
    header("Location: home.php");
    exit;
}

// Ambil data transaksi untuk ditampilkan ke user
$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = '$id_transaksi' AND user_id = '{$_SESSION['user_id']}'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Pesanan tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan Berhasil | Cartix</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .success-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 450px;
            width: 100%;
        }

        .icon {
            font-size: 60px;
            color: #10b981;
            margin-bottom: 20px;
        }

        h2 {
            margin: 0 0 10px;
            color: #1f2937;
        }

        p {
            color: #6b7280;
            line-height: 1.6;
        }

        .order-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: left;
        }

        .order-box div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* Styling Tambahan untuk Upload */
        .upload-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed #e5e7eb;
        }

        .upload-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
        }

        .input-file {
            width: 100%;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .btn-upload {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }

        .btn-upload:hover {
            background: #059669;
        }

        .btn-home {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: 0.3s;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-home:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>

    <div class="success-card">
        <i class="fa-solid fa-circle-check icon"></i>
        <h2>Pesanan Diterima!</h2>
        <p>Terima kasih sudah berbelanja. Pesanan Anda sedang diproses oleh admin.</p>

        <div class="order-box">
            <div><span>ID Transaksi:</span> <strong>#<?= $data['id'] ?></strong></div>
            <div><span>Total Bayar:</span> <strong>Rp <?= number_format($data['total'], 0, ',', '.') ?></strong></div>
            <div><span>Metode:</span> <strong><?= $data['metode_pembayaran'] ?></strong></div>
            <div><span>Status:</span> <strong style="color: #d97706;"><?= ucfirst($data['status']) ?></strong></div>
        </div>

        <div class="upload-section">
            <form action="proses_upload_bukti.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_transaksi" value="<?= $data['id'] ?>">
                <label class="upload-label"><i class="fa-solid fa-camera"></i> Upload Bukti Transfer</label>
                <input type="file" name="bukti_transfer" class="input-file" accept="image/*" required>
                <button type="submit" class="btn-upload">Kirim Bukti Pembayaran</button>
            </form>
        </div>

        <p style="font-size: 13px; margin-top: 20px;">Silakan simpan ID Transaksi Anda jika sewaktu-waktu diperlukan.</p>

        <a href="home.php" class="btn-home">Kembali ke Beranda</a>
    </div>

</body>

</html>