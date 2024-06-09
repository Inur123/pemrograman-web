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

  $sql = "SELECT * FROM Mahasiswa WHERE nim = '$nim' AND password = '$password'";
  $result = $conn->query($sql);

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $_SESSION['nim'] = $nim;
    $_SESSION['role'] = $row['role'];
    header("Location: dashboard.php");
  } else {
    $error = "NIM atau password salah";
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
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <h2 class="card-title text-center">Login Mahasiswa</h2>
        <form method="post" action="">
          <div class="mb-3">
            <label for="nim" class="form-label">NIM:</label>
            <input type="text" class="form-control" id="nim" name="nim" require>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" id="password" name="password" require>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary">Login</button>
          </div>
        </form>
        <?php if (isset($error)) {
          echo "<p class='text-danger text-center'>$error</p>";
        } ?>
      </div>
    </div>
  </div>
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>


</html>