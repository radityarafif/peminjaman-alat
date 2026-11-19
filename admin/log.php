<?php
require '../config.php';
cek('admin');
$rows = q("SELECT l.*, u.nama, u.role FROM log_aktivitas l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 300")->fetch_all(MYSQLI_ASSOC);
$judul = 'Log Aktivitas';
include '../inc/header.php';
?>
<table class="table table-sm table-bordered bg-white">
    <thead>
        <tr>
            <th>Waktu</th>
            <th>User</th>
            <th>Role</th>
            <th>Aktivitas</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?><tr>
                <td><?= $r['waktu'] ?></td>
                <td><?= e($r['nama'] ?? '(dihapus)') ?></td>
                <td><?= e($r['role']) ?></td>
                <td><?= e($r['aktivitas']) ?></td>
            </tr><?php endforeach ?>
    </tbody>
</table>
<?php include '../inc/footer.php'; ?>