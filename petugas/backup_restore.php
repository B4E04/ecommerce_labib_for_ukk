<?php
// SET ZONA WAKTU KE WIB
date_default_timezone_set("Asia/Jakarta");

include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

/* ======================
   KONFIGURASI
====================== */
$backupDir = __DIR__ . "/../backup/";
if (!is_dir($backupDir)) {
  mkdir($backupDir, 0777, true);
}

/* ======================
   BACKUP DATABASE (PHP)
====================== */
if (isset($_POST['backup'])) {

  $namaFile = "backup_" . date("Y-m-d_H-i-s") . ".sql";
  $pathFile = $backupDir . $namaFile;

  $sqlDump = "-- Backup Database ecommerce_labib\n";
  $sqlDump .= "-- Generated: " . date("Y-m-d H:i:s") . " WIB\n\n";
  $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

  $tables = [];
  $q = mysqli_query($conn, "SHOW TABLES");
  while ($row = mysqli_fetch_row($q)) {
    $tables[] = $row[0];
  }

  foreach ($tables as $table) {
    $create = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE `$table`"));
    $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
    $sqlDump .= $create[1] . ";\n\n";

    $data = mysqli_query($conn, "SELECT * FROM `$table`");
    while ($row = mysqli_fetch_assoc($data)) {
      $cols = array_keys($row);
      $vals = array_map(function ($v) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $v) . "'";
      }, array_values($row));
      $sqlDump .= "INSERT INTO `$table` (`" . implode("`,`", $cols) . "`) VALUES (" . implode(",", $vals) . ");\n";
    }
    $sqlDump .= "\n\n";
  }

  $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";
  file_put_contents($pathFile, $sqlDump);

  if (filesize($pathFile) > 0) {
    header("Content-Type: application/sql");
    header("Content-Disposition: attachment; filename=\"$namaFile\"");
    header("Content-Length: " . filesize($pathFile));
    readfile($pathFile);
    exit;
  } else {
    die("Backup gagal dibuat");
  }
}

/* ======================
   RESTORE DATABASE (SUPER SAFE LINE-BY-LINE)
====================== */
if (isset($_POST['restore'])) {
  if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) {
    die("File tidak valid");
  }

  $path = $_FILES['file']['tmp_name'];
  // Membaca file per baris dan mengabaikan baris kosong
  $sqlLines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

  $tempQuery = '';
  $success = true;
  $errorMsg = '';

  // Matikan check foreign key agar tidak bentrok saat hapus tabel
  mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

  foreach ($sqlLines as $line) {
    $line = trim($line);

    // Abaikan jika baris adalah komentar SQL
    if (empty($line) || substr($line, 0, 2) == '--' || substr($line, 0, 1) == '#') {
      continue;
    }

    $tempQuery .= $line;

    // Jika menemukan titik koma di ujung baris, berarti satu perintah SQL selesai
    if (substr($line, -1) == ';') {
      if (!mysqli_query($conn, $tempQuery)) {
        $success = false;
        $errorMsg = mysqli_error($conn);
        break;
      }
      $tempQuery = ''; // Reset untuk perintah berikutnya
    }
  }

  mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

  if ($success) {
    header("Location: backup_restore.php?msg=restore_ok");
    exit;
  } else {
    die("Restore Gagal! Error pada query: <br><code>$tempQuery</code><br><br>Pesan Error: <b>$errorMsg</b>");
  }
}

/* ======================
   LIST BACKUP FILE
====================== */
$files = array_diff(scandir($backupDir), ['.', '..']);
rsort($files);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Backup & Restore - WIB</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: Inter, Arial, sans-serif;
    }

    body {
      margin: 0;
      background: #f5f7fb;
    }

    .wrapper {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styling (Versi yang sudah kita rapikan sebelumnya) */
    .sidebar {
      width: 240px;
      background: #111827;
      color: #fff;
      padding: 25px 20px;
    }

    .sidebar h2 {
      font-size: 20px;
      margin-bottom: 30px;
    }

    .sidebar a {
      display: block;
      font-size: 15px;
      color: #cbd5e1;
      text-decoration: none;
      padding: 12px 14px;
      border-radius: 8px;
      margin-bottom: 10px;
      transition: 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: #2563eb;
      color: #fff;
    }

    .sidebar a.logout-btn {
      color: #ef4444;
      margin-top: 20px;
      font-weight: 600;
    }

    .sidebar a.logout-btn:hover {
      background: #fee2e2;
      color: #b91c1c;
    }

    .content {
      flex: 1;
      padding: 40px;
    }

    .card {
      background: #fff;
      padding: 25px;
      border-radius: 14px;
      margin-bottom: 25px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
    }

    h1 {
      font-size: 22px;
      margin-bottom: 5px;
    }

    p {
      color: #6b7280;
      margin-bottom: 25px;
    }

    button {
      padding: 12px 18px;
      border: none;
      border-radius: 8px;
      background: #2563eb;
      color: #fff;
      cursor: pointer;
      font-weight: 600;
    }

    button.restore {
      background: #16a34a;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      background: #1e293b;
      color: #fff;
      padding: 12px;
      text-align: left;
    }

    td {
      padding: 12px;
      border-bottom: 1px solid #e5e7eb;
    }

    .alert {
      padding: 14px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .success {
      background: #dcfce7;
      color: #166534;
    }

    .waktu-skrg {
      font-weight: bold;
      color: #2563eb;
      background: #eef2ff;
      padding: 4px 8px;
      border-radius: 4px;
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
      <a href="laporan_penjualan.php">Laporan Penjualan</a>
      <a href="laporan_stok.php">Laporan Stok</a>
      <a href="backup_restore.php" class="active">Backup & Restore</a>
      <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">Logout</a>
    </div>

    <div class="content">
      <h1>Backup & Restore Database</h1>
      <p>Waktu Server (WIB): <span id="realtime-clock" class="waktu-skrg">00:00:00</span></p>

      <?php if (isset($_GET['msg'])) { ?>
        <div class="alert success">
          <?= $_GET['msg'] == 'backup_ok' ? 'Backup berhasil dibuat' : 'Restore database berhasil' ?>
        </div>
      <?php } ?>

      <div class="card">
        <h3>Backup Database</h3>
        <form method="post">
          <button name="backup">Backup Sekarang</button>
        </form>
      </div>

      <div class="card">
        <h3>Restore Database</h3>
        <form method="post" enctype="multipart/form-data">
          <input type="file" name="file" required>
          <br><br>
          <button name="restore" class="restore"
            onclick="return confirm('Yakin ingin restore? Data lama akan diganti.')">Restore Database</button>
        </form>
      </div>

      <div class="card">
        <h3>Riwayat Backup di Server</h3>
        <table>
          <tr>
            <th>Nama File</th>
            <th>Ukuran</th>
            <th>Waktu Backup (WIB)</th>
          </tr>
          <?php foreach ($files as $f) { ?>
            <tr>
              <td><?= $f ?></td>
              <td><?= round(filesize($backupDir . $f) / 1024, 2) ?> KB</td>
              <td><?= date("d/m/Y H:i:s", filemtime($backupDir . $f)) ?> WIB</td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>
  </div>

  <script>
    function updateClock() {
      const now = new Date();
      const options = { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
      const formatter = new Intl.DateTimeFormat('en-GB', options);
      document.getElementById('realtime-clock').innerText = formatter.format(now) + " WIB";
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>

</body>

</html>