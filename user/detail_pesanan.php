<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$id_pesanan = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data pesanan (Pastikan hanya milik user yang login)
$query_pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = '$id_pesanan' AND user_id = '$user_id'");
$pesanan = mysqli_fetch_assoc($query_pesanan);

if (!$pesanan) {
    echo "Pesanan tidak ditemukan atau Anda tidak memiliki akses.";
    exit;
}

// Ambil detail produk dalam pesanan tersebut
$query_detail = mysqli_query($conn, "SELECT pd.*, p.nama, p.foto 
                                     FROM pesanan_detail pd 
                                     JOIN produk p ON pd.produk_id = p.id 
                                     WHERE pd.pesanan_id = '$id_pesanan'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= $id_pesanan ?> | Cartix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2563EB; --bg: #F9FAFB; --white: #ffffff; --dark: #1F2937; --gray: #6B7280; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--dark); padding: 40px 5%; margin: 0; }
        .container { max-width: 800px; margin: 0 auto; }
        
        .card { background: var(--white); border-radius: 20px; padding: 30px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #F1F5F9; }
        
        .status-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .badge { padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-success { background: #D1FAE5; color: #065F46; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; border-top: 1px solid #F1F5F9; padding-top: 20px; }
        .info-item label { display: block; font-size: 12px; color: var(--gray); text-transform: uppercase; font-weight: 600; margin-bottom: 5px; }
        .info-item p { margin: 0; font-size: 15px; font-weight: 600; }

        .product-item { display: flex; align-items: center; gap: 15px; padding: 15px 0; border-bottom: 1px solid #F8FAFC; }
        .product-item:last-child { border-bottom: none; }
        .product-img { width: 70px; height: 70px; object-fit: contain; background: #F8FAFC; border-radius: 12px; border: 1px solid #eee; }
        .product-info { flex: 1; }
        .product-info h4 { margin: 0 0 5px; font-size: 16px; }
        .product-info p { margin: 0; color: var(--gray); font-size: 14px; }

        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 15px; }
        .total-row { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px dashed #F1F5F9; font-size: 20px; font-weight: 800; color: var(--primary); }
        
        .btn-back { display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: var(--gray); font-weight: 600; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <a href="riwayat.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat</a>

    <div class="card">
        <div class="status-header">
            <div>
                <h2 style="margin: 0;">Detail Pesanan</h2>
                <p style="color: var(--gray); margin: 5px 0 0;">#CTX-<?= str_pad($pesanan['id'], 5, '0', STR_PAD_LEFT) ?></p>
            </div>
            <span class="badge badge-<?= strtolower($pesanan['status_pesanan']) ?>">
                <?= $pesanan['status_pesanan'] ?>
            </span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>Tanggal Pemesanan</label>
                <p><?= date('d F Y, H:i', strtotime($pesanan['tanggal_pesan'])) ?></p>
            </div>
            <div class="info-item">
                <label>Metode Pembayaran</label>
                <p><?= $pesanan['metode_bayar'] ?></p>
            </div>
            <div class="info-item" style="grid-column: span 2;">
                <label>Alamat Pengiriman</label>
                <p><?= $pesanan['nama_penerima'] ?> (<?= $pesanan['hp'] ?>)<br>
                <span style="font-weight: 400; color: var(--gray);"><?= $pesanan['alamat'] ?></span></p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 18px;">Produk Dipilih</h3>
        <?php while($item = mysqli_fetch_assoc($query_detail)): ?>
        <div class="product-item">
            <img src="<?= $item['foto'] ?>" class="product-img" onerror="this.src='https://via.placeholder.com/70'">
            <div class="product-info">
                <h4><?= htmlspecialchars($item['nama']) ?></h4>
                <p><?= $item['qty'] ?> barang x Rp <?= number_format($item['harga_saat_ini'], 0, ',', '.') ?></p>
            </div>
            <div style="font-weight: 700;">
                Rp <?= number_format($item['harga_saat_ini'] * $item['qty'], 0, ',', '.') ?>
            </div>
        </div>
        <?php endwhile; ?>

        <div style="margin-top: 30px;">
            <div class="summary-row">
                <span>Subtotal Produk</span>
                <span>Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></span>
            </div>
            <div class="summary-row">
                <span>Biaya Pengiriman</span>
                <span style="color: #10B981; font-weight: 600;">Gratis</span>
            </div>
            <div class="total-row">
                <span>Total Bayar</span>
                <span>Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
</div>

</body>
</html>