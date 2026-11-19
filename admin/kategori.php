<?php
require '../config.php';
cek('admin');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  coba(function () use ($id) {
    if (isset($_POST['hapus'])) {
      q("DELETE FROM kategori WHERE id=?", [$id], 'i');
      logAkt("Hapus kategori #$id");
      flash('Kategori dihapus');
      return;
    }
    $n = $_POST['nama_kategori'];
    if ($id) {
      q("UPDATE kategori SET nama_kategori=? WHERE id=?", [$n, $id], 'si');
      logAkt("Ubah kategori $n");
    } else {
      q("INSERT INTO kategori(nama_kategori) VALUES(?)", [$n]);
      logAkt("Tambah kategori $n");
    }
    flash('Data tersimpan');
  });
  header('Location: kategori.php');
  exit;
}
$edit = isset($_GET['edit']) ? q("SELECT * FROM kategori WHERE id=?", [(int)$_GET['edit']], 'i')->fetch_assoc() : null;
$rows = q("SELECT * FROM kategori ORDER BY nama_kategori")->fetch_all(MYSQLI_ASSOC);
$judul = 'CRUD Kategori';
include '../inc/header.php';
?>
<form method="post" class="card card-body mb-4 flex-row gap-2">
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? 0) ?>">
  <input name="nama_kategori" class="form-control" placeholder="Nama kategori" value="<?= e($edit['nama_kategori'] ?? '') ?>" required>
  <button class="btn btn-primary"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>Kategori</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $i => $r): ?><tr>
        <td><?= $i + 1 ?></td>
        <td><?= e($r['nama_kategori']) ?></td>
        <td><a class="btn btn-sm btn-warning" href="?edit=<?= $r['id'] ?>">Edit</a>
          <form method="post" class="d-inline" onsubmit="return confirm('Hapus?')"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="hapus" class="btn btn-sm btn-danger">Hapus</button></form>
        </td>
      </tr><?php endforeach ?>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>