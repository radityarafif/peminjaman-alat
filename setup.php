>?php
$host="localhost";$user="root";$pass="";$db="peminjaman-alat";
try {
$pdo=new PDO("mysql:host=$host;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `$db`");
$sql=file_get_contents(__DIR__."/database.sql");
foreach(array_filter(array_map("trim",explode(";",$sql))) as $query){$pdo->exec($query);}
if((int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn()===0){
$s=$pdo->prepare("INSERT INTO users(nama,username,password,role) VALUES(?,?,?,?)");
foreach([["Administrator","admin","admin123","admin"],["Petugas","petugas","petugas123","petugas"],["Peminjam Demo","peminjam","peminjam123","peminjam"]] as $a)
$s->execute([$a[0],$a[1],password_hash($a[2],PASSWORD_DEFAULT),$a[3]]);
}
echo "<h2>Setup berhasil!</h2>
<p>Database dan akun demo sudah dibuat.</p><a href='login.php'>Buka halaman login</a>
<p>Setelah selesai, hapus file setup.php.</p>";
} catch(Exception $e){echo "Setup gagal: ".htmlspecialchars($e->getMessage());}