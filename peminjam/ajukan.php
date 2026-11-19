<?php
require '../config.php';
cek('peminjam');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $a = q("SELECT * FROM alat WHERE id=?", [(int)$_POST['alat_id']], 'i')->fetch_assoc();
  $j = (int)$_POST['jumlah'];
  if (!$a || $j < 1 || $j > $a['stok']) flash('Jumlah melebihi stok tersedia');
  elseif ($_POST['tgl_kembali_rencana'] < $_POST['tgl_pinjam']) flash('Tanggal kembali tidak boleh sebelum tanggal pinjam');
  else {
    q(
      "INSERT INTO peminjaman(user_id,alat_id,jumlah,tgl_pinjam,tgl_kembali_rencana) VALUES(?,?,?,?,?)",
      [$_SESSION['user']['id'], $a['id'], $j, $_POST['tgl_pinjam'], $_POST['tgl_kembali_rencana']],
      'iiiss'
    );
    logAkt("Ajukan peminjaman {$a['nama_alat']} x$j");
    flash('Pengajuan terkirim, menunggu persetujuan petugas');
  }
  header('Location: ajukan.php');
  exit;
}
$al = pilih("SELECT id, CONCAT(nama_alat,' (stok ',stok,')') AS label FROM alat WHERE stok>0 ORDER BY nama_alat");
$riwayat = q("SELECT p.*, a.nama_alat FROM peminjaman p JOIN alat a ON a.id=p.alat_id WHERE p.user_id=? ORDER BY p.id DESC", [$_SESSION['user']['id']], 'i')->fetch_all(MYSQLI_ASSOC);
$judul = 'Ajukan Peminjaman';
include '../inc/header.php';
?>
<form method="post" class="card card-body mb-4 row g-2 flex-row">
  <div class="col-md-4"><label class="form-label">Alat</label><select name="alat_id" class="form-select"><?php opt($al, $_GET['alat'] ?? '') ?></select></div>
  <div class="col-md-2"><label class="form-label">Jumlah</label><input type="number" min="1" name="jumlah" class="form-control" value="1" required></div>
  <div class="col-md-2"><label class="form-label">Tgl pinjam</label><input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
  <div class="col-md-2"><label class="form-label">Tgl kembali</label><input type="date" name="tgl_kembali_rencana" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required></div>
  <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Ajukan</button></div>
</form>
<h6>Riwayat pengajuan saya</h6>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>Alat</th>
      <th>Jml</th>
      <th>Tgl Pinjam</th>
      <th>Rencana Kembali</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($riwayat as $r): ?><tr>
        <td><?= $r['id'] ?></td>
        <td><?= e($r['nama_alat']) ?></td>
        <td><?= $r['jumlah'] ?></td>
        <td><?= $r['tgl_pinjam'] ?></td>
        <td><?= $r['tgl_kembali_rencana'] ?></td>
        <td><?= badge($r['status']) ?></td>
      </tr><?php endforeach ?>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>