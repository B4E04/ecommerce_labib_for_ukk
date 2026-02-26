<?php
session_start();
include "../config/database.php";

// Ambil data transaksi
// Gunakan COALESCE atau pengecekan jika tanggal_transaksi belum ada, 
// tapi sebaiknya jalankan SQL di atas dulu.
$query = "SELECT * FROM transaksi ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin - Daftar Pesanan Masuk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #333;
            color: white;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .menunggu {
            background: #fff3cd;
            color: #856404;
        }

        .diterima {
            background: #d4edda;
            color: #155724;
        }

        .ditolak {
            background: #f8d7da;
            color: #721c24;
        }

        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            color: white;
            background: #007bff;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2><i class="fa fa-list"></i> Pesanan Role User</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pembeli</th>
                    <th>HP / Alamat</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_pembeli']) ?></strong></td>
                        <td>
                            <small>WA: <?= $row['hp'] ?? '-' ?></small><br>
                            <small>Alamat: <?= $row['alamat'] ?? '-' ?></small>
                        </td>
                        <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                        <td>
                            <span class="status-badge <?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="admin_detail.php?id=<?= $row['id'] ?>" class="btn">Detail</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>