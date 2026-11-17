<?php
require __DIR__.'/includes/bootstrap.php'; require __DIR__.'/includes/layout.php';
if (!empty($_SESSION['user_id'])) redirect('index.php');
if ((int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn()===0) redirect('setup.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf();
 if (($_SESSION['retry_after']??0)>time()) $error='Terlalu banyak percobaan. Tunggu 60 detik lalu coba lagi.';
 else {
  $username=is_string($_POST['username']??null)?trim($_POST['username']):'';
  $password=is_string($_POST['password']??null)?$_POST['password']:'';
  $q=db()->prepare('SELECT * FROM users WHERE username=?');$q->execute([$username]);$u=$q->fetch();
  if ($u && password_verify($password,$u['password_hash'])) {
   session_regenerate_id(true); $_SESSION=['user_id'=>(int)$u['id'],'username'=>$u['username']]; csrf(); redirect('index.php');
  }
  $_SESSION['attempts']=($_SESSION['attempts']??0)+1;
  if ($_SESSION['attempts']>=5) { $_SESSION['retry_after']=time()+60;$_SESSION['attempts']=0; }
  $error='Username atau password tidak sesuai.';
 }
}
page_header('Login admin'); ?>
<section class="panel narrow"><span class="eyebrow">RUANG PENDATAAN LOKAL</span><h2>Masuk untuk mengelola data.</h2><p>Materi edukasi dapat dibaca tanpa login. Data kejadian, korban, dan posko memerlukan akun admin.</p><?php if($error): ?><div class="error" role="alert"><?=e($error)?></div><?php endif; ?><form method="post"><?=csrf_input()?><label>Username<input name="username" required maxlength="60" autocomplete="username"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button>Masuk</button></form><p class="small">Lupa password? Lihat prosedur reset lokal di README.md.</p></section><?php page_footer(); ?>
