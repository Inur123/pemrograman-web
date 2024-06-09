<?php
session_start();
if (!isset($_SESSION['nim'])) {
  header("Location: login.php");
  exit;
}
if (isset($_SESSION['nama'])) {
  $nama = $_SESSION['nama'];
} else {
  // Lakukan sesuatu jika $_SESSION['nama'] belum diinisialisasi
  $nama = "Pengguna";
}

require_once "koneksi.php";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah'])) {
  $nim = $_POST['nim'];
  $nama = $_POST['nama'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
  $role = 'mahasiswa'; // Atur peran secara default ke 'mahasiswa'
  // Periksa apakah NIM sudah ada di database
  $sql_check_nim = "SELECT * FROM Mahasiswa WHERE nim = '$nim'";
  $result_check_nim = $conn->query($sql_check_nim);
  if ($result_check_nim->num_rows > 0) {
    $error = "<span style='color: red;'>NIM sudah ada. Harap gunakan NIM lain.</span>";
  } else {
    // Tangani unggahan file foto
    $foto = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto_path = ""; // Atur nilai awal foto_path menjadi string kosong

    // Cek apakah ada file foto yang diunggah
    if (!empty($foto)) { // Tambahkan pengecekan apakah $foto tidak kosong
      $foto_path = "uploads/" . $foto; // Lokasi penyimpanan foto

      if (move_uploaded_file($foto_tmp, $foto_path)) {
        // Jika file berhasil diunggah, simpan path foto ke database
      } else {
        $error = "Gagal mengunggah foto. Silakan coba lagi.";
      }
    } else {
      // Jika pengguna tidak mengunggah foto, gunakan gambar default sebagai placeholder
      $foto_path = "images/default-image.webp";
    }

    // Jika NIM belum ada di database, lanjutkan proses penyimpanan data
    $sql = "INSERT INTO Mahasiswa (nim, nama, password, role, foto) VALUES ('$nim', '$nama', '$password', '$role', '$foto_path')";
    if ($conn->query($sql) === TRUE) {
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "Gagal menambahkan mahasiswa. Silakan coba lagi.";
    }
  }
}

// Jika pengguna adalah admin, tampilkan fungsi CRUD
if ($_SESSION['role'] == 'admin') {
  // Tampilkan fungsi CRUD untuk admin di sini
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo1.png" type="images/x-icon">
    <title>Dashboard Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
      .container {
        padding: 20px;
      }

      /* Atur lebar sisi kanan */
      .right-side {
        width: 30%;
      }

      /* Atur margin untuk memberikan jarak antara form dan tabel */
      .right-side .mt-2 {
        margin-top: 20px;
      }
    </style>
  </head>

  <body>
    <div class="container">
      <div class="dropdown float-end">
        <p class="dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
          <?php echo $nama; ?>
        </p>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <li><a class="dropdown-item" href="logout.php">Logout</a></li>
        </ul>
      </div>
      <h2 class="mb-5">Dashboard Admin</h2>

      <div class="row">
        <!-- Sisi Kiri: Tabel Data Mahasiswa -->
        <div class="col-md-8">
          <h3>Daftar Mahasiswa</h3>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>NIM</th>
                  <th>Nama</th>
                  <th>Foto</th>
                  <th>Role</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM Mahasiswa";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                  $counter = 1;
                  while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $counter . '</td>'; // Tambahkan nomor urut
                    echo '<td>' . $row['nim'] . '</td>';
                    echo '<td>' . $row['nama'] . '</td>';
                    echo '<td><img src="' . $row['foto'] . '" width="50" alt="' . $row['nama'] . '"></td>';
                    echo '<td>' . $row['role'] . '</td>';
                    echo '<td>';
                    echo '<button class="btn btn-primary btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editMahasiswaModal-' . $row['id'] . '">Edit</button>';
                    echo ' <a href="#" class="btn btn-danger btn-sm" onclick="showDeleteConfirmationModal(\'' . $row['id'] . '\')">Hapus</a>';
                    echo '<div class="form-check form-switch">';
                    echo '<input class="form-check-input" type="radio" name="approvedRadio' . $row['id'] . '" id="approvedRadio' . $row['id'] . '" value="1" onclick="toggleApproval(\'' . $row['id'] . '\')" ' . ($row['approved'] == 1 ? 'checked' : '') . '>';
                    if ($row['approved'] == 1) {
                      echo '<label class="form-check-label" for="approvedRadio' . $row['id'] . '" style=" color: green;">Approved</label>';
                    } else {
                      echo '<label class="form-check-label" for="approvedRadio' . $row['id'] . '" style=" color: red;">Not Approved</label>';
                    }
                    echo '</div>';



                    echo '</td>';
                    echo '</tr>';
                    // Modal untuk edit
                    renderEditModal($row['id'], $row['nim'], $row['nama'], $row['password'], $row['role']);
                    $counter++;
                  }
                } else {
                  echo '<tr><td colspan="6">Tidak ada data mahasiswa</td></tr>';
                }
                ?>
              </tbody>

            </table>
          </div>
        </div>

        <!-- Sisi Kanan: Form Tambah Mahasiswa Baru -->
        <div class="col-md-6 right-side">
          <h3 class="mt-2">Tambah Mahasiswa Baru</h3>
          <form method="post" action="" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="nim" class="form-label">NIM:</label>
              <input type="text" class="form-control" id="nim" name="nim" required>
            </div>
            <div class="mb-3">
              <label for="nama" class="form-label">Nama:</label>
              <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
              <label for="foto" class="form-label">Foto:</label>
              <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary" name="tambah">Tambah</button>
          </form>
          <?php if (isset($error)) {
            echo "<p>$error</p>";
          } ?>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bagian JavaScript -->
    <script>
      function toggleApproval(id) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "toggle_approval.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4) {
            if (xhr.status === 200) {
              // Handle response if needed
              console.log(xhr.responseText);
              // Refresh halaman jika perubahan berhasil
              location.reload(); // Contoh: refresh halaman
            } else {
              // Tampilkan pesan kesalahan jika terjadi kesalahan dalam permintaan AJAX
              console.error("Error: " + xhr.status);
            }
          }
        };

        // Determine the new status to be sent based on the current status
        var currentStatus = document.getElementById("approvedRadio" + id).checked;
        var newStatus = currentStatus ? 0 : 1; // Toggle the status

        xhr.send("id=" + id + "&status=" + newStatus); // Send both ID and new status to the server
      }
    </script>





  </body>

  </html>

