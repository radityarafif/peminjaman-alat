<?php
require '../config.php';
cek('peminjam');
$cari = $_GET['cari'] ?? '';
$rows = q("SELECT a.*, k.nama_kategori FROM alat a LEFT JOIN kategori k ON k.id=a.kategori_id WHERE a.nama_alat LIKE ? ORDER BY a.nama_alat", ["%$cari%"])->fetch_all(MYSQLI_ASSOC);
$judul = 'Daftar Alat';
include '../inc/header.php';
?>
<form class="mb-3 d-flex gap-2"><input name="cari" class="form-control" placeholder="Cari alat..." value="<?= e($cari) ?>"><button class="btn btn-primary">Cari</button></form>
<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>#</th>
            <th>Alat</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Kondisi</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $i => $r): ?><tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($r['nama_alat']) ?></td>
                <td><?= e($r['nama_kategori']) ?></td>
                <td><?= $r['stok'] ?></td>
                <td><?= e($r['kondisi']) ?></td>
                <td><?php if ($r['stok'] > 0): ?><a class="btn btn-sm btn-primary" href="ajukan.php?alat=<?= $r['id'] ?>">Ajukan</a><?php else: ?><span class="text-muted">Habis</span><?php endif ?></td>
            </tr><?php endforeach ?>
    </tbody>
</table>
<?php include '../inc/footer.php'; ?>