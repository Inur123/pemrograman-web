<?php
session_start();

if (!isset($_SESSION['nim'])) {
  header("Location: login.php");
  exit;
}

require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
  $id = $_POST['id'];
  $nim = isset($_POST['nim']) ? $_POST['nim'] : null;
  $nama = isset($_POST['nama']) ? $_POST['nama'] : null;
  $password = isset($_POST['password']) ? $_POST['password'] : null;
  $role = isset($_POST['role']) ? $_POST['role'] : null;
  $foto_path = isset($_POST['existing_foto_path']) ? $_POST['existing_foto_path'] : null;

  if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto_path = "uploads/" . $foto;

    if (!move_uploaded_file($foto_tmp, $foto_path)) {
      echo "Gagal mengunggah foto. Silakan coba lagi.";
      exit;
    }
  }

  if (!empty($password)) {
    $password = password_hash($password, PASSWORD_DEFAULT);
  } else {
    $password = null;
  }

  $sql = "UPDATE Mahasiswa SET ";
  $bind_types = "";
  $bind_values = array();

  if ($nim !== null) {
    $sql .= "nim=?, ";
    $bind_types .= "s";
    $bind_values[] = &$nim;
  }

  if ($nama !== null) {
    $sql .= "nama=?, ";
    $bind_types .= "s";
    $bind_values[] = &$nama;
  }

  if ($password !== null) {
    $sql .= "password=?, ";
    $bind_types .= "s";
    $bind_values[] = &$password;
  }

  if ($role !== null) {
    $sql .= "role=?, ";
    $bind_types .= "s";
    $bind_values[] = &$role;
  }

  if ($foto_path !== null) {
    $sql .= "foto=?, ";
    $bind_types .= "s";
    $bind_values[] = &$foto_path;
  }

  $sql = rtrim($sql, ", ");
  $sql .= " WHERE id=?";
  $bind_types .= "i";
  $bind_values[] = &$id;

  $stmt = $conn->prepare($sql);
  if ($stmt) {
    if (!empty($bind_values)) {
      $params = array_merge(array($bind_types), $bind_values);
      $ref_array = array();
      foreach ($params as $key => $value) {
        $ref_array[$key] = &$params[$key];
      }
      call_user_func_array(array($stmt, 'bind_param'), $ref_array);
    }

    if ($stmt->execute()) {
      header("Location: dashboard.php");
      exit;
    } else {
      echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
  } else {
    echo "Error preparing statement: " . $conn->error;
  }
}