<?php
} else {
  // Jika pengguna adalah mahasiswa, tampilkan informasi mahasiswa
  $nim = $_SESSION['nim'];
  $sql = "SELECT * FROM Mahasiswa WHERE nim = '$nim'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .container {
        padding: 20px;
      }
    </style>
  </head>

  <body>
    <div class="container">
      <h2 class="mt-5">Dashboard Mahasiswa</h2>
      <p>Selamat datang, <?php echo $row['nama']; ?>!</p>
      <p>NIM: <?php echo $row['nim']; ?></p>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <!-- Bootstrap Bundle with Popper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
  </body>

  </html>
<?php
}
?>

<?php
function renderEditModal($id, $nim, $nama, $role, $foto)
{
  echo '
    <!-- Modal untuk Edit Mahasiswa -->
    <div class="modal fade" id="editMahasiswaModal-' . $id . '" tabindex="-1" aria-labelledby="editMahasiswaModalLabel-' . $id . '" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editMahasiswaModalLabel-' . $id . '">Edit Mahasiswa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="post" action="edit_mahasiswa.php" enctype="multipart/form-data">
              <input type="hidden" name="id" value="' . $id . '">
              <div class="mb-3">
                <label for="nimEdit" class="form-label">NIM:</label>
                <input type="text" class="form-control" id="nimEdit" name="nim" value="' . $nim . '" required>
              </div>
              <div class="mb-3">
                <label for="namaEdit" class="form-label">Nama:</label>
                <input type="text" class="form-control" id="namaEdit" name="nama" value="' . $nama . '" required>
              </div>
              <div class="mb-3">
                <label for="passwordEdit" class="form-label">Password (kosongkan jika tidak ingin mengubah):</label>
                <input type="password" class="form-control" id="passwordEdit" name="password">
              </div>
              <div class="mb-3">
                <label for="roleEdit" class="form-label">Role:</label>
                <select class="form-select" id="roleEdit" name="role" required>
                  <option value="mahasiswa"' . ($role == 'mahasiswa' ? ' selected' : '') . '>Mahasiswa</option>
                  <option value="admin"' . ($role == 'admin' ? ' selected' : '') . '>Admin</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="fotoEdit" class="form-label">Foto:</label>
                <input type="file" class="form-control" id="fotoEdit" name="foto">
                <input type="hidden" name="existing_foto_path" value="' . $foto . '">
              </div>
              <button type="submit" class="btn btn-primary" name="edit">Simpan Perubahan</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  ';
}

?>
<!-- Modal konfirmasi penghapusan -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmationModalLabel">Konfirmasi Penghapusan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Anda yakin ingin menghapus mahasiswa ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <!-- Tombol "Hapus" yang memicu penghapusan -->
        <a id="deleteButton" class="btn btn-danger">Hapus</a>
      </div>
    </div>
  </div>
</div>

<script>
  // Fungsi untuk menampilkan modal konfirmasi penghapusan
  function showDeleteConfirmationModal(id) {
    var deleteButton = document.getElementById('deleteButton');
    // Set URL penghapusan sesuai dengan ID mahasiswa yang akan dihapus
    deleteButton.setAttribute('href', 'hapus_mahasiswa.php?id=' + id);
    // Tampilkan modal konfirmasi penghapusan
    var deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    deleteConfirmationModal.show();
  }
</script>