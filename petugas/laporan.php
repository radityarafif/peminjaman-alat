<?php
require '../config.php';
cek('petugas');
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$rows = q("SELECT p.*, u.nama, a.nama_alat, g.tgl_kembali, g.denda, g.kondisi FROM peminjaman p
  JOIN users u ON u.id=p.user_id JOIN alat a ON a.id=p.alat_id LEFT JOIN pengembalian g ON g.peminjaman_id=p.id
  WHERE p.tgl_pinjam BETWEEN ? AND ? ORDER BY p.tgl_pinjam", [$dari, $sampai])->fetch_all(MYSQLI_ASSOC);
$judul = 'Laporan Peminjaman';
include '../inc/header.php';
?>
<form class="row g-2 mb-3 d-print-none">
  <div class="col-auto"><input type="date" name="dari" class="form-control" value="<?= e($dari) ?>"></div>
  <div class="col-auto"><input type="date" name="sampai" class="form-control" value="<?= e($sampai) ?>"></div>
  <div class="col-auto"><button class="btn btn-primary">Tampilkan</button> <button type="button" class="btn btn-success" onclick="window.print()">Cetak</button></div>
</form>
<p>Periode: <?= e($dari) ?> s/d <?= e($sampai) ?></p>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>No</th>
      <th>Peminjam</th>
      <th>Alat</th>
      <th>Jml</th>
      <th>Tgl Pinjam</th>
      <th>Rencana</th>
      <th>Tgl Kembali</th>
      <th>Status</th>
      <th>Denda</th>
    </tr>
  </thead>
  <tbody>
    <?php $tot = 0;
    foreach ($rows as $i => $r): $tot += (int)$r['denda']; ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= e($r['nama']) ?></td>
        <td><?= e($r['nama_alat']) ?></td>
        <td><?= $r['jumlah'] ?></td>
        <td><?= $r['tgl_pinjam'] ?></td>
        <td><?= $r['tgl_kembali_rencana'] ?></td>
        <td><?= $r['tgl_kembali'] ?? '-' ?></td>
        <td><?= e($r['status']) ?></td>
        <td>Rp <?= number_format((int)$r['denda'], 0, ',', '.') ?></td>
      </tr>
    <?php endforeach ?>
    <tr>
      <th colspan="8" class="text-end">Total denda</th>
      <th>Rp <?= number_format($tot, 0, ',', '.') ?></th>
    </tr>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>