<?php
session_start();
define('BASE', '/peminjaman-alat/');
define('DENDA_PER_HARI', 5000);
$conn = new mysqli('localhost', 'root', '', 'db_peminjaman_alat');
$conn->set_charset('utf8mb4');

function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Query prepared statement. SELECT -> mysqli_result, lainnya -> statement
function q($sql, $p = [], $t = '')
{
    global $conn;
    $st = $conn->prepare($sql);
    if ($p) {
        if ($t === '') $t = str_repeat('s', count($p));
        $st->bind_param($t, ...$p);
    }
    $st->execute();
    $r = $st->get_result();
    return $r === false ? $st : $r;
}
function cek($roles)
{
    if (empty($_SESSION['user'])) {
        header('Location: ' . BASE . 'login.php');
        exit;
    }
    if (!in_array($_SESSION['user']['role'], (array)$roles)) {
        http_response_code(403);
        die('Akses ditolak');
    }
}
function logAkt($a)
{
    if (!empty($_SESSION['user'])) q("INSERT INTO log_aktivitas(user_id,aktivitas) VALUES(?,?)", [$_SESSION['user']['id'], $a], 'is');
}
function flash($m = null)
{
    if ($m) {
        $_SESSION['flash'] = $m;
        return;
    }
    if (isset($_SESSION['flash'])) {
        echo '<div class="alert alert-info">' . e($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
}
function coba($f)
{
    try {
        $f();
    } catch (mysqli_sql_exception $ex) {
        flash('Gagal: data duplikat atau sedang dipakai data lain.');
    }
}
function opt($arr, $sel = '')
{
    foreach ($arr as $v => $l) echo '<option value="' . e($v) . '"' . ((string)$v === (string)$sel ? ' selected' : '') . '>' . e($l) . '</option>';
}
function pilih($sql)
{
    $o = [];
    foreach (q($sql) as $r) $o[$r['id']] = $r['label'];
    return $o;
}
function badge($s)
{
    $c = ['menunggu' => 'warning', 'disetujui' => 'primary', 'ditolak' => 'danger', 'dikembalikan' => 'success'][$s] ?? 'secondary';
    return '<span class="badge bg-' . $c . '">' . e($s) . '</span>';
}
function hitungDenda($rencana, $tgl)
{
    return max(0, (int)((strtotime($tgl) - strtotime($rencana)) / 86400)) * DENDA_PER_HARI;
}
// Proses pengembalian: catat, ubah status, kembalikan stok
function kembalikan($pid, $tgl, $kondisi, $cat)
{
    $p = q("SELECT * FROM peminjaman WHERE id=? AND status='disetujui'", [$pid], 'i')->fetch_assoc();
    if (!$p) return false;
    q(
        "INSERT INTO pengembalian(peminjaman_id,tgl_kembali,kondisi,denda,catatan) VALUES(?,?,?,?,?)",
        [$pid, $tgl, $kondisi, hitungDenda($p['tgl_kembali_rencana'], $tgl), $cat],
        'issis'
    );
    q("UPDATE peminjaman SET status='dikembalikan' WHERE id=?", [$pid], 'i');
    q("UPDATE alat SET stok=stok+? WHERE id=?", [$p['jumlah'], $p['alat_id']], 'ii');
    return true;
}
