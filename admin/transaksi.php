<?php
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

/* ======================
    UPDATE STATUS TRANSAKSI
====================== */
if (isset($_POST['update_status'])) {
    $id     = intval($_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Pastikan nama kolom 'status_pesanan' atau 'status' sesuai dengan database kamu
    // Berdasarkan query riwayat kamu, saya gunakan 'status_pesanan'
    mysqli_query($conn, "UPDATE pesanan SET status_pesanan='$status' WHERE id=$id");

    header("Location: transaksi.php");
    exit;
}

/* ======================
    HAPUS TRANSAKSI
====================== */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM pesanan WHERE id=$id");
    header("Location: transaksi.php");
    exit;
}

/* ======================
    DATA TRANSAKSI (JOIN DENGAN TABEL USERS)
====================== */
$dataTransaksi = mysqli_query($conn, "SELECT pesanan.*, users.nama AS nama_akun 
     FROM pesanan 
     LEFT JOIN users ON pesanan.user_id = users.id 
     ORDER BY pesanan.id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Transaksi | Admin</title>
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

        .pending {
            background: #f59e0b
        }

        .diproses {
            background: #2563eb
        }

        .dikirim {
            background: #8b5cf6
        }

        .selesai {
            background: #16a34a
        }

        .batal {
            background: #dc2626
        }

        select,
        button {
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background: #2563eb;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .action.hapus {
            background: #dc2626;
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
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
        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>ADMIN PANEL</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Kelola User</a>
            <a href="petugas.php">Kelola Petugas</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="transaksi.php" class="active">Transaksi</a>
            <a href="laporan_transaksi.php"> Laporan Transaksi</a>
            <a href="laporan_penjualan.php">Laporan Penjualan</a>
            <a href="laporan_stok.php">Laporan Stok</a>
            <a href="backup.php">Backup & Restore</a>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                Logout
            </a>
        </div>
        <div class="content">
            <h1>Kelola Transaksi</h1>
            <table>
                <tr>
                    <th>No</th>
                    <th>ID Pesanan</th>
                    <th>Nama Akun</th>
                    <th>Total</th>
                    <th>Status Saat Ini</th>
                    <th>Aksi Ganti Status</th>
                </tr>
                <?php $no = 1;
                while ($t = mysqli_fetch_assoc($dataTransaksi)) :
                    $st_class = strtolower(str_replace(' ', '', $t['status_pesanan']));
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong>#CTX-<?= str_pad($t['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                        <td><?= htmlspecialchars($t['nama_akun'] ?? 'User Terhapus') ?></td>
                        <td>Rp <?= number_format($t['total_bayar']) ?></td>
                        <td>
                            <span class="status <?= $st_class ?>">
                                <?= strtoupper($t['status_pesanan']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" style="display:inline">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <select name="status">
                                    <option value="Pending" <?= $t['status_pesanan'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Diproses" <?= $t['status_pesanan'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="Dikirim" <?= $t['status_pesanan'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                    <option value="Selesai" <?= $t['status_pesanan'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="Batal" <?= $t['status_pesanan'] == 'Batal' ? 'selected' : '' ?>>Batal</option>
                                </select>
                                <button name="update_status">Update</button>
                            </form>
                            <a class="action hapus" href="?hapus=<?= $t['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>

</html>