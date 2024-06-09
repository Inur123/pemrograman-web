<?php
session_start();

// Periksa apakah pengguna telah login
if (!isset($_SESSION['nim'])) {
  header("Location: login.php");
  exit;
}

// Periksa apakah pengguna memiliki akses sebagai admin
if ($_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit;
}

// Sambungkan ke database
require_once "koneksi.php";

// Inisialisasi variabel untuk menyimpan pesan kesalahan
$error = "";

// Periksa apakah ada parameter id yang dikirimkan melalui URL
if (isset($_GET['id'])) {
  // Escape parameter id untuk menghindari serangan SQL Injection
  $id = $conn->real_escape_string($_GET['id']);

  // Kueri SQL untuk menghapus data mahasiswa berdasarkan id
  $sql = "DELETE FROM Mahasiswa WHERE id = '$id'";

  // Eksekusi kueri dan periksa hasilnya
  if ($conn->query($sql) === TRUE) {
    // Jika penghapusan berhasil, arahkan kembali ke dashboard
    header("Location: dashboard.php?modal=deleted");
    exit;
  } else {
    // Jika terjadi kesalahan, set pesan kesalahan
    $error = "Gagal menghapus mahasiswa. Silakan coba lagi.";
  }
} else {
  // Jika tidak ada parameter id yang diberikan, set pesan kesalahan
  $error = "Tidak ada ID mahasiswa yang diberikan untuk dihapus.";
}

// Jika terjadi kesalahan, arahkan kembali ke dashboard dengan pesan kesalahan
header("Location: dashboard.php?error=" . urlencode($error));
exit;
