<?php
require __DIR__.'/includes/bootstrap.php';require_login();require __DIR__.'/includes/layout.php';require __DIR__.'/includes/records.php';
$kind=selected_kind();$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);if(!$id||$id<1){http_response_code(400);exit('ID tidak valid.');}
$q=db()->prepare("SELECT * FROM $kind WHERE id=?");$q->execute([$id]);$row=$q->fetch();if(!$row){http_response_code(404);exit('Catatan tidak ditemukan.');}
$error='';if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();try{$q=db()->prepare("DELETE FROM $kind WHERE id=?");$q->execute([$id]);flash('Catatan berhasil dihapus.');redirect('data.php?kind='.$kind);}catch(PDOException $ex){$error='Catatan masih memiliki data terkait atau gagal dihapus. Hapus/pindahkan registrasi orang dan posko terkait terlebih dahulu.';}
}
page_header('Konfirmasi hapus',$kind); ?>
<section class="panel narrow"><h2>Hapus “<?=e($row['name'])?>”?</h2><p>Tindakan ini menghapus satu catatan secara permanen. Data induk tidak dapat dihapus selama masih memiliki catatan terkait.</p><?php if($error): ?><div class="error"><?=e($error)?></div><?php endif; ?><form method="post"><?=csrf_input()?><div class="actions"><button class="danger">Ya, hapus catatan</button><a class="button secondary" href="data.php?kind=<?=e($kind)?>">Batal</a></div></form></section><?php page_footer(); ?>
