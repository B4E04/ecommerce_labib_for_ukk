<?php
// ====== LOGIKA DATABASE (SINKRON) ======
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

// 1. Total User (Sinkron dengan menu Kelola User)
$resUser = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$dataUser = mysqli_fetch_assoc($resUser);
$totalUser = $dataUser['total'] ?? 0;

// 2. Total Produk (Sinkron dengan tabel produk)
$resProduk = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk");
$dataProduk = mysqli_fetch_assoc($resProduk);
$totalProduk = $dataProduk['total'] ?? 0;

// 3. Total Payment (Pendapatan dari pesanan yang 'Selesai')
$resPayment = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM pesanan WHERE status_pesanan = 'Selesai'");
$dataPayment = mysqli_fetch_assoc($resPayment);
$totalPaymentNominal = $dataPayment['total'] ?? 0;

// FORMAT LOGIKA: Menambahkan Rp dan pemisah ribuan (Contoh: Rp 1.500.000)
$totalPayment = "Rp " . number_format($totalPaymentNominal, 0, ',', '.');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f5f7fb;
        }

        /* ===== LAYOUT ===== */
        .wrapper {
            display: flex;
            min-height: 100vh;
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

        /* ===== WELCOME ===== */
        .welcome {
            text-align: center;
            margin: 40px 0;
        }

        .welcome h2 {
            font-size: 40px;
            font-weight: 725;
            font-family: 'Inter', sans-serif;
        }

        .welcome span {
            color: #2563eb;
        }

        /* ===== GRID ===== */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        /* ===== CARD ===== */
        .card {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }

        .card h3 {
            font-size: 16px;
            color: #111827;
        }

        .value {
            font-size: 34px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 10px;
        }

        .percent {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 10px;
        }

        .card-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 120px;
        }

        .chart-container {
            width: 130px;
            height: 70px;
        }

        .percent {
            font-size: 28px;
            font-weight: 800;
            color: #2563eb;
            margin-top: 5px;
        }

        .sidebar a.logout-btn {
            color: #ef4444;
            font-weight: 600;
            margin-top: 20px;
            border: 1px solid transparent;
        }

        .sidebar a.logout-btn:hover {
            background: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <div class="sidebar">
            <h2> ADMIN PANEL</h2>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="user.php">Kelola User</a>
            <a href="petugas.php">Kelola Petugas</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="transaksi.php">Transaksi</a>
            <a href="laporan_transaksi.php"> Laporan Transaksi</a>
            <a href="laporan_penjualan.php">Laporan Penjualan</a>
            <a href="laporan_stok.php">Laporan Stok</a>
            <a href="backup_restore.php">Backup & Restore</a>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                Logout
            </a>
        </div>

        <div class="content">

            <div class="welcome">
                <h2>Selamat Datang, <span>Admin</span></h2>
            </div>

            <div class="grid-3">
                <div class="card">
                    <h3>Total User</h3>
                    <div class="value"><?= $totalUser ?></div>
                </div>

                <div class="card">
                    <h3>Total Produk</h3>
                    <div class="value"><?= $totalProduk ?></div>
                </div>

                <div class="card">
                    <h3>Total Penghasilan</h3>
                    <div class="value"><?= $totalPayment ?></div>
                </div>
            </div>

            <div class="grid-2">
                <div class="card card-flex">
                    <div>
                        <h3>Pesanan</h3>
                        <div class="percent" id="txt-pesanan">+0 %</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartPesanan"></canvas>
                    </div>
                </div>

                <div class="card card-flex">
                    <div>
                        <h3>Pengunjung</h3>
                        <div class="percent" id="txt-pengunjung">+0 %</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartPengunjung"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                },
                elements: {
                    line: {
                        tension: 0.4
                    },
                    point: {
                        radius: 0
                    }
                }
            };

            const ctxPesanan = document.getElementById('chartPesanan').getContext('2d');
            const chartPesanan = new Chart(ctxPesanan, {
                type: 'line',
                data: {
                    labels: [1, 2, 3, 4, 5, 6],
                    datasets: [{
                        data: [0, 0, 0, 0, 0, 0],
                        borderColor: '#22c55e',
                        borderWidth: 3,
                        fill: false,
                        pointRadius: [0, 0, 0, 0, 0, 5],
                        pointBackgroundColor: '#22c55e'
                    }]
                },
                options: chartOptions
            });

            const ctxPengunjung = document.getElementById('chartPengunjung').getContext('2d');
            const chartPengunjung = new Chart(ctxPengunjung, {
                type: 'line',
                data: {
                    labels: [1, 2, 3, 4, 5, 6],
                    datasets: [{
                        data: [0, 0, 0, 0, 0, 0],
                        borderColor: '#f59e0b',
                        borderWidth: 3,
                        fill: false,
                        pointRadius: [0, 0, 0, 0, 0, 5],
                        pointBackgroundColor: '#f59e0b'
                    }]
                },
                options: chartOptions
            });

            function updateStats() {
                fetch('api_data.php')
                    .then(res => res.json())
                    .then(data => {
                        const p = data.graph_pesanan;
                        const lastP = p[p.length - 1] || 0;
                        const prevP = p[p.length - 2] || 1;
                        const diffP = (((lastP - prevP) / prevP) * 100).toFixed(1);
                        document.getElementById('txt-pesanan').style.color = diffP >= 0 ? "#2563eb" : "#ef4444";
                        const signP = diffP >= 0 ? "+" : "";
                        document.getElementById('txt-pesanan').innerText = `${signP}${diffP} %`;

                        const v = data.graph_pengunjung;
                        const lastV = v[v.length - 1] || 0;
                        const prevV = v[v.length - 2] || 1;
                        const diffV = (((lastV - prevV) / prevV) * 100).toFixed(1);
                        const signV = diffV >= 0 ? "+" : "";
                        document.getElementById('txt-pengunjung').innerText = `${signV}${diffV} %`;

                        chartPesanan.data.datasets[0].data = data.graph_pesanan;
                        chartPesanan.update();

                        chartPengunjung.data.datasets[0].data = data.graph_pengunjung;
                        chartPengunjung.update();
                    })
                    .catch(err => console.log("Menunggu data..."));
            }

            updateStats();
        </script>
    </div>
</body>
</html>