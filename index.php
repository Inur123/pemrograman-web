<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRUD Mahasiswa</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h2>CRUD Mahasiswa</h2>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">Tambah Mahasiswa</button>

    <!-- Table -->
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>No</th>
          <th>NIM</th>
          <th>Nama</th>
          <th>Foto</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include 'config.php';
        $result = $conn->query("SELECT * FROM mahasiswa");
        $no = 1; // Inisialisasi variabel penghitung
        while ($row = $result->fetch_assoc()) :
        ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nim'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><img src="upload/<?= $row['foto'] ?>" width="50" alt="<?= $row['nama'] ?>"></td>
            <td>
              <button class="btn btn-success" data-toggle="modal" data-target="#editModal" data-id="<?= $row['id'] ?>" data-nim="<?= $row['nim'] ?>" data-nama="<?= $row['nama'] ?>" data-foto="<?= $row['foto'] ?>">Edit</button>
              <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" data-id="<?= $row['id'] ?>">Hapus</button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Add Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="crud.php" method="POST" enctype="multipart/form-data">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addModalLabel">Tambah Mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="nim">NIM</label>
              <input type="text" class="form-control" id="nim" name="nim" required>
            </div>
            <div class="form-group">
              <label for="nama">Nama</label>
              <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="form-group">
              <label for="foto">Foto</label>
              <input type="file" class="form-control" id="foto" name="foto" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="add">Tambah</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="crud.php" method="POST" enctype="multipart/form-data">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="edit_id" name="id">
            <div class="form-group">
              <label for="edit_nim">NIM</label>
              <input type="text" class="form-control" id="edit_nim" name="nim" required>
            </div>
            <div class="form-group">
              <label for="edit_nama">Nama</label>
              <input type="text" class="form-control" id="edit_nama" name="nama" required>
            </div>
            <div class="form-group">
              <label for="edit_foto">Foto</label>
              <input type="file" class="form-control" id="edit_foto" name="foto">
            </div>
            <img id="edit_img" src="" width="100" alt="" class="mt-3 mb-3">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="edit">Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="crud.php" method="POST">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Hapus Mahasiswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="delete_id" name="id">
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-danger" name="delete">Hapus</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $('#editModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var nim = button.data('nim');
      var nama = button.data('nama');
      var foto = button.data('foto');

      var modal = $(this);
      modal.find('#edit_id').val(id);
      modal.find('#edit_nim').val(nim);
      modal.find('#edit_nama').val(nama);
      modal.find('#edit_img').attr('src', 'upload/' + foto);
    });
  </script>
  <script>
    $('#editModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var nim = button.data('nim');
      var nama = button.data('nama');
      var foto = button.data('foto');

      var modal = $(this);
      modal.find('.modal-body #edit_id').val(id);
      modal.find('.modal-body #edit_nim').val(nim);
      modal.find('.modal-body #edit_nama').val(nama);
    });

    $('#deleteModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');

      var modal = $(this);
      modal.find('.modal-body #delete_id').val(id);
    });
  </script>
</body>

</html>