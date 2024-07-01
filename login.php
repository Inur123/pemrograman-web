<?php
session_start();

$max_attempts = 3;
$lockout_time = 60; // in seconds

if (isset($_SESSION['nim'])) {
  header("Location: dashboard.php");
  exit;
}

// Initialize session variables if not set
if (!isset($_SESSION['login_attempts'])) {
  $_SESSION['login_attempts'] = 0;
}

if (!isset($_SESSION['last_attempt_time'])) {
  $_SESSION['last_attempt_time'] = 0;
}

$remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
$lockout = false;

if ($_SESSION['login_attempts'] >= $max_attempts) {
  $elapsed_time = time() - $_SESSION['last_attempt_time'];
  if ($elapsed_time < $lockout_time) {
    $wait_time = $lockout_time - $elapsed_time;
    $lockout = true;
  } else {
    $_SESSION['login_attempts'] = 0;
    $remaining_attempts = $max_attempts;
    $lockout = false;
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$lockout) {
  $recaptcha_secret = '6LclGgUqAAAAAODhjIrD3mY05FNCl3TLziK-1Ryu';
  $recaptcha_response = $_POST['g-recaptcha-response'];

  // Verify reCAPTCHA response
  $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
  $recaptcha_data = array(
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response
  );

  $options = array(
    'http' => array(
      'method' => 'POST',
      'header' => 'Content-type: application/x-www-form-urlencoded',
      'content' => http_build_query($recaptcha_data)
    )
  );

  $context = stream_context_create($options);
  $recaptcha_verify = file_get_contents($recaptcha_url, false, $context);
  $recaptcha_success = json_decode($recaptcha_verify);

  if ($recaptcha_success->success) {
    require_once "koneksi.php";

    $nim = $_POST['nim'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Mahasiswa WHERE nim = '$nim' AND approved = 1";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      if (password_verify($password, $row['password'])) {
        $_SESSION['nim'] = $nim;
        $_SESSION['role'] = $row['role'];
        header("Location: dashboard.php");
        exit;
      } else {
        $error = "NIM atau password salah";
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
      }
    } else {
      $error = "NIM atau password salah atau akun belum diizinkan untuk login";
      $_SESSION['login_attempts']++;
      $_SESSION['last_attempt_time'] = time();
    }
  } else {
    $error = "reCAPTCHA verification failed. Please try again.";
  }
}

$remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <!-- Bootstrap CSS -->
  <link rel="icon" href="images/logo1.png" type="images/x-icon">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
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
          <div class="g-recaptcha mb-3" data-sitekey="6LclGgUqAAAAAJDRTMr1FiAuRFYqowz5F_wCGXQG"></div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary col-12" <?php echo $_SESSION['login_attempts'] >= $max_attempts ? 'disabled' : ''; ?>>Login</button>
          </div>
        </form>
        <?php if ($lockout) : ?>
          <p class="text-center mt-2">Terlalu banyak percobaan gagal. Silakan coba lagi dalam <span id="countdown" style="color: red;"><?php echo $wait_time; ?></span> detik.</p>
        <?php else : ?>
          <p class="text-center mt-2">Kesempatan login tersisa: <span style="color: red;"><?php echo $remaining_attempts; ?></span></p>
        <?php endif; ?>
        <p class="text-center">Belum punya akun? <a href="register.php">Daftar disini</a></p>
        <p class="text-center" style="font-size: small; color: red;">Setelah register akun belum bisa gunakan/hubungi admin</p>
      </div>
    </div>
  </div>

  <script>
    // Menutup alert secara otomatis setelah 5 detik
    setTimeout(function() {
      const errorAlert = document.querySelector('.error-alert');
      if (errorAlert) {
        errorAlert.style.display = 'none';
      }
    }, 5000);

    function togglePasswordVisibility() {
      const passwordInput = document.querySelector('#password');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      const eyeIcon = document.querySelector('#togglePassword');
      eyeIcon.classList.toggle('bi-eye');
      eyeIcon.classList.toggle('bi-eye-slash');
    }

    // Countdown timer for lockout
    <?php if ($lockout) : ?>
      let countdownTime = <?php echo $wait_time; ?>;
      const countdownElement = document.getElementById('countdown');
      const countdownInterval = setInterval(() => {
        countdownTime--;
        countdownElement.textContent = countdownTime;
        if (countdownTime <= 0) {
          clearInterval(countdownInterval);
          location.reload();
        }
      }, 1000);
    <?php else : ?>
      let remainingAttempts = <?php echo $remaining_attempts; ?>;
      const button = document.querySelector('button[type="submit"]');
      const attemptsElement = document.querySelector('.text-center.mt-3');

      const checkAttemptsInterval = setInterval(() => {
        if (remainingAttempts <= 0) {
          clearInterval(checkAttemptsInterval);
          button.disabled = true;
          let countdownTime = <?php echo $lockout_time; ?>;
          attemptsElement.innerHTML = `Terlalu banyak percobaan gagal. Silakan coba lagi dalam <span id="countdown" style="color: red;">${countdownTime}</span> detik.`;

          const countdownInterval = setInterval(() => {
            countdownTime--;
            document.getElementById('countdown').textContent = countdownTime;
            if (countdownTime <= 0) {
              clearInterval(countdownInterval);
              location.reload();
            }
          }, 1000);
        }
      }, 1000);
    <?php endif; ?>
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>