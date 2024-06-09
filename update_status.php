<?php
// Koneksi ke database
$conn = new mysqli('localhost', 'root', 'root', 'mahasiswa');

// Periksa koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari permintaan AJAX
$id = $_POST['id'];
$approved = $_POST['approved'];

// Update status di database
$sql = "UPDATE Mahasiswa SET approved='$approved' WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
  echo "Status berhasil diperbarui.";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

// Tutup koneksi
$conn->close();
