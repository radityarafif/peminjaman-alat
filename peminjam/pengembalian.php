<?php
require '../config.php';
cek('peminjam');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = (int)$_POST['id'];
  $own = q("SELECT id FROM peminjaman WHERE id=? AND user_id=?", [$id, $_SESSION['user']['id']], 'ii')->fetch_assoc();
  if ($own && kembalikan($id, date('Y-m-d'), $_POST['kondisi'], $_POST['catatan'])) {
    logAkt("Kembalikan alat (peminjaman #$id)");
    flash('Alat berhasil dikembalikan');
  } else flash('Gagal memproses pengembalian');
  header('Location: pengembalian.php');
  exit;
}
$rows = q("SELECT p.*, a.nama_alat, g.tgl_kembali, g.denda FROM peminjaman p JOIN alat a ON a.id=p.alat_id LEFT JOIN pengembalian g ON g.peminjaman_id=p.id
  WHERE p.user_id=? AND p.status IN ('disetujui','dikembalikan') ORDER BY p.id DESC", [$_SESSION['user']['id']], 'i')->fetch_all(MYSQLI_ASSOC);
$judul = 'Mengembalikan Alat';
include '../inc/header.php';
?>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>Alat</th>
      <th>Jml</th>
      <th>Rencana Kembali</th>
      <th>Status</th>
      <th>Denda</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?><tr>
        <td><?= $r['id'] ?></td>
        <td><?= e($r['nama_alat']) ?></td>
        <td><?= $r['jumlah'] ?></td>
        <td><?= $r['tgl_kembali_rencana'] ?></td>
        <td><?= badge($r['status']) ?></td>
        <td><?= $r['denda'] !== null ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
        <td><?php if ($r['status'] == 'disetujui'): ?>
            <form method="post" class="d-flex gap-1" onsubmit="return confirm('Kembalikan alat ini?')"><input type="hidden" name="id" value="<?= $r['id'] ?>">
              <select name="kondisi" class="form-select form-select-sm"><?php opt(['baik' => 'Baik', 'rusak ringan' => 'Rusak ringan', 'rusak berat' => 'Rusak berat']) ?></select>
              <input name="catatan" class="form-control form-control-sm" placeholder="Catatan"><button class="btn btn-sm btn-success">Kembalikan</button>
            </form>
          <?php else: echo 'Dikembalikan ' . e($r['tgl_kembali']);
            endif ?>
        </td>
      </tr>
    <?php endforeach;
    if (!$rows): ?><tr>
        <td colspan="7" class="text-center text-muted">Belum ada peminjaman yang disetujui</td>
      </tr><?php endif ?>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>