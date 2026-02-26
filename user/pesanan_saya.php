<?php
session_start();
include "../config/database.php";

// 1. Keamanan: Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. Ambil data transaksi milik user (Urutkan dari yang terbaru)
$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id = '$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya | Cartix</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #1f2937;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header & Navigasi */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .btn-back {
            text-decoration: none;
            color: #4b5563;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-back:hover {
            color: #2563eb;
        }

        h2 { margin: 0; font-size: 24px; }

        /* Card Pesanan */
        .card-pesanan {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            border-left: 5px solid #e5e7eb;
            transition: 0.2s;
        }

        .card-pesanan:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .info-left { flex: 1; min-width: 200px; }
        
        .id-label {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .id-value {
            display: block;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .total-price {
            font-size: 20px;
            font-weight: 800;
            color: #10b981;
        }

        .payment-method {
            font-size: 13px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 10px;
        }

        .status-pending { background: #f3f4f6; color: #4b5563; } /* Default */
        .status-menunggu { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; } /* Menunggu Verifikasi */
        .status-diproses { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; } /* Diproses */
        .status-dikirim { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; } /* Dikirim */

        .action-right {
            text-align: right;
            min-width: 150px;
        }

        /* Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-view { background: #f3f4f6; color: #374151; }
        .btn-view:hover { background: #e5e7eb; }

        .btn-upload { background: #ef4444; color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); }
        .btn-upload:hover { background: #dc2626; }

        .empty-state {
            text-align: center;
            background: white;
            padding: 60px 20px;
            border-radius: 20px;
            margin-top: 40px;
        }

        @media (max-width: 600px) {
            .card-pesanan { flex-direction: column; align-items: flex-start; gap: 20px; }
            .action-right { text-align: left; width: 100%; }
            .btn-action { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h2><i class="fa-solid fa-receipt" style="color: #2563eb;"></i> Pesanan Saya</h2>
        <a href="home.php" class="btn-back">
            <i class="fa-solid fa-house"></i> Beranda
        </a>
    </div>

    <?php if (mysqli_num_rows($query) > 0) : ?>
        <?php while ($row = mysqli_fetch_assoc($query)) : 
            // Logika Warna Status
            $status_raw = $row['status'];
            $status_class = 'status-pending';
            
            if ($status_raw == 'menunggu verifikasi') {
                $status_class = 'status-menunggu';
                $border_color = '#d97706';
            } elseif ($status_raw == 'diproses') {
                $status_class = 'status-diproses';
                $border_color = '#2563eb';
            } elseif ($status_raw == 'dikirim') {
                $status_class = 'status-dikirim';
                $border_color = '#16a34a';
            } else {
                $border_color = '#e5e7eb';
            }
        ?>
            <div class="card-pesanan" style="border-left-color: <?= $border_color ?>;">
                <div class="info-left">
                    <span class="id-label">ID Transaksi</span>
                    <span class="id-value">#<?= $row['id'] ?></span>
                    <div class="total-price">Rp <?= number_format($row['total'], 0, ',', '.') ?></div>
                    <div class="payment-method">
                        <i class="fa-solid fa-wallet"></i> <?= strtoupper($row['metode_pembayaran']) ?>
                    </div>
                </div>

                <div class="action-right">
                    <div class="status-badge <?= $status_class ?>">
                        <i class="fa-solid fa-circle-dot"></i> <?= ucfirst($status_raw) ?>
                    </div>
                    <br>
                    
                    <?php if (!empty($row['bukti_pembayaran'])) : ?>
                        <a href="../<?= $row['bukti_pembayaran'] ?>" target="_blank" class="btn-action btn-view">
                            <i class="fa-solid fa-image"></i> Lihat Bukti
                        </a>
                    <?php else : ?>
                        <a href="pembayaran_berhasil.php?id=<?= $row['id'] ?>" class="btn-action btn-upload">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Upload Bukti
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>

    <?php else : ?>
        <div class="empty-state">
            <i class="fa-solid fa-box-open" style="font-size: 60px; color: #d1d5db; margin-bottom: 20px;"></i>
            <h3>Belum ada transaksi</h3>
            <p style="color: #6b7280;">Sepertinya kamu belum belanja apa pun hari ini.</p>
            <br>
            <a href="home.php" class="btn-action" style="background: #2563eb; color: white;">Mulai Belanja</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>