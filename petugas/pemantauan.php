<?php
require '../config.php';
cek('petugas');
$aktif = q("SELECT p.*, u.nama, a.nama_alat FROM peminjaman p JOIN users u ON u.id=p.user_id JOIN alat a ON a.id=p.alat_id WHERE p.status='disetujui' ORDER BY p.tgl_kembali_rencana")->fetch_all(MYSQLI_ASSOC);
$sudah = q("SELECT g.*, u.nama, a.nama_alat, p.tgl_kembali_rencana FROM pengembalian g JOIN peminjaman p ON p.id=g.peminjaman_id JOIN users u ON u.id=p.user_id JOIN alat a ON a.id=p.alat_id ORDER BY g.id DESC LIMIT 50")->fetch_all(MYSQLI_ASSOC);
$judul = 'Memantau Pengembalian';
include '../inc/header.php';
?>
<h6>Sedang dipinjam</h6>
<table class="table table-bordered bg-white mb-4">
    <thead>
        <tr>
            <th>Peminjam</th>
            <th>Alat</th>
            <th>Jml</th>
            <th>Tgl Pinjam</th>
            <th>Rencana Kembali</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($aktif as $r): $tel = (int)((strtotime(date('Y-m-d')) - strtotime($r['tgl_kembali_rencana'])) / 86400); ?>
            <tr class="<?= $tel > 0 ? 'table-danger' : '' ?>">
                <td><?= e($r['nama']) ?></td>
                <td><?= e($r['nama_alat']) ?></td>
                <td><?= $r['jumlah'] ?></td>
                <td><?= $r['tgl_pinjam'] ?></td>
                <td><?= $r['tgl_kembali_rencana'] ?></td>
                <td><?= $tel > 0 ? "Terlambat $tel hari" : 'Masih dalam waktu' ?></td>
            </tr>
        <?php endforeach;
        if (!$aktif): ?><tr>
                <td colspan="6" class="text-center text-muted">Tidak ada peminjaman aktif</td>
            </tr><?php endif ?>
    </tbody>
</table>
<h6>Riwayat pengembalian</h6>
<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>Peminjam</th>
            <th>Alat</th>
            <th>Rencana</th>
            <th>Kembali</th>
            <th>Kondisi</th>
            <th>Denda</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sudah as $r): ?><tr>
                <td><?= e($r['nama']) ?></td>
                <td><?= e($r['nama_alat']) ?></td>
                <td><?= $r['tgl_kembali_rencana'] ?></td>
                <td><?= $r['tgl_kembali'] ?></td>
                <td><?= e($r['kondisi']) ?></td>
                <td>Rp <?= number_format($r['denda'], 0, ',', '.') ?></td>
            </tr><?php endforeach ?>
    </tbody>
</table>
<?php include '../inc/footer.php'; ?>