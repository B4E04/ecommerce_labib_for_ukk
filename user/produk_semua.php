<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semua Produk | Cartix</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="home.php" class="text-blue-600 hover:text-blue-800">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Semua Produk</h1>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php
            // Query tanpa LIMIT agar muncul semua
            $query = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");

            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)):
                    $namaProduk = $row['nama_produk'] ?? 'Produk';
                    $fotoProduk = $row['foto'] ?? '';
                    $hargaProduk = $row['harga'] ?? 0;
            ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition">
                    <img src="<?= $fotoProduk ?>" alt="<?= $namaProduk ?>" class="w-full h-48 object-contain mb-4">
                    <h4 class="font-semibold text-gray-800 h-12 overflow-hidden"><?= htmlspecialchars($namaProduk) ?></h4>
                    <div class="text-blue-600 font-bold text-lg my-3">
                        Rp <?= number_format($hargaProduk, 0, ',', '.') ?>
                    </div>
                    <button class="w-full bg-blue-600 text-white py-2 rounded-xl font-medium hover:bg-blue-700 transition">
                        Beli Sekarang
                    </button>
                </div>
            <?php 
                endwhile; 
            } else {
                echo "<p class='col-span-full text-center text-gray-500'>Belum ada produk.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>