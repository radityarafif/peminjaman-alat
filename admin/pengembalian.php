<?php
require '../config.php';
cek('admin');
$KOND = ['baik' => 'Baik', 'rusak ringan' => 'Rusak ringan', 'rusak berat' => 'Rusak berat'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  coba(function () use ($id) {
    $p = $id ? q("SELECT p.* FROM pengembalian g JOIN peminjaman p ON p.id=g.peminjaman_id WHERE g.id=?", [$id], 'i')->fetch_assoc() : null;
    if (isset($_POST['hapus']) && $p) {
      q("DELETE FROM pengembalian WHERE id=?", [$id], 'i');
      q("UPDATE peminjaman SET status='disetujui' WHERE id=?", [$p['id']], 'i');
      q("UPDATE alat SET stok=stok-? WHERE id=?", [$p['jumlah'], $p['alat_id']], 'ii');
      logAkt("Hapus pengembalian #$id");
      flash('Pengembalian dihapus, status peminjaman kembali ke disetujui');
      return;
    }
    if ($p) {
      q(
        "UPDATE pengembalian SET tgl_kembali=?,kondisi=?,denda=?,catatan=? WHERE id=?",
        [$_POST['tgl_kembali'], $_POST['kondisi'], hitungDenda($p['tgl_kembali_rencana'], $_POST['tgl_kembali']), $_POST['catatan'], $id],
        'ssisi'
      );
      logAkt("Ubah pengembalian #$id");
      flash('Data tersimpan');
    } else {
      kembalikan((int)$_POST['peminjaman_id'], $_POST['tgl_kembali'], $_POST['kondisi'], $_POST['catatan']);
      logAkt("Tambah pengembalian peminjaman #" . (int)$_POST['peminjaman_id']);
      flash('Pengembalian dicatat');
    }
  });
  header('Location: pengembalian.php');
  exit;
}
$edit = isset($_GET['edit']) ? q("SELECT * FROM pengembalian WHERE id=?", [(int)$_GET['edit']], 'i')->fetch_assoc() : null;
$rows = q("SELECT g.*, u.nama, a.nama_alat, p.tgl_kembali_rencana FROM pengembalian g JOIN peminjaman p ON p.id=g.peminjaman_id JOIN users u ON u.id=p.user_id JOIN alat a ON a.id=p.alat_id ORDER BY g.id DESC")->fetch_all(MYSQLI_ASSOC);
$aktif = pilih("SELECT p.id, CONCAT('#',p.id,' - ',u.nama,' - ',a.nama_alat) AS label FROM peminjaman p JOIN users u ON u.id=p.user_id JOIN alat a ON a.id=p.alat_id WHERE p.status='disetujui'");
$judul = 'CRUD Pengembalian';
include '../inc/header.php';
?>
<form method="post" class="card card-body mb-4 row g-2 flex-row">
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? 0) ?>">
  <?php if (!$edit): ?><div class="col-md-3"><select name="peminjaman_id" class="form-select" required>
        <option value="">- Peminjaman aktif -</option><?php opt($aktif) ?>
      </select></div><?php endif ?>
  <div class="col-md-2"><input type="date" name="tgl_kembali" class="form-control" value="<?= e($edit['tgl_kembali'] ?? date('Y-m-d')) ?>" required></div>
  <div class="col-md-2"><select name="kondisi" class="form-select"><?php opt($KOND, $edit['kondisi'] ?? '') ?></select></div>
  <div class="col-md-3"><input name="catatan" class="form-control" placeholder="Catatan" value="<?= e($edit['catatan'] ?? '') ?>"></div>
  <div class="col-md-2"><button class="btn btn-primary"><?= $edit ? 'Update' : 'Catat' ?></button></div>
</form>
<p class="text-muted">Denda otomatis Rp <?= number_format(DENDA_PER_HARI, 0, ',', '.') ?>/hari keterlambatan.</p>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>Peminjam</th>
      <th>Alat</th>
      <th>Rencana</th>
      <th>Kembali</th>
      <th>Kondisi</th>
      <th>Denda</th>
      <th>Catatan</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?><tr>
        <td><?= $r['id'] ?></td>
        <td><?= e($r['nama']) ?></td>
        <td><?= e($r['nama_alat']) ?></td>
        <td><?= $r['tgl_kembali_rencana'] ?></td>
        <td><?= $r['tgl_kembali'] ?></td>
        <td><?= e($r['kondisi']) ?></td>
        <td>Rp <?= number_format($r['denda'], 0, ',', '.') ?></td>
        <td><?= e($r['catatan']) ?></td>
        <td><a class="btn btn-sm btn-warning" href="?edit=<?= $r['id'] ?>">Edit</a>
          <form method="post" class="d-inline" onsubmit="return confirm('Hapus?')"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="hapus" class="btn btn-sm btn-danger">Hapus</button></form>
        </td>
      </tr><?php endforeach ?>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>