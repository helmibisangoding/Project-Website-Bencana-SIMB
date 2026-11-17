<?php
require __DIR__.'/includes/bootstrap.php';
require __DIR__.'/includes/layout.php';
if ((int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn()>0) redirect('login.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf();
 $username=is_string($_POST['username']??null)?trim($_POST['username']):'';
 $password=is_string($_POST['password']??null)?$_POST['password']:'';
 $confirm=is_string($_POST['confirm']??null)?$_POST['confirm']:'';
 if (!preg_match('/^[a-zA-Z0-9_]{3,40}$/',$username)) $error='Username: 3–40 huruf, angka, atau garis bawah.';
 elseif (strlen($password)<10 || strlen($password)>72) $error='Password harus 10–72 byte.';
 elseif ($password!==$confirm) $error='Konfirmasi password berbeda.';
 else {
  // ID tetap mencegah dua setup bersamaan membuat dua admin awal.
  try { $q=db()->prepare('INSERT INTO users (id,username,password_hash) VALUES (1,?,?)'); $q->execute([$username,password_hash($password,PASSWORD_DEFAULT)]); flash('Admin dibuat. Silakan login.'); redirect('login.php'); }
  catch (PDOException $ex) { $error='Admin sudah dibuat atau database gagal menyimpan. Silakan buka halaman login.'; }
 }
}
page_header('Buat admin pertama'); ?>
<section class="panel narrow"><h2>Selamat datang di Siaga Lereng</h2><p>Buat akun admin lokal Anda. Tidak ada password bawaan. Halaman ini otomatis terkunci setelah admin pertama dibuat.</p><?php if($error): ?><div class="error"><?=e($error)?></div><?php endif; ?><form method="post"><?=csrf_input()?><label>Username<input name="username" pattern="[a-zA-Z0-9_]{3,40}" maxlength="40" required autocomplete="username"></label><label>Password (minimal 10 karakter ASCII)<input type="password" name="password" minlength="10" maxlength="72" required autocomplete="new-password"></label><label>Ulangi password<input type="password" name="confirm" required autocomplete="new-password"></label><button>Buat admin</button></form></section><?php page_footer(); ?>
