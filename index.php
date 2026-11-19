<?php
require 'config.php';
cek(['admin', 'petugas', 'peminjam']);
$go = ['admin' => 'admin/user.php', 'petugas' => 'petugas/persetujuan.php', 'peminjam' => 'peminjam/alat.php'];
header('Location: ' . BASE . $go[$_SESSION['user']['role']]);
