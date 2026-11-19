<?php
require '../config.php';
cek('admin');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  coba(function () use ($id) {
    if (isset($_POST['hapus'])) {
      if ($id == $_SESSION['user']['id']) {
        flash('Tidak bisa menghapus akun sendiri');
        return;
      }
      q("DELETE FROM users WHERE id=?", [$id], 'i');
      logAkt("Hapus user #$id");
      flash('User dihapus');
      return;
    }
    $n = $_POST['nama'];
    $u = $_POST['username'];
    $r = $_POST['role'];
    $pw = $_POST['password'];
    if ($id) {
      if ($pw != '') q("UPDATE users SET nama=?,username=?,role=?,password=? WHERE id=?", [$n, $u, $r, password_hash($pw, PASSWORD_DEFAULT), $id], 'ssssi');
      else q("UPDATE users SET nama=?,username=?,role=? WHERE id=?", [$n, $u, $r, $id], 'sssi');
      logAkt("Ubah user $u");
    } else {
      q("INSERT INTO users(nama,username,role,password) VALUES(?,?,?,?)", [$n, $u, $r, password_hash($pw, PASSWORD_DEFAULT)]);
      logAkt("Tambah user $u");
    }
    flash('Data tersimpan');
  });
  header('Location: user.php');
  exit;
}
$edit = isset($_GET['edit']) ? q("SELECT * FROM users WHERE id=?", [(int)$_GET['edit']], 'i')->fetch_assoc() : null;
$rows = q("SELECT * FROM users ORDER BY role, nama")->fetch_all(MYSQLI_ASSOC);
$judul = 'CRUD User';
include '../inc/header.php';
?>
<form method="post" class="card card-body mb-4 row g-2 flex-row">
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? 0) ?>">
  <div class="col-md-3"><input name="nama" class="form-control" placeholder="Nama" value="<?= e($edit['nama'] ?? '') ?>" required></div>
  <div class="col-md-2"><input name="username" class="form-control" placeholder="Username" value="<?= e($edit['username'] ?? '') ?>" required></div>
  <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="<?= $edit ? 'Password (kosongkan jika tidak diubah)' : 'Password' ?>" <?= $edit ? '' : 'required' ?>></div>
  <div class="col-md-2"><select name="role" class="form-select"><?php opt(['admin' => 'Admin', 'petugas' => 'Petugas', 'peminjam' => 'Peminjam'], $edit['role'] ?? '') ?></select></div>
  <div class="col-md-2"><button class="btn btn-primary"><?= $edit ? 'Update' : 'Tambah' ?></button><?php if ($edit): ?> <a href="user.php" class="btn btn-secondary">Batal</a><?php endif ?></div>
</form>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>Nama</th>
      <th>Username</th>
      <th>Role</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $i => $r): ?><tr>
        <td><?= $i + 1 ?></td>
        <td><?= e($r['nama']) ?></td>
        <td><?= e($r['username']) ?></td>
        <td><?= e($r['role']) ?></td>
        <td><a class="btn btn-sm btn-warning" href="?edit=<?= $r['id'] ?>">Edit</a>
          <form method="post" class="d-inline" onsubmit="return confirm('Hapus user ini?')"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="hapus" class="btn btn-sm btn-danger">Hapus</button></form>
        </td>
      </tr><?php endforeach ?>
  </tbody>
</table>
<?php include '../inc/footer.php'; ?>