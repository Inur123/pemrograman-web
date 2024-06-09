<?php
session_start();
if (isset($_SESSION['nim'])) {
  header("Location: dashboard.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require_once "koneksi.php";

  $nim = $_POST['nim'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM Mahasiswa WHERE nim = '$nim' AND approved = 1"; // Hanya mahasiswa yang memiliki nilai approved = 1 yang diizinkan untuk login
  $result = $conn->query($sql);

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    // Verifikasi password
    if (password_verify($password, $row['password'])) {
      $_SESSION['nim'] = $nim;
      $_SESSION['role'] = $row['role'];
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "NIM atau password salah";
    }
  } else {
    $error = "NIM atau password salah atau akun belum diizinkan untuk login";
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <!-- Bootstrap CSS -->
  <link rel="icon" href="images/logo1.png" type="images/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    /* Custom CSS for styling */
    body {
      background-color: #f8f9fa;
      padding-top: 50px;
    }

    .card {
      max-width: 400px;
      margin: 0 auto;
      background-color: #fff;
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

    .text-center {
      text-align: center;
    }

    .mt-3 {
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Error Alert -->
    <?php if (isset($error)) : ?>
      <div class="alert alert-danger error-alert position-fixed top-0 end-0 mb-3 m-2" role="alert" style="max-width: 400px;">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>
    <div class="card">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">Login Mahasiswa</h2>
        <form method="post" action="">
          <div class="mb-3">
            <label for="nim" class="form-label">NIM:</label>
            <input type="text" class="form-control" id="nim" name="nim" value="<?php echo isset($_POST['nim']) ? $_POST['nim'] : ''; ?>" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <div class="input-group">
              <input type="password" class="form-control" id="password" name="password" required>
              <span class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility()">
                <i class="bi bi-eye" id="togglePassword"></i>
              </span>
            </div>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary col-12">Login</button>
          </div>
        </form>

        <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar disini</a></p>
        <p class="text-center mt-3" style="font-size: small; color: red;">Setelah register akun belum bisa gunakan/hubungi admin</p>
      </div>
    </div>
  </div>

  <script>
    // Menutup alert secara otomatis setelah 5 detik
    setTimeout(function() {
      document.querySelector('.error-alert').style.display = 'none';
    }, 5000);
  </script>
  <!-- Bootstrap Bundle with Popper -->
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>