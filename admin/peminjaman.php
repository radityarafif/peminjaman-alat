<?php
require '../config.php';
cek('admin');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  coba(function () use ($id) {
    if (isset($_POST['hapus'])) {
      $p = q("SELECT * FROM peminjaman WHERE id=?", [$id], 'i')->fetch_assoc();
      if ($p && $p['status'] == 'disetujui') q("UPDATE alat SET stok=stok+? WHERE id=?", [$p['jumlah'], $p['alat_id']], 'ii');
      q("DELETE FROM peminjaman WHERE id=?", [$id], 'i');
      logAkt("Hapus peminjaman #$id");
      flash('Data peminjaman dihapus');
      return;
    }
    $f = [(int)$_POST['user_id'], (int)$_POST['alat_id'], (int)$_POST['jumlah'], $_POST['tgl_pinjam'], $_POST['tgl_kembali_rencana']];
    if ($id) {
      q("UPDATE peminjaman SET user_id=?,alat_id=?,jumlah=?,tgl_pinjam=?,tgl_kembali_rencana=? WHERE id=? AND status='menunggu'", [...$f, $id], 'iiissi');
      logAkt("Ubah peminjaman #$id");
    } else {
      q("INSERT INTO peminjaman(user_id,alat_id,jumlah,tgl_pinjam,tgl_kembali_rencana) VALUES(?,?,?,?,?)", $f, 'iiiss');
      logAkt("Tambah peminjaman");
    }
    flash('Data tersimpan (edit hanya berlaku untuk status menunggu)');
  });
  header('Location: peminjaman.php');
  exit;
}
$edit = isset($_GET['edit']) ? q("SELECT * FROM peminjaman WHERE id=?", [(int)$_GET['edit']], 'i')->fetch_assoc() : null;
$rows = q("SELECT p.*, u.nama, a.nama_alat FROM peminjaman p JOIN users u ON u.id=p.user_id JOIN alat a ON a.id=p.alat_id ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);
$us = pilih("SELECT id, nama AS label FROM users WHERE role='peminjam' ORDER BY nama");
$al = pilih("SELECT id, nama_alat AS label FROM alat ORDER BY nama_alat");
$judul = 'CRUD Data Peminjaman';
include '../inc/header.php';
?>
<form method="post" class="card card-body mb-4 row g-2 flex-row">
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? 0) ?>">
  <div class="col-md-2"><select name="user_id" class="form-select"><?php opt($us, $edit['user_id'] ?? '') ?></select></div>
  <div class="col-md-2"><select name="alat_id" class="form-select"><?php opt($al, $edit['alat_id'] ?? '') ?></select></div>
  <div class="col-md-1"><input type="number" min="1" name="jumlah" class="form-control" value="<?= e($edit['jumlah'] ?? 1) ?>" required></div>
  <div class="col-md-2"><input type="date" name="tgl_pinjam" class="form-control" value="<?= e($edit['tgl_pinjam'] ?? date('Y-m-d')) ?>" required></div>
  <div class="col-md-3"><input type="date" name="tgl_kembali_rencana" class="form-control" value="<?= e($edit['tgl_kembali_rencana'] ?? date('Y-m-d', strtotime('+3 days'))) ?>" required></div>
  <div class="col-md-2"><button class="btn btn-primary"><?= $edit ? 'Update' : 'Tambah' ?></button></div>
</form>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>Peminjam</th>
      <th>Alat</th>
      <th>Jml</th>
      <th>Tgl Pinjam</th>
      <th>Rencana Kembali</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?><tr>
        <td><?= $r['id'] ?></td>
        <td><?= e($r['nama']) ?></td>
        <td><?= e($r['nama_alat']) ?></td>
        <td><?= $r['jumlah'] ?></td>
        <td><?= $r['tgl_pinjam'] ?></td>
        <td><?= $r['tgl_kembali_rencana'] ?></td>
        <td><?= badge($r['status']) ?></td>
        <td><?php if ($r['status'] == 'menunggu'): ?><a class="btn btn-sm btn-warning" href="?edit=<?= $r['id'] ?>">Edit</a> <?php endif ?>
          <form method="post" class="d-inline" onsubmit="return confirm('Hapus?')"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="hapus" class="btn btn-sm btn-danger">Hapus</button></form>
        </td>
      </tr><?php endforeach ?>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>