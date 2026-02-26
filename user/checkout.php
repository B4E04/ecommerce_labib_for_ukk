<?php
session_start();
include "../config/database.php";

// Proteksi Halaman: User harus login dan keranjang tidak boleh kosong
if (!isset($_SESSION['user_id']) || empty($_SESSION['keranjang'])) {
    header("Location: home.php");
    exit;
}

$total_belanja = 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pembayaran | Cartix</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --bg: #F9FAFB;
            --white: #ffffff;
            --dark: #1F2937;
            --gray: #6B7280;
            --light-blue: #EFF6FF;
            --warning-bg: #FFFBEB;
            --warning-border: #FDE68A;
            --warning-text: #92400E;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            padding: 40px 5%;
            color: var(--dark);
            line-height: 1.5;
        }

        .checkout-container {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            align-items: start;
        }

        .card {
            background: var(--white);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #F1F5F9;
            margin-bottom: 25px;
        }

        h3 {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: 1px solid #E5E7EB;
            font-family: inherit;
            font-size: 14px;
            transition: 0.2s;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            border-color: var(--primary);
            outline: none;
            background: #fff;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 12px;
            padding: 20px;
        }

        .payment-item {
            position: relative;
            cursor: pointer;
        }

        .payment-item input {
            position: absolute;
            opacity: 0;
        }

        .payment-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            border: 2px solid #F3F4F6;
            border-radius: 16px;
            text-align: center;
            min-height: 90px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
        }

        .payment-label img,
        .payment-label i {
            height: 22px;
            margin-bottom: 8px;
            object-fit: contain;
            filter: grayscale(100%);
            opacity: 0.5;
            transition: 0.3s;
        }

        .payment-item:hover .payment-label {
            border-color: #BFDBFE;
            background: #F0F7FF;
            box-shadow: 0 10px 15px rgba(37, 99, 235, 0.1);
            transform: translateY(-3px);
        }

        .payment-item:hover .payment-label img,
        .payment-item:hover .payment-label i {
            filter: grayscale(0%);
            opacity: 1;
        }

        .payment-item input:checked+.payment-label {
            border-color: var(--primary);
            background: var(--light-blue);
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.15);
            transform: scale(0.97);
        }

        .payment-item input:checked+.payment-label img,
        .payment-item input:checked+.payment-label i {
            filter: grayscale(0%);
            opacity: 1;
        }

        .payment-accordion {
            border: 1px solid #E5E7EB;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .accordion-header {
            padding: 20px;
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
        }

        .accordion-header.active {
            background: var(--light-blue);
            color: var(--primary);
            border-bottom: 1px solid #E5E7EB;
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .order-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .order-item img {
            width: 55px;
            height: 55px;
            object-fit: contain;
            border-radius: 12px;
            background: #f8f8f8;
            border: 1px solid #eee;
        }

        .estimation-section {
            background: var(--warning-bg);
            border: 1px solid var(--warning-border);
            border-radius: 16px;
            padding: 18px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #E5E7EB;
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
        }

        /* Styling Kebijakan */
        .policy-container {
            margin: 20px 0;
            padding: 15px;
            background: #F8FAFC;
            border-radius: 14px;
            border: 1px solid #EDF2F7;
        }

        .policy-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            cursor: pointer;
            font-size: 12px;
            color: var(--gray);
            line-height: 1.4;
        }

        .policy-item input {
            margin-top: 3px;
            cursor: pointer;
        }

        .policy-item a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .btn-pay {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 18px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            font-size: 16px;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
            transition: 0.3s;
        }

        .btn-pay:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
        }

        .btn-pay:disabled {
            background: #94A3B8;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-back-cart {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 12px;
            padding: 14px;
            color: var(--gray);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 16px;
            transition: 0.3s;
        }

        /* MODAL STYLING */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 24px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            padding: 20px 30px;
            background: #fff;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 18px;
            font-weight: 700;
        }

        .close-modal {
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
            transition: 0.2s;
        }

        .close-modal:hover { color: var(--dark); }

        .modal-body {
            padding: 30px;
            overflow-y: auto;
            font-size: 14px;
            color: var(--dark);
            line-height: 1.6;
        }

        .modal-body h4 { margin: 15px 0 5px 0; color: var(--primary); }

        @media (max-width: 900px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <form action="proses_bayar.php" method="POST" id="checkoutForm">
        <div class="checkout-container">
            <div class="main-content">
                <div class="card">
                    <h3><i class="fa-solid fa-map-location-dot" style="color: var(--primary);"></i> Informasi Pengiriman</h3>
                    <div class="input-group" style="display: grid; gap: 15px;">
                        <input type="text" name="nama_pembeli" value="<?= htmlspecialchars($_SESSION['user_nama'] ?? '') ?>" placeholder="Nama Penerima" required>
                        <input type="tel" name="hp" placeholder="Nomor WhatsApp (Aktif)" required>
                        <textarea name="alamat" rows="3" placeholder="Alamat Lengkap (Jl, No, Kec, Kota)" required></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fa-solid fa-credit-card" style="color: var(--primary);"></i> Metode Pembayaran</h3>

                    <div class="payment-accordion">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <span><i class="fa-solid fa-building-columns"></i> &nbsp; Transfer Bank</span>
                            <i class="fa-solid fa-chevron-down chevron"></i>
                        </div>
                        <div class="accordion-content">
                            <div class="payment-grid">
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="BCA">
                                    <div class="payment-label"><img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg"><span>BCA</span></div>
                                </label>
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="MANDIRI">
                                    <div class="payment-label"><img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg"><span>Mandiri</span></div>
                                </label>
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="BNI">
                                    <div class="payment-label"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrEaF7_JLRn2JynymL2tni8lZZJD9_Scjg1g&s"><span>BNI</span></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="payment-accordion">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <span><i class="fa-solid fa-wallet"></i> &nbsp; E-Wallet & QRIS</span>
                            <i class="fa-solid fa-chevron-down chevron"></i>
                        </div>
                        <div class="accordion-content">
                            <div class="payment-grid">
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="DANA">
                                    <div class="payment-label"><img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg"><span>DANA</span></div>
                                </label>
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="OVO">
                                    <div class="payment-label"><img src="https://upload.wikimedia.org/wikipedia/commons/e/eb/Logo_ovo_purple.svg"><span>OVO</span></div>
                                </label>
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="GOPAY">
                                    <div class="payment-label"><img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg"><span>GoPay</span></div>
                                </label>
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="QRIS">
                                    <div class="payment-label"><img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg"><span>QRIS</span></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="payment-accordion">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <span><i class="fa-solid fa-store"></i> &nbsp; Gerai & COD</span>
                            <i class="fa-solid fa-chevron-down chevron"></i>
                        </div>
                        <div class="accordion-content">
                            <div class="payment-grid">
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="INDOMARET">
                                    <div class="payment-label"><img src="https://upload.wikimedia.org/wikipedia/commons/9/9d/Logo_Indomaret.png"><span>Indomaret</span></div>
                                </label>
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="ALFAMART">
                                    <div class="payment-label"><img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Alfamart_logo.svg"><span>Alfamart</span></div>
                                </label>
                                <label class="payment-item">
                                    <input type="radio" name="metode" value="COD">
                                    <div class="payment-label"><i class="fa-solid fa-handshake" style="font-size:24px; color:#4B5563; margin-bottom:5px;"></i><span>COD</span></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3><i class="fa-solid fa-money-bill-transfer" style="color: var(--primary);"></i> Konfirmasi Transfer</h3>
                    <div class="input-group" style="display: grid; gap: 15px;">
                        <input type="text" name="no_rekening" placeholder="Nomor Rekening / No. HP Pengirim" required>
                        <input type="number" id="nominal_transfer" name="nominal_transfer" placeholder="Nominal Transfer (Contoh: 50000)" required>
                    </div>
                </div>
            </div>

            <div class="sidebar">
                <div class="card" style="position: sticky; top: 20px;">
                    <h3><i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Ringkasan Pesanan</h3>

                    <div style="margin-top: 15px; max-height: 200px; overflow-y: auto; padding-right: 8px;">
                        <?php
                        foreach ($_SESSION['keranjang'] as $id => $qty):
                            $id_safe = mysqli_real_escape_string($conn, $id);
                            $res = mysqli_query($conn, "SELECT * FROM produk WHERE id = '$id_safe'");
                            $row = mysqli_fetch_assoc($res);
                            $subtotal = $row['harga'] * $qty;
                            $total_belanja += $subtotal;
                        ?>
                            <div class="order-item">
                                <img src="<?= $row['foto'] ?>" onerror="this.src='https://via.placeholder.com/60'">
                                <div style="flex:1;">
                                    <p style="font-size:13px; font-weight:700; margin:0;"><?= htmlspecialchars($row['nama']) ?></p>
                                    <div style="display:flex; justify-content:space-between; margin-top:5px;">
                                        <span style="font-size:12px; color:var(--gray);"><?= $qty ?> x Rp <?= number_format($row['harga']) ?></span>
                                        <span style="font-size:13px; font-weight:700;">Rp <?= number_format($subtotal) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="estimation-section">
                        <i class="fa-solid fa-clock-rotate-left" style="color: #B45309; font-size: 20px;"></i>
                        <div style="flex:1;">
                            <p style="font-size: 13px; font-weight: 700; color: var(--warning-text); margin:0;">Estimasi Tiba</p>
                            <p style="font-size: 11px; color: #B45309; margin:0;">2 - 3 Hari Kerja</p>
                        </div>
                    </div>

                    <div style="background: #F9FAFB; border-radius: 18px; padding: 20px; border: 1px solid #F1F5F9;">
                        <div class="total-row">
                            <span style="font-size: 16px;">Total Tagihan</span>
                            <span>Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
                            <input type="hidden" id="total_final_val" name="total_final" value="<?= $total_belanja ?>">
                        </div>
                    </div>

                    <div class="policy-container">
                        <label class="policy-item">
                            <input type="checkbox" id="policy_agree">
                            <span>
                                Saya telah membaca dan menyetujui
                                <a href="javascript:void(0)" onclick="showPolicy('terms')">Syarat & Ketentuan</a> serta
                                <a href="javascript:void(0)" onclick="showPolicy('privacy')">Kebijakan Privasi</a> yang berlaku di Cartix.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn-pay" id="btnSubmit" onclick="return validasiCheckout(event)" disabled>
                        <i class="fa-solid fa-shield-lock"></i> Konfirmasi & Bayar
                    </button>

                    <a href="cart.php" class="btn-back-cart">
                        <i class="fa-solid fa-chevron-left" style="font-size: 10px; margin-right: 8px;"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div id="policyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Informasi</h2>
                <span class="close-modal" onclick="closePolicy()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody">
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleAccordion(header) {
            const allHeaders = document.querySelectorAll('.accordion-header');
            allHeaders.forEach(h => {
                if (h !== header) {
                    h.classList.remove('active');
                    h.nextElementSibling.style.maxHeight = null;
                }
            });
            header.classList.toggle('active');
            const content = header.nextElementSibling;
            content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px";
        }

        // Script Aktivasi Tombol
        const checkbox = document.getElementById('policy_agree');
        const btnSubmit = document.getElementById('btnSubmit');

        checkbox.addEventListener('change', function() {
            btnSubmit.disabled = !this.checked;
        });

        // FUNGSI BARU: VALIDASI NOMINAL & METODE
        function validasiCheckout(e) {
            const metode = document.querySelector('input[name="metode"]:checked');
            const nominal = parseInt(document.getElementById('nominal_transfer').value);
            const total = parseInt(document.getElementById('total_final_val').value);

            if (!metode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Metode Pembayaran',
                    text: 'Silakan pilih metode pembayaran terlebih dahulu!',
                    confirmButtonColor: '#2563EB'
                });
                return false;
            }

            if (isNaN(nominal) || nominal < total) {
                Swal.fire({
                    icon: 'error',
                    title: 'Nominal Kurang',
                    text: 'Nominal transfer tidak boleh kurang dari total tagihan!',
                    confirmButtonColor: '#2563EB'
                });
                return false;
            }

            return true;
        }

        // MODAL LOGIC
        function showPolicy(type) {
            const modal = document.getElementById('policyModal');
            const title = document.getElementById('modalTitle');
            const body = document.getElementById('modalBody');

            if (type === 'terms') {
                title.innerText = "Syarat & Ketentuan | Cartix";
                body.innerHTML = `
                    <h4>1. Ketentuan Umum</h4>
                    <p>Dengan melakukan pemesanan di Cartix, Anda dianggap telah memahami dan menyetujui alur transaksi yang berlaku.</p>
                    <h4>2. Pembayaran</h4>
                    <p>Pembayaran wajib dilakukan dalam waktu 1x24 jam. Pesanan akan otomatis dibatalkan jika melewati batas waktu tersebut.</p>
                    <h4>3. Pengiriman</h4>
                    <p>Barang dikirim dari gudang pusat kami. Estimasi waktu sampai bergantung pada kurir dan lokasi tujuan.</p>
                    <h4>4. Pengembalian</h4>
                    <p>Klaim kerusakan wajib menyertakan video unboxing tanpa jeda/editing.</p>
                `;
            } else if (type === 'privacy') {
                title.innerText = "Kebijakan Privasi | Cartix";
                body.innerHTML = `
                    <h4>1. Data Pribadi</h4>
                    <p>Nama, nomor HP, dan alamat yang Anda masukkan hanya digunakan untuk keperluan pengiriman barang dan informasi status pesanan.</p>
                    <h4>2. Keamanan Data</h4>
                    <p>Kami tidak akan menjual atau memberikan data pribadi Anda kepada pihak ketiga mana pun kecuali untuk kebutuhan logistik (kurir).</p>
                    <h4>3. Cookies</h4>
                    <p>Situs ini menggunakan session untuk menyimpan data keranjang belanja Anda agar transaksi berjalan lancar.</p>
                `;
            }
            modal.style.display = "block";
            document.body.style.overflow = "hidden"; // Disable scroll
        }

        function closePolicy() {
            document.getElementById('policyModal').style.display = "none";
            document.body.style.overflow = "auto"; // Enable scroll
        }

        // Close modal if click outside
        window.onclick = function(event) {
            const modal = document.getElementById('policyModal');
            if (event.target == modal) {
                closePolicy();
            }
        }
    </script>
</body>

</html>