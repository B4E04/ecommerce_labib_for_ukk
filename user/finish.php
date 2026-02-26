<?php
session_start();
include "../config/database.php";

// Pastikan ada ID pesanan yang baru diproses
if (!isset($_SESSION['last_order_id'])) {
    header("Location: home.php");
    exit;
}

$pesanan_id = $_SESSION['last_order_id'];

// Ambil data pesanan untuk ditampilkan
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = '$pesanan_id'");
$data = mysqli_fetch_assoc($query);

// Logika instruksi berdasarkan metode bayar
$instruksi = "";
$norek = "";

switch ($data['metode_bayar']) {
    case 'BCA': $norek = "8830 1234 5678"; $instruksi = "Transfer via m-BCA atau ATM BCA ke nomor Virtual Account di atas."; break;
    case 'MANDIRI': $norek = "123 000 987 654"; $instruksi = "Pilih menu Bayar/Beli > Multipayment pada aplikasi Livin' Mandiri."; break;
    case 'DANA': $norek = "0812-3456-7890"; $instruksi = "Buka aplikasi DANA, pilih 'Kirim', lalu masukkan nomor telepon di atas."; break;
    case 'COD': $norek = "Bayar di Tempat"; $instruksi = "Siapkan uang tunai pas saat kurir mengantarkan paket ke alamat Anda."; break;
    default: $norek = "Sistem Otomatis"; $instruksi = "Silakan cek email atau WhatsApp Anda untuk langkah pembayaran selanjutnya."; break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil | Cartix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2563EB; --bg: #F9FAFB; --white: #ffffff; --success: #10B981; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
        
        .finish-card { background: var(--white); max-width: 500px; width: 100%; padding: 40px; border-radius: 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); text-align: center; }
        
        .icon-success { width: 80px; height: 80px; background: #ECFDF5; color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 25px; }
        
        h1 { font-size: 24px; font-weight: 800; color: #1F2937; margin-bottom: 10px; }
        .order-id { font-size: 14px; color: #6B7280; margin-bottom: 30px; }
        .order-id span { color: var(--primary); font-weight: 700; }

        .payment-box { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 20px; padding: 25px; margin-bottom: 30px; }
        .method-name { font-size: 13px; color: #6B7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; font-weight: 600; }
        .account-number { font-size: 22px; font-weight: 800; color: #1F2937; margin-bottom: 5px; letter-spacing: 1px; }
        .total-amount { font-size: 18px; color: var(--primary); font-weight: 700; margin-top: 15px; padding-top: 15px; border-top: 1px solid #E2E8F0; }

        .instruction { font-size: 13px; color: #6B7280; line-height: 1.6; padding: 0 10px; }
        
        .btn-group { display: flex; flex-direction: column; gap: 12px; }
        .btn { padding: 16px; border-radius: 16px; font-weight: 700; text-decoration: none; transition: 0.3s; font-size: 15px; }
        .btn-home { background: var(--primary); color: white; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4); }
        .btn-history { background: #F3F4F6; color: #4B5563; }
        .btn-history:hover { background: #E5E7EB; }

        .confetti { font-size: 12px; color: #9CA3AF; margin-top: 25px; }
    </style>
</head>
<body>

<div class="finish-card">
    <div class="icon-success">
        <i class="fa-solid fa-check"></i>
    </div>
    
    <h1>Pesanan Berhasil!</h1>
    <p class="order-id">ID Pesanan: <span>#CTX-<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT) ?></span></p>

    <div class="payment-box">
        <p class="method-name">Pembayaran via <?= $data['metode_bayar'] ?></p>
        <div class="account-number"><?= $norek ?></div>
        <div class="instruction">
            <i class="fa-solid fa-circle-info" style="color: #3B82F6;"></i> <?= $instruksi ?>
        </div>
        <div class="total-amount">
            Total: Rp <?= number_format($data['total_bayar'], 0, ',', '.') ?>
        </div>
    </div>

    <div class="btn-group">
        <a href="home.php" class="btn btn-home">Kembali ke Beranda</a>
        <a href="riwayat.php" class="btn btn-history">Cek Status Pesanan</a>
    </div>

    <p class="confetti">Terima kasih telah berbelanja di Cartix ✨</p>
</div>

<?php 
// Opsional: Hapus ID pesanan dari session setelah ditampilkan sekali
// unset($_SESSION['last_order_id']); 
?>

</body>
</html>