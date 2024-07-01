<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['nim'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
  $id = $_POST['id'];
  $nim = $_POST['nim'];
  $nama = $_POST['nama'];
  $password = $_POST['password']; // Password tidak di-hash kembali karena hanya di-update jika diisi
  $existing_foto_path = $_POST['existing_foto_path'];

  // Tangani unggahan file foto
  $foto = $_FILES['foto']['name'];
  $foto_tmp = $_FILES['foto']['tmp_name'];

  // Cek apakah ada file foto yang diunggah
  if (!empty($foto)) {
    $foto_path = "uploads/" . $foto; // Lokasi penyimpanan foto

    if (move_uploaded_file($foto_tmp, $foto_path)) {
      // Hapus foto yang lama
      if ($existing_foto_path !== "images/default-image.webp" && file_exists($existing_foto_path)) {
        unlink($existing_foto_path);
      }
    } else {
      $error = "Gagal mengunggah foto. Silakan coba lagi.";
    }
  } else {
    // Jika tidak ada file foto yang diunggah, gunakan foto yang lama
    $foto_path = $existing_foto_path;
  }

  // Perbarui data mahasiswa ke database
  // Password hanya di-update jika tidak kosong
  if (!empty($password)) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE Mahasiswa SET nim='$nim', nama='$nama', password='$password', foto='$foto_path' WHERE id='$id'";
  } else {
    $sql = "UPDATE Mahasiswa SET nim='$nim', nama='$nama', foto='$foto_path' WHERE id='$id'";
  }

  if ($conn->query($sql) === TRUE) {
    // Perbarui data di variabel $row dengan data terbaru dari database
    $result = $conn->query("SELECT * FROM Mahasiswa WHERE id='$id'");
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
    }
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "Gagal memperbarui data mahasiswa. Silakan coba lagi.";
  }
}
