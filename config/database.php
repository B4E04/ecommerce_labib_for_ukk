<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ecommerce_labib";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
  die("KONEKSI GAGAL: " . mysqli_connect_error());
}
?>


