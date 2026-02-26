<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$view_all = isset($_GET['view']) && $_GET['view'] == 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$total_item_keranjang = 0;
if (isset($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $qty) {
        $total_item_keranjang += (int)$qty;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartix | Official Gadget Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: #2563EB;
            --dark: #0F172A;
            --slate: #64748B;
            --bg: #F8FAFC;
            --white: #ffffff;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            padding-top: 85px;
            color: var(--dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            height: 75px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0 5%;
        }

        .logo img {
            height: 40px;
        }

        .search-bar {
            position: relative;
            flex: 0.4;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 20px 12px 48px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            outline: none;
        }

        .search-bar i {
            position: absolute;
            left: 18px;
            top: 15px;
            color: var(--slate);
        }

        .nav-icons {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-link {
            color: var(--dark);
            font-size: 22px;
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            flex: 1;
            width: 100%;
        }

        /* HERO SLIDER */
        .hero-section {
            margin-bottom: 50px;
            border-radius: 24px;
            overflow: hidden;
            height: 450px;
            position: relative;
            background: #eee;
        }

        .hero-slide {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .hero-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.6), transparent);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 60px;
            color: white;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #fff !important;
            background: rgba(0, 0, 0, 0.2);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            backdrop-filter: blur(4px);
        }

        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 18px;
        }

        /* PRODUCT CARDS */
        .grid-products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .p-card {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            border: 1px solid rgba(0, 0, 0, 0.02);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .p-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        }

        .p-card img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .p-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: auto;
        }

        .qty-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-input {
            width: 60px;
            height: 45px;
            border-radius: 10px;
            border: 1.5px solid #E2E8F0;
            text-align: center;
            font-weight: 700;
            outline: none;
        }

        .p-btn {
            flex: 1;
            background: var(--dark);
            color: white;
            border: none;
            border-radius: 12px;
            height: 45px;
            cursor: pointer;
            font-weight: 700;
            transition: var(--transition);
        }

        .p-btn:hover {
            background: var(--primary);
        }

        /* FOOTER HANYA COPYRIGHT */
        .simple-footer {
            padding: 30px 0;
            text-align: center;
            border-top: 1px solid #E2E8F0;
            background: var(--white);
            margin-top: 50px;
        }

        .simple-footer p {
            color: var(--slate);
            font-size: 14px;
            font-weight: 600;
        }

        .section-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background: #EF4444;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 800;
            border: 2px solid var(--white);
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><a href="home.php"><img src="../cartixlogo.png" alt="Cartix"></a></div>
        <form action="home.php" method="GET" class="search-bar">
            <i class="fa fa-search"></i>
            <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
        </form>
        <div class="nav-icons">
            <a href="riwayat.php" class="nav-link" title="Riwayat"><i class="fa-solid fa-clock-rotate-left"></i></a>
            <a href="cart.php" class="nav-link" title="Keranjang">
                <i class="fa-solid fa-bag-shopping"></i>
                <?php if ($total_item_keranjang > 0): ?><span class="badge"><?= $total_item_keranjang ?></span><?php endif; ?>
            </a>
            <button onclick="confirmLogout()" style="background:#FFF1F2; color:#E11D48; border:none; padding:10px 18px; border-radius:12px; font-weight:700; cursor:pointer;">Logout</button>
        </div>
    </nav>

    <main class="container">
        <?php if (empty($search) && !$view_all): ?>
            <div class="swiper mySwiper hero-section">
                <div class="swiper-wrapper">
                    <div class="swiper-slide hero-slide">
                        <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=1200" alt="Banner">
                        <div class="hero-content">
                            <h1 style="font-size: 40px; font-weight: 800;">Exclusive Tech Deals</h1>
                            <p>Dapatkan perangkat terbaik dengan harga spesial.</p>
                        </div>
                    </div>
                    <div class="swiper-slide hero-slide">
                        <img src="https://images.unsplash.com/photo-1468495244123-6c6c332eeece?q=80&w=1200" alt="Banner">
                        <div class="hero-content">
                            <h1 style="font-size: 40px; font-weight: 800;">Modern Lifestyle</h1>
                            <p>Inovasi gadget untuk mendukung produktivitasmu.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        <?php endif; ?>

        <div class="section-title">
            <span><?= !empty($search) ? "Hasil Cari: '$search'" : ($view_all ? "Semua Produk" : "Rekomendasi Produk") ?></span>
            <?php if (!$view_all && empty($search)): ?>
                <a href="?view=all" style="color:var(--primary); text-decoration:none; font-size:14px; font-weight:700;">Lihat Semua</a>
            <?php else: ?>
                <a href="home.php" style="color:var(--slate); text-decoration:none; font-size:14px;">Kembali</a>
            <?php endif; ?>
        </div>

        <div class="grid-products">
            <?php
            if (!empty($search)) {
                $sql = "SELECT * FROM produk WHERE nama LIKE '%$search%'";
            } elseif ($view_all) {
                $sql = "SELECT * FROM produk";
            } else {
                $sql = "SELECT * FROM produk LIMIT 4";
            }

            $query = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($query)): ?>
                <div class="p-card">
                    <img src="<?= $row['foto'] ?>" onerror="this.src='https://via.placeholder.com/200'">
                    <div style="font-weight:700; margin-bottom:5px;"><?= $row['nama'] ?></div>
                    <div style="font-size: 20px; font-weight: 800; color: var(--primary); margin-bottom:15px;">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                    <form action="keranjang_tambah.php" method="POST" class="p-form">
                        <input type="hidden" name="id_produk" value="<?= $row['id'] ?>">
                        <div class="qty-wrapper">
                            <input type="number" name="qty" value="1" min="1" class="qty-input">
                            <button type="submit" class="p-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <footer class="simple-footer">
        <div class="container">
            <p>&copy; 2026 Cartix Official Gadget Store. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 4000
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
        });

        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Keluar'
            }).then((res) => {
                if (res.isConfirmed) window.location.href = 'logout_user.php';
            });
        }
    </script>
</body>

</html>