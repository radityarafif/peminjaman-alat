<?php
require '../config.php';
cek('petugas');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = (int)$_POST['id'];
  $p = q("SELECT p.*, a.stok, a.nama_alat FROM peminjaman p JOIN alat a ON a.id=p.alat_id WHERE p.id=? AND p.status='menunggu'", [$id], 'i')->fetch_assoc();
  if ($p) {
    if (isset($_POST['setuju'])) {
      if ($p['stok'] < $p['jumlah']) flash('Stok tidak cukup untuk menyetujui');
      else {
        q("UPDATE alat SET stok=stok-? WHERE id=?", [$p['jumlah'], $p['alat_id']], 'ii');
        q("UPDATE peminjaman SET status='disetujui', petugas_id=? WHERE id=?", [$_SESSION['user']['id'], $id], 'ii');
        logAkt("Setujui peminjaman #$id ({$p['nama_alat']})");
        flash('Peminjaman disetujui');
      }
    } else {
      q("UPDATE peminjaman SET status='ditolak', petugas_id=? WHERE id=?", [$_SESSION['user']['id'], $id], 'ii');
      logAkt("Tolak peminjaman #$id");
      flash('Peminjaman ditolak');
    }
  }
  header('Location: persetujuan.php');
  exit;
}
$rows = q("SELECT p.*, u.nama, a.nama_alat, a.stok FROM peminjaman p JOIN users u ON u.id=p.user_id JOIN alat a ON a.id=p.alat_id WHERE p.status='menunggu' ORDER BY p.id")->fetch_all(MYSQLI_ASSOC);
$judul = 'Menyetujui Peminjaman';
include '../inc/header.php';
?>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>Peminjam</th>
      <th>Alat</th>
      <th>Jml</th>
      <th>Stok</th>
      <th>Tgl Pinjam</th>
      <th>Rencana Kembali</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?><tr>
        <td><?= $r['id'] ?></td>
        <td><?= e($r['nama']) ?></td>
        <td><?= e($r['nama_alat']) ?></td>
        <td><?= $r['jumlah'] ?></td>
        <td><?= $r['stok'] ?></td>
        <td><?= $r['tgl_pinjam'] ?></td>
        <td><?= $r['tgl_kembali_rencana'] ?></td>
        <td>
          <form method="post"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="setuju" class="btn btn-sm btn-success">Setujui</button> <button name="tolak" class="btn btn-sm btn-danger">Tolak</button></form>
        </td>
      </tr>
    <?php endforeach;
    if (!$rows): ?><tr>
        <td colspan="8" class="text-center text-muted">Tidak ada pengajuan menunggu</td>
      </tr><?php endif ?>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>