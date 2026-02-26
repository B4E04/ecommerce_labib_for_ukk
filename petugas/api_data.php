<?php
header('Content-Type: application/json');

// Contoh data dinamis (bisa kamu ganti dengan query SQL)
$data_pesanan    = [10, 25, 15, 30, 20, 48]; // Angka terakhir (48) akan dibandingkan dengan (20)
$data_pengunjung = [15, 18, 12, 25, 22, 35]; 

echo json_encode([
    "graph_pesanan"    => $data_pesanan,
    "graph_pengunjung" => $data_pengunjung
]);