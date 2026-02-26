<?php
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

/* ======================
    FILTER TANGGAL
====================== */
$dari   = isset($_GET['dari']) ? $_GET['dari'] : '';
$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : '';

$where_clause = "";
if ($dari && $sampai) {
    // DISESUAIKAN: Menggunakan kolom 'tanggal_pesan'
    $where_clause = "WHERE DATE(pesanan.tanggal_pesan) BETWEEN '$dari' AND '$sampai'";
}

/* ======================
    DATA TRANSAKSI
====================== */
$dataTransaksi = mysqli_query($conn, "SELECT pesanan.*, users.nama AS nama_akun 
     FROM pesanan 
     LEFT JOIN users ON pesanan.user_id = users.id 
     $where_clause
     ORDER BY pesanan.id DESC");

/* ======================
    TOTAL PENDAPATAN
====================== */
$where_total = "WHERE status_pesanan='Selesai'";
if ($dari && $sampai) {
    $where_total .= " AND DATE(tanggal_pesan) BETWEEN '$dari' AND '$sampai'";
}
$totalQuery = mysqli_query($conn, "SELECT SUM(total_bayar) AS total_rp FROM pesanan $where_total");
$res_total = mysqli_fetch_assoc($totalQuery);
$pendapatan = $res_total['total_rp'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi | Petugas</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif
        }

        body {
            margin: 0;
            background: #f5f7fb
        }

        .wrapper {
            display: flex;
            min-height: 100vh
        }

        .sidebar {
            width: 240px;
            background: #111827;
            color: #fff;
            padding: 25px 20px;
        }

        .sidebar h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            font-size: 15px;
            font-weight: 500;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: #fff;
        }

        .content {
            flex: 1;
            padding: 40px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #1e293b;
            color: #fff;
        }

        .status {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            color: #fff;
            display: inline-block;
            font-weight: bold;
        }

        .selesai {
            background: #16a34a
        }

        .pending {
            background: #f59e0b
        }

        .diproses {
            background: #2563eb
        }

        .dikirim {
            background: #8b5cf6
        }

        .batal {
            background: #dc2626
        }

        .total-box {
            margin-bottom: 25px;
            font-size: 18px;
            font-weight: bold;
        }

        .total-box span {
            color: #16a34a;
            font-size: 22px;
        }

        .card-filter {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        input[type="date"],
        button {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background: #2563eb;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            padding: 8px 20px;
        }
        /* Styling khusus untuk link logout */
    .sidebar a.logout-btn {
      color: #ef4444;
      /* Warna merah (Tailwind Red 500) */
      font-weight: 600;
      margin-top: 20px;
      /* Memberi jarak agar terpisah dari menu utama */
      border: 1px solid transparent;
    }

    /* Efek saat mouse diarahkan ke tombol */
    .sidebar a.logout-btn:hover {
      background: #fee2e2;
      /* Background merah sangat muda */
      color: #b91c1c;
      /* Teks merah lebih gelap */
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="sidebar">
            <h2>PETUGAS PANEL</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Kelola User</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="transaksi.php">Transaksi</a>
            <a href="laporan_transaksi.php" class="active">Laporan Transaksi</a>
            <a href="laporan_penjualan.php">Laporan Penjualan</a>
            <a href="laporan_stok.php">Laporan Stok</a>
            <a href="backup_restore.php">Backup & Restore</a>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                Logout
            </a>
        </div>

        <div class="content">
            <h1>Laporan Transaksi</h1>
            <div class="card-filter">
                <form method="get">
                    <input type="date" name="dari" value="<?= $dari ?>" required>
                    <span> s/d </span>
                    <input type="date" name="sampai" value="<?= $sampai ?>" required>
                    <button type="submit">Filter Laporan</button>
                    <a href="laporan_transaksi.php" style="margin-left:10px; text-decoration:none; color:gray; font-size:12px;">Reset</a>
                </form>
            </div>

            <div class="total-box">Total Pendapatan (Selesai): <span>Rp <?= number_format($pendapatan) ?></span></div>

            <table>
                <tr>
                    <th>No</th>
                    <th>ID Pesanan</th>
                    <th>Nama Akun</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
                <?php $no = 1;
                while ($t = mysqli_fetch_assoc($dataTransaksi)) :
                    $st_class = strtolower(str_replace(' ', '', $t['status_pesanan'] ?? 'pending'));
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong>#CTX-<?= $t['id'] ?></strong></td>
                        <td><?= htmlspecialchars($t['nama_akun'] ?? 'User Terhapus') ?></td>
                        <td>Rp <?= number_format($t['total_bayar']) ?></td>
                        <td><span class="status <?= $st_class ?>"><?= strtoupper($t['status_pesanan']) ?></span></td>
                        <td>
                            <?php
                            // MENGGUNAKAN: tanggal_pesan
                            if (!empty($t['tanggal_pesan'])) {
                                echo date('d-m-Y', strtotime($t['tanggal_pesan']));
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>

</html>