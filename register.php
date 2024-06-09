<?php
session_start();
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Jika pengguna mencoba melakukan pendaftaran
  if (isset($_POST['register'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'mahasiswa'; // Role default diubah menjadi "mahasiswa"

    // Tangani unggahan file foto
    $foto = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto_path = "";

    // Cek apakah ada file foto yang diunggah
    if (!empty($foto)) {
      $foto_path = "uploads/" . $foto;

      if (move_uploaded_file($foto_tmp, $foto_path)) {
        // Jika file berhasil diunggah, simpan data ke database
        $sql = "INSERT INTO Mahasiswa (nim, nama, password, role, foto) VALUES (?, ?, ?, ?, ?)";

        // Persiapkan statement
        $stmt = $conn->prepare($sql);
        // Bind parameter ke statement
        $stmt->bind_param("sssss", $nim, $nama, $password, $role, $foto_path);

        // Eksekusi statement
        if ($stmt->execute()) {
          // Redirect ke dashboard atau halaman lain setelah pendaftaran berhasil
          header("Location: dashboard.php");
          exit;
        } else {
          $error = "Gagal menambahkan mahasiswa. Silakan coba lagi.";
        }
      } else {
        $error = "Gagal mengunggah foto. Silakan coba lagi.";
      }
    } else {
      $error = "Silakan unggah foto Anda.";
    }
  }
}

// Tampilkan form pendaftaran mahasiswa umum jika pengguna bukan admin
if (isset($_SESSION['role']) && $_SESSION['role'] != 'admin') {
  $approved = 0; // Status approved untuk mahasiswa umum
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Mahasiswa Umum</title>
  </head>

  <body>
    <h2>Register Mahasiswa Umum</h2>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <form method="post" action="" enctype="multipart/form-data">
      <label for="nim">NIM:</label>
      <input type="text" id="nim" name="nim" required><br><br>
      <label for="nama">Nama:</label>
      <input type="text" id="nama" name="nama" required><br><br>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required><br><br>
      <label for="foto">Foto:</label>
      <input type="file" id="foto" name="foto" accept="image/*" required><br><br>
      <input type="submit" name="register" value="Register">
    </form>
  </body>

  </html>

<?php
} else {
  // Jika pengguna adalah admin, tampilkan dashboard admin
  // ...
}
?>