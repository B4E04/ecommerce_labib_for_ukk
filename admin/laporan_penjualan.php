<?php
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

/* ======================
    FILTER TANGGAL
====================== */
$dari   = isset($_GET['dari']) ? $_GET['dari'] : '';
$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : '';

// Filter hanya status 'Selesai' untuk laporan penjualan
$where_clause = "WHERE pesanan.status_pesanan = 'Selesai'";
if ($dari && $sampai) {
    $where_clause .= " AND DATE(pesanan.tanggal_pesan) BETWEEN '$dari' AND '$sampai'";
}

/* ======================
    DATA PENJUALAN
====================== */
$dataPenjualan = mysqli_query($conn, "SELECT pesanan.*, users.nama AS nama_akun 
     FROM pesanan 
     LEFT JOIN users ON pesanan.user_id = users.id 
     $where_clause
     ORDER BY pesanan.id DESC");

/* ======================
    TOTAL PENDAPATAN
====================== */
$totalUang = mysqli_query($conn, "SELECT SUM(total_bayar) AS total FROM pesanan $where_clause");
$resUang = mysqli_fetch_assoc($totalUang);
$pendapatan = $resUang['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan | Admin</title>
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

        /* ===== SIDEBAR ===== */
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar h2 img {
            width: 30px;
            height: 30px;
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


        /* ===== CONTENT ===== */
        .content {
            flex: 1;
            padding: 40px;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 20px;
        }

        .content {
            flex: 1;
            padding: 40px
        }

        .card-filter {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
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

        .total-box {
            margin-bottom: 25px;
            font-size: 18px;
            font-weight: bold;
        }

        .total-box span {
            color: #16a34a;
            font-size: 22px;
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
            <h2>ADMIN PANEL</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Kelola User</a>
            <a href="petugas.php">Kelola Petugas</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="transaksi.php">Transaksi</a>
            <a href="laporan_transaksi.php">Laporan Transaksi</a>
            <a href="laporan_penjualan.php" class="active">Laporan Penjualan</a>
            <a href="laporan_stok.php">Laporan Stok</a>
            <a href="backup_restore.php">Backup & Restore</a>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                Logout
            </a>
        </div>
        <div class="content">
            <h1>Laporan Penjualan</h1>

            <div class="card-filter">
                <form method="get">
                    <label>Periode:</label>
                    <input type="date" name="dari" value="<?= $dari ?>" required>
                    <span> s/d </span>
                    <input type="date" name="sampai" value="<?= $sampai ?>" required>
                    <button type="submit">Filter</button>
                    <a href="laporan_penjualan.php" style="margin-left:10px; color:gray; text-decoration:none; font-size:12px;">Reset</a>
                </form>
            </div>

            <div class="total-box">Total Omzet (Selesai): <span>Rp <?= number_format($pendapatan) ?></span></div>

            <table>
                <tr>
                    <th>No</th>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Total Bayar</th>
                    <th>Tanggal Pesan</th>
                </tr>
                <?php $no = 1;
                while ($p = mysqli_fetch_assoc($dataPenjualan)) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong>#CTX-<?= $p['id'] ?></strong></td>
                        <td><?= htmlspecialchars($p['nama_penerima'] ?? $p['nama_akun']) ?></td>
                        <td>Rp <?= number_format($p['total_bayar']) ?></td>
                        <td>
                            <?php
                            // PERBAIKAN DI SINI:
                            // Pastikan variabel mengambil 'tanggal_pesan' sesuai struktur SQL kamu
                            if (!empty($p['tanggal_pesan']) && $p['tanggal_pesan'] != '0000-00-00 00:00:00') {
                                echo date('d-m-Y H:i', strtotime($p['tanggal_pesan']));
                            } else {
                                echo "Belum ada tanggal";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($dataPenjualan) == 0): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:gray;">Data penjualan tidak ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>

</html>