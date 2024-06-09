<?php
// Lakukan validasi permintaan POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['status'])) {
  // Ambil ID dan status baru dari permintaan
  $id = $_POST['id'];
  $newStatus = $_POST['status'];

  // Lakukan penyimpanan status baru ke database (misalnya dengan update SQL)
  require_once "koneksi.php"; // Pastikan ini mengarah ke file koneksi yang benar
  $sql = "UPDATE Mahasiswa SET approved = $newStatus WHERE id = $id"; // Misalnya, update status menjadi yang baru
  if ($conn->query($sql) === TRUE) {
    // Berhasil mengubah status
    echo "Status mahasiswa berhasil diubah.";
  } else {
    // Gagal mengubah status
    echo "Gagal mengubah status mahasiswa: " . $conn->error;
  }
} else {
  // Jika tidak ada ID atau status yang diterima dari permintaan, kirimkan pesan kesalahan
  echo "Permintaan tidak valid.";
}
