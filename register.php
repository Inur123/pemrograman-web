<?php
session_start();
require_once "koneksi.php";

$nim = $nama = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
  $nim = $_POST['nim'];
  $nama = $_POST['nama'];
  $password = $_POST['password'];

  // Validasi panjang password
  if (strlen($password) < 6) {
    $error = "Password minimal harus memiliki panjang 6 karakter.";
  } else {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'mahasiswa'; // Atur peran secara default ke 'mahasiswa'
    $approved = 0; // Atur status approved ke 0 (not approved)

    // Persiapkan variabel foto_path dengan nilai default
    $foto_path = "images/default-image.webp"; // Gunakan foto default jika pengguna tidak mengunggah foto baru

    // Validasi NIM tidak boleh sama
    $sql_check_nim = "SELECT * FROM Mahasiswa WHERE nim = ?";
    $stmt_check_nim = $conn->prepare($sql_check_nim);
    $stmt_check_nim->bind_param("s", $nim);
    $stmt_check_nim->execute();
    $result_check_nim = $stmt_check_nim->get_result();

    if ($result_check_nim->num_rows > 0) {
      $error = "NIM sudah digunakan. Harap gunakan NIM lain.";
    } else {
      // Cek apakah ada file foto yang diunggah
      if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_path = "uploads/" . $foto;

        if (move_uploaded_file($foto_tmp, $foto_path)) {
          // Jika file berhasil diunggah, simpan data ke database
          $sql = "INSERT INTO Mahasiswa (nim, nama, password, role, foto, approved) VALUES (?, ?, ?, ?, ?, ?)";

          // Persiapkan statement
          $stmt = $conn->prepare($sql);
          // Bind parameter ke statement
          $stmt->bind_param("sssssi", $nim, $nama, $password, $role, $foto_path, $approved);

          // Eksekusi statement
          if ($stmt->execute()) {
            // Redirect ke halaman login setelah pendaftaran berhasil
            header("Location: login.php");
            exit;
          } else {
            $error = "Gagal menambahkan mahasiswa. Silakan coba lagi.";
          }
        } else {
          $error = "Gagal mengunggah foto. Silakan coba lagi.";
        }
      }

      // Jika tidak ada foto yang diunggah, gunakan foto default
      else {
        // Simpan data ke database tanpa foto
        $sql = "INSERT INTO Mahasiswa (nim, nama, password, role, foto, approved) VALUES (?, ?, ?, ?, ?, ?)";
        // Persiapkan statement
        $stmt = $conn->prepare($sql);
        // Bind parameter ke statement
        $stmt->bind_param("sssssi", $nim, $nama, $password, $role, $foto_path, $approved);
        // Eksekusi statement
        if ($stmt->execute()) {
          // Redirect ke halaman login setelah pendaftaran berhasil
          header("Location: login.php");
          exit;
        } else {
          $error = "Gagal menambahkan mahasiswa. Silakan coba lagi.";
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Mahasiswa</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
    }

    .container {
      max-width: 400px;
      margin: 100px auto;
    }

    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .card-body {
      padding: 30px;
    }

    .form-label {
      font-weight: bold;
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    .text-danger {
      color: red;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">Register Mahasiswa</h2>
        <?php if (isset($error)) : ?>
          <div class="alert alert-danger error-alert position-fixed top-0 end-0 mb-3 m-2" role="alert" style="max-width: 400px;">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="nim" class="form-label">NIM:</label>
            <input type="text" class="form-control" id="nim" name="nim" value="<?php echo $nim; ?>" required>
          </div>
          <div class="mb-3">
            <label for="nama" class="form-label">Nama:</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $nama; ?>" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <div class="input-group">
              <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
              <span class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility()">
                <i class="bi bi-eye" id="togglePassword"></i>
              </span>
            </div>
          </div>
          <div class="mb-3">
            <label for="foto" class="form-label">Foto:</label>
            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary col-12" name="register">Register</button>
          </div>
        </form>
        <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login disini</a></p>
      </div>
    </div>
  </div>
</body>
<script>
  function togglePasswordVisibility() {
    const passwordInput = document.querySelector('#password');
    // Dapatkan jenis tipe input
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    // Setel tipe input baru
    passwordInput.setAttribute('type', type);
    // Ubah ikon mata
    const eyeIcon = document.querySelector('#togglePassword');
    eyeIcon.classList.toggle('bi-eye');
    eyeIcon.classList.toggle('bi-eye-slash');
  }
</script>


<script>
  // Menutup alert secara otomatis setelah 5 detik
  setTimeout(function() {
    document.querySelector('.error-alert').style.display = 'none';
  }, 5000);
</script>

</html>