<?php
session_start();
if (isset($_SESSION['nim'])) {
  header("Location: dashboard.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require_once "koneksi.php";

  $nim = $_POST['nim'];
  $nama = $_POST['nama'];
  $password = $_POST['password'];
  $role = 'mahasiswa'; // Atur peran secara default ke 'mahasiswa'

  // Pengaturan file upload
  $target_dir = "uploads/"; // Direktori untuk menyimpan foto
  $target_file = $target_dir . basename($_FILES["foto"]["name"]); // Path lengkap file foto yang akan diunggah
  $uploadOk = 1; // Variabel untuk menandai apakah unggahan file berhasil atau tidak
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Mendapatkan tipe file gambar

  // Periksa apakah file gambar yang diunggah adalah gambar nyata atau palsu
  if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check !== false) {
      echo "File adalah gambar - " . $check["mime"] . ".";
      $uploadOk = 1;
    } else {
      echo "File bukan gambar.";
      $uploadOk = 0;
    }
  }

  // Periksa apakah file sudah ada
  if (file_exists($target_file)) {
    echo "Maaf, file sudah ada.";
    $uploadOk = 0;
  }

  // Periksa ukuran file
  if ($_FILES["foto"]["size"] > 500000) {
    echo "Maaf, ukuran file terlalu besar.";
    $uploadOk = 0;
  }

  // Izinkan hanya format gambar tertentu
  if (
    $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif"
  ) {
    echo "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
    $uploadOk = 0;
  }

  // Periksa jika $uploadOk telah diatur ke 0 oleh kesalahan
  if ($uploadOk == 0) {
    echo "Maaf, file tidak diunggah.";
    // jika semua ok, coba unggah file
  } else {
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
      echo "File " . htmlspecialchars(basename($_FILES["foto"]["name"])) . " telah berhasil diunggah.";
    } else {
      echo "Maaf, terjadi kesalahan saat mengunggah file.";
    }
  }

  $sql = "INSERT INTO Mahasiswa (nim, nama, password, role, foto) VALUES ('$nim', '$nama', '$password', '$role', '$target_file')";
  if ($conn->query($sql) === TRUE) {
    $_SESSION['nim'] = $nim;
    $_SESSION['role'] = $role;
    header("Location: dashboard.php");
  } else {
    $error = "Gagal mendaftar. Silakan coba lagi.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="images/logo1.png" type="images/x-icon">
  <title>Register</title>
</head>

<body>
  <h2>Registrasi Mahasiswa</h2>
  <form method="post" action="" enctype="multipart/form-data">
    <label for="nim">NIM:</label><br>
    <input type="text" id="nim" name="nim"><br>
    <label for="nama">Nama:</label><br>
    <input type="text" id="nama" name="nama"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <label for="foto">Foto:</label><br>
    <input type="file" name="foto" id="foto"><br><br>
    <input type="submit" value="Register" name="submit">
  </form>
  <?php if (isset($error)) {
    echo "<p>$error</p>";
  } ?>
</body>

</html>