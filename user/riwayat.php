<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Query JOIN untuk mengambil data pesanan
$sql = "SELECT p.*, 
        GROUP_CONCAT(pr.foto SEPARATOR '|||') as foto_produk,
        GROUP_CONCAT(pr.nama SEPARATOR '|||') as nama_produk
        FROM pesanan p
        JOIN pesanan_detail pd ON p.id = pd.pesanan_id
        JOIN produk pr ON pd.produk_id = pr.id
        WHERE p.user_id = '$user_id'
        GROUP BY p.id
        ORDER BY p.tanggal_pesan DESC";

$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan | Cartix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --bg: #F9FAFB;
            --white: #ffffff;
            --dark: #1F2937;
            --gray: #6B7280;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--dark);
            margin: 0;
            padding: 0;
        }

        .top-nav {
            position: fixed;
            top: 25px;
            left: 0;
            right: 0;
            padding: 0 25px;
            display: flex;
            justify-content: space-between;
            z-index: 100;
        }

        .btn-nav-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: var(--white);
            color: var(--dark);
            text-decoration: none;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: 0.3s;
            border: 1px solid #EDF2F7;
            pointer-events: auto;
        }

        .btn-nav-circle:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 100px 5% 40px;
        }

        .header {
            margin-bottom: 35px;
            text-align: center;
        }

        .order-card {
            background: var(--white);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 20px;
            border: 1px solid #EDF2F7;
            transition: 0.3s;
        }

        .order-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 18px;
            border-bottom: 1px solid #F1F5F9;
            margin-bottom: 18px;
        }

        /* Badge Status Styles */
        .badge {
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-pending {
            background: #FFFBEB;
            color: #92400E;
            border: 1px solid #FEF3C7;
        }

        .status-diproses {
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #DBEAFE;
        }

        .status-dikirim {
            background: #F5F3FF;
            color: #5B21B6;
            border: 1px solid #EDE9FE;
        }

        .status-selesai {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #D1FAE5;
        }

        .status-batal {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FEE2E2;
        }

        .order-content {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .img-thumb {
            width: 70px;
            height: 70px;
            object-fit: contain;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
        }

        .total-price {
            font-size: 19px;
            font-weight: 800;
            color: var(--primary);
        }

        .btn-detail {
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <div class="top-nav">
        <a href="home.php" class="btn-nav-circle"><i class="fa-solid fa-chevron-left"></i></a>
        <a href="home.php" class="btn-nav-circle"><i class="fa-solid fa-house"></i></a>
    </div>

    <div class="container">
        <div class="header">
            <h2>Pesanan Saya</h2>
            <p>Lacak pengiriman belanja Anda</p>
        </div>

        <?php if (mysqli_num_rows($query) == 0): ?>
            <p style="text-align:center; color:var(--gray);">Belum ada pesanan.</p>
        <?php else: ?>
            <?php while ($row = mysqli_fetch_assoc($query)):
                $fotos = explode('|||', $row['foto_produk']);
                $namas = explode('|||', $row['nama_produk']);
                $jumlah_item = count($fotos);
                $status_class = strtolower($row['status_pesanan']);
                ?>
                <div class="order-card">
                    <div class="order-head">
                        <div style="font-size:13px; color:var(--gray);">
                            <?= date('d M Y', strtotime($row['tanggal_pesan'])) ?> | <b>#CTX-<?= $row['id'] ?></b>
                        </div>
                        <span class="badge status-<?= $status_class ?>">
                            <?= $row['status_pesanan'] ?>
                        </span>
                    </div>

                    <div class="order-content">
                        <img src="../admin/<?= $fotos[0] ?>" class="img-thumb"
                            onerror="this.src='https://via.placeholder.com/70'">
                        <div style="flex:1;">
                            <h4 style="margin:0;"><?= htmlspecialchars($namas[0]) ?></h4>
                            <p style="font-size:13px; color:var(--gray);"><?= $jumlah_item ?> Produk •
                                <?= $row['metode_bayar'] ?></p>
                        </div>
                    </div>

                    <div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <small style="color:var(--gray);">Total Bayar</small>
                            <div class="total-price">Rp <?= number_format($row['total_bayar']) ?></div>
                        </div>
                        <a href="detail_pesanan.php?id=<?= $row['id'] ?>" class="btn-detail">Rincian</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</body>

</html>