<?php
require '../config.php';
cek('admin');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  coba(function () use ($id) {
    if (isset($_POST['hapus'])) {
      q("DELETE FROM alat WHERE id=?", [$id], 'i');
      logAkt("Hapus alat #$id");
      flash('Alat dihapus');
      return;
    }
    $n = $_POST['nama_alat'];
    $k = (int)$_POST['kategori_id'] ?: null;
    $s = (int)$_POST['stok'];
    $c = $_POST['kondisi'];
    if ($id) {
      q("UPDATE alat SET nama_alat=?,kategori_id=?,stok=?,kondisi=? WHERE id=?", [$n, $k, $s, $c, $id], 'siisi');
      logAkt("Ubah alat $n");
    } else {
      q("INSERT INTO alat(nama_alat,kategori_id,stok,kondisi) VALUES(?,?,?,?)", [$n, $k, $s, $c], 'siis');
      logAkt("Tambah alat $n");
    }
    flash('Data tersimpan');
  });
  header('Location: alat.php');
  exit;
}
$edit = isset($_GET['edit']) ? q("SELECT * FROM alat WHERE id=?", [(int)$_GET['edit']], 'i')->fetch_assoc() : null;
$rows = q("SELECT a.*, k.nama_kategori FROM alat a LEFT JOIN kategori k ON k.id=a.kategori_id ORDER BY a.nama_alat")->fetch_all(MYSQLI_ASSOC);
$kat = pilih("SELECT id, nama_kategori AS label FROM kategori ORDER BY nama_kategori");
$judul = 'CRUD Alat';
include '../inc/header.php';
?>
<form method="post" class="card card-body mb-4 row g-2 flex-row">
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? 0) ?>">
  <div class="col-md-3"><input name="nama_alat" class="form-control" placeholder="Nama alat" value="<?= e($edit['nama_alat'] ?? '') ?>" required></div>
  <div class="col-md-3"><select name="kategori_id" class="form-select">
      <option value="0">- Kategori -</option><?php opt($kat, $edit['kategori_id'] ?? '') ?>
    </select></div>
  <div class="col-md-2"><input type="number" min="0" name="stok" class="form-control" placeholder="Stok" value="<?= e($edit['stok'] ?? 0) ?>" required></div>
  <div class="col-md-2"><select name="kondisi" class="form-select"><?php opt(['baik' => 'Baik', 'rusak ringan' => 'Rusak ringan', 'rusak berat' => 'Rusak berat'], $edit['kondisi'] ?? '') ?></select></div>
  <div class="col-md-2"><button class="btn btn-primary"><?= $edit ? 'Update' : 'Tambah' ?></button></div>
</form>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>Alat</th>
      <th>Kategori</th>
      <th>Stok</th>
      <th>Kondisi</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $i => $r): ?><tr>
        <td><?= $i + 1 ?></td>
        <td><?= e($r['nama_alat']) ?></td>
        <td><?= e($r['nama_kategori']) ?></td>
        <td><?= $r['stok'] ?></td>
        <td><?= e($r['kondisi']) ?></td>
        <td><a class="btn btn-sm btn-warning" href="?edit=<?= $r['id'] ?>">Edit</a>
          <form method="post" class="d-inline" onsubmit="return confirm('Hapus?')"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="hapus" class="btn btn-sm btn-danger">Hapus</button></form>
        </td>
      </tr><?php endforeach ?>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>