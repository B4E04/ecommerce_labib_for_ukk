<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

// Logika hapus item
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    unset($_SESSION['keranjang'][$id_hapus]);
    header("Location: cart.php");
    exit;
}

// Logika update qty (dipicu otomatis oleh JS)
if (isset($_POST['update_qty']) || isset($_POST['auto_update'])) {
    foreach ($_POST['qty'] as $id => $jumlah) {
        if ($jumlah <= 0) {
            unset($_SESSION['keranjang'][$id]);
        } else {
            $_SESSION['keranjang'][$id] = $jumlah;
        }
    }
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | Cartix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2563EB; --bg: #F9FAFB; --white: #ffffff; --dark: #1F2937; --gray: #6B7280; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); padding: 40px 5%; color: var(--dark); }
        .cart-container { max-width: 900px; margin: 0 auto; background: var(--white); padding: 30px; border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        
        h2 { margin-bottom: 25px; display: flex; align-items: center; gap: 12px; font-weight: 700; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #F3F4F6; color: var(--gray); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 20px 15px; border-bottom: 1px solid #F3F4F6; }
        
        .product-info { display: flex; align-items: center; gap: 15px; }
        .product-info img { width: 65px; height: 65px; object-fit: contain; border-radius: 12px; background: #f9fafb; border: 1px solid #eee; }
        
        /* Style Input Qty Modern */
        .qty-wrapper { display: flex; align-items: center; gap: 8px; }
        .input-qty {
            width: 55px; padding: 10px; border: 2px solid #E5E7EB; border-radius: 10px;
            text-align: center; font-weight: 600; font-family: inherit; transition: 0.2s;
        }
        .input-qty:focus { border-color: var(--primary); outline: none; background: #f0f7ff; }

        .btn-remove { color: #EF4444; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s; }
        .btn-remove:hover { color: #B91C1C; }
        
        .cart-summary { display: flex; justify-content: space-between; align-items: flex-end; padding-top: 25px; border-top: 2px dashed #F3F4F6; }
        .total-price { font-size: 28px; font-weight: 800; color: var(--primary); margin-top: 5px; }
        
        .actions { display: flex; gap: 12px; }
        .btn-back { padding: 14px 25px; border-radius: 14px; text-decoration: none; color: var(--gray); font-weight: 600; background: #F3F4F6; transition: 0.2s; }
        .btn-back:hover { background: #E5E7EB; color: var(--dark); }
        
        .btn-checkout { padding: 14px 40px; border-radius: 14px; text-decoration: none; color: white; font-weight: 700; background: var(--primary); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3); transition: 0.3s; border: none; }
        .btn-checkout:hover { background: #1D4ED8; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4); }
        
        .empty-state { text-align: center; padding: 60px 0; }
        .empty-state i { font-size: 70px; color: #E5E7EB; margin-bottom: 20px; }
        
        /* Sembunyikan tombol update manual tapi biarkan tetap ada untuk proses backend */
        #btn-manual-update { display: none; }

        /* Animasi loading tipis saat update */
        .updating { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>

<div class="cart-container">
    <h2><i class="fa-solid fa-cart-shopping" style="color: var(--primary);"></i> Keranjang Kamu</h2>

    <?php if (empty($_SESSION['keranjang'])): ?>
        <div class="empty-state">
            <i class="fa-solid fa-basket-shopping"></i>
            <p style="color: var(--gray); font-size: 18px;">Keranjangmu kosong nih.</p>
            <br>
            <a href="home.php" class="btn-checkout">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <form action="cart.php" method="POST" id="cartForm">
            <input type="hidden" name="auto_update" value="1">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th style="width: 100px;">Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_belanja = 0;
                    foreach ($_SESSION['keranjang'] as $id => $jumlah):
                        $res = mysqli_query($conn, "SELECT * FROM produk WHERE id = '$id'");
                        $data = mysqli_fetch_assoc($res);
                        $subtotal = $data['harga'] * $jumlah;
                        $total_belanja += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="<?= $data['foto'] ?>" onerror="this.src='https://via.placeholder.com/65'">
                                <div>
                                    <strong style="display: block; margin-bottom: 4px;"><?= htmlspecialchars($data['nama']) ?></strong>
                                    <span style="font-size: 12px; color: var(--gray);">Tersedia</span>
                                </div>
                            </div>
                        </td>
                        <td style="color: var(--gray);">Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                        <td>
                            <input type="number" 
                                   name="qty[<?= $id ?>]" 
                                   value="<?= $jumlah ?>" 
                                   min="0" 
                                   class="input-qty" 
                                   onchange="autoUpdateCart()">
                        </td>
                        <td><b style="color: var(--dark);">Rp <?= number_format($subtotal, 0, ',', '.') ?></b></td>
                        <td>
                            <a href="cart.php?hapus=<?= $id ?>" class="btn-remove" onclick="return confirm('Hapus produk ini?')">
                                <i class="fa-regular fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div>
                    <span style="color: var(--gray); font-weight: 600; font-size: 14px;">TOTAL PEMBAYARAN</span>
                    <div class="total-price">Rp <?= number_format($total_belanja, 0, ',', '.') ?></div>
                </div>
                <div class="actions">
                    <a href="home.php" class="btn-back">Tambah Produk</a>
                    <a href="checkout.php" class="btn-checkout">Checkout Sekarang</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    function autoUpdateCart() {
        // Tambahkan efek loading visual
        const container = document.querySelector('.cart-container');
        container.classList.add('updating');
        
        // Submit form secara otomatis
        document.getElementById('cartForm').submit();
    }
</script>

</body>
</html>