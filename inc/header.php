<?php
$u = $_SESSION['user'];
$r = $u['role'];
$menu = [
    'admin' => ['admin/user.php' => 'User', 'admin/alat.php' => 'Alat', 'admin/kategori.php' => 'Kategori', 'admin/peminjaman.php' => 'Peminjaman', 'admin/pengembalian.php' => 'Pengembalian', 'admin/log.php' => 'Log Aktivitas'],
    'petugas' => ['petugas/persetujuan.php' => 'Persetujuan', 'petugas/pemantauan.php' => 'Pemantauan Pengembalian', 'petugas/laporan.php' => 'Laporan'],
    'peminjam' => ['peminjam/alat.php' => 'Daftar Alat', 'peminjam/ajukan.php' => 'Ajukan Peminjaman', 'peminjam/pengembalian.php' => 'Pengembalian'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($judul ?? 'Peminjaman Alat') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary d-print-none">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE ?>index.php">Peminjaman Alat</a>
            <ul class="navbar-nav flex-row flex-wrap gap-3 me-auto">
                <?php foreach ($menu[$r] as $f => $l): ?><li class="nav-item"><a class="nav-link" href="<?= BASE . $f ?>"><?= $l ?></a></li><?php endforeach ?>
            </ul>
            <span class="navbar-text me-3"><?= e($u['nama']) ?> (<?= $r ?>)</span>
            <a class="btn btn-sm btn-light" href="<?= BASE ?>logout.php">Logout</a>
        </div>
    </nav>
    <div class="container py-4">
        <h4 class="mb-3"><?= e($judul ?? '') ?></h4><?php flash() ?>