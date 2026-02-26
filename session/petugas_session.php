<?php
session_start();

if(!isset($_SESSION['petugas'])){
  header("Location: ../login_petugas.php");
  exit;
}
?>