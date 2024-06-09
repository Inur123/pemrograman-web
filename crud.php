<?php
include 'config.php';

// Add Mahasiswa
if (isset($_POST['add'])) {
  $nim = $_POST['nim'];
  $nama = $_POST['nama'];
  $foto = $_FILES['foto']['name'];
  $target = "upload/" . basename($foto);

  if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
    $sql = "INSERT INTO mahasiswa (nim, nama, foto) VALUES ('$nim', '$nama', '$foto')";
    if ($conn->query($sql) === TRUE) {
      header("Location: index.php");
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
}

// Edit Mahasiswa
if (isset($_POST['edit'])) {
  $id = $_POST['id'];
  $nim = $_POST['nim'];
  $nama = $_POST['nama'];
  $foto = $_FILES['foto']['name'];
  $target = "upload/" . basename($foto);

  if ($foto) {
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
      $sql = "UPDATE mahasiswa SET nim='$nim', nama='$nama', foto='$foto' WHERE id='$id'";
    }
  } else {
    $sql = "UPDATE mahasiswa SET nim='$nim', nama='$nama' WHERE id='$id'";
  }

  if ($conn->query($sql) === TRUE) {
    header("Location: index.php");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Delete Mahasiswa
if (isset($_POST['delete'])) {
  $id = $_POST['id'];
  $sql = "DELETE FROM mahasiswa WHERE id='$id'";
  if ($conn->query($sql) === TRUE) {
    header("Location: index.php");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

$conn->close();
