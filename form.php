<?php
require __DIR__.'/includes/bootstrap.php'; require_login(); require __DIR__.'/includes/layout.php';require __DIR__.'/includes/records.php';
$kind=selected_kind();$def=definitions()[$kind];$id=filter_var($_GET['id']??0,FILTER_VALIDATE_INT);
if($id===false || $id<0){http_response_code(400);exit('ID tidak valid.');}
$row=[];$error='';
if($id){$q=db()->prepare("SELECT * FROM $kind WHERE id=?");$q->execute([$id]);$row=$q->fetch();if(!$row){http_response_code(404);exit('Catatan tidak ditemukan.');}}
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();$row=$_POST;
 try {
  $clean=validate_record($kind,$_POST,db());
  // Hindari posko berpindah kejadian sementara masih menampung registrasi individu kejadian lama.
  if($kind==='shelters' && $id){$q=db()->prepare('SELECT COUNT(*) FROM people WHERE shelter_id=? AND incident_id<>?');$q->execute([$id,$clean['incident_id']]);if((int)$q->fetchColumn())throw new InvalidArgumentException('Posko masih terkait orang dari kejadian lama. Pindahkan registrasi orang tersebut terlebih dahulu.');}
  $keys=array_keys($clean);
  if($id){$sql="UPDATE $kind SET ".implode(',',array_map(static function($k){return "$k=?";},$keys)).' WHERE id=?';$values=array_values($clean);$values[]=$id;}
  else {$sql="INSERT INTO $kind (".implode(',',$keys).') VALUES ('.implode(',',array_fill(0,count($keys),'?')).')';$values=array_values($clean);}
  $q=db()->prepare($sql);$q->execute($values);flash('Catatan berhasil disimpan.');redirect('data.php?kind='.$kind);
 }catch(InvalidArgumentException $ex){$error=$ex->getMessage();}
 catch(PDOException $ex){$error='Penyimpanan gagal. Periksa data terkait dan koneksi database.';}
}
$incidents=db()->query('SELECT id,name FROM incidents ORDER BY event_date DESC,id DESC')->fetchAll();
$shelters=db()->query('SELECT s.id,s.name,i.name incident_name FROM shelters s JOIN incidents i ON i.id=s.incident_id ORDER BY s.name')->fetchAll();
page_header(($id?'Ubah ':'Tambah ').$def['singular'],$kind); ?>
<section class="panel"><div class="section-heading"><p>Kolom bertanda * wajib diisi.</p><a href="data.php?kind=<?=e($kind)?>">← Kembali</a></div><?php if($error): ?><div class="error" role="alert"><?=e($error)?></div><?php endif; ?><?php if($kind!=='incidents'&&!$incidents): ?><div class="notice">Tambahkan <a href="form.php?kind=incidents">data kejadian</a> terlebih dahulu.</div><?php endif; ?><form method="post"><?=csrf_input()?><div class="formgrid">
<?php foreach($def['fields'] as $key=>$f): $type=$f[1];$optional=in_array($type,['textarea','shelter'],true);$value=$row[$key]??($type==='number'?0:''); if(!is_scalar($value)&&$value!==null)$value=''; ?>
<label class="<?=$type==='textarea'?'full':''?>"><?=e($f[0])?><?=$optional?'':' *'?>
<?php if($type==='textarea'): ?><textarea name="<?=e($key)?>" rows="4" maxlength="2000"><?=e($value)?></textarea>
<?php elseif(in_array($type,['select','incident','shelter'],true)): ?><select name="<?=e($key)?>" <?=$optional?'':'required'?>>
<?php if($type==='select'): foreach($f[2] as $opt): ?><option value="<?=e($opt)?>" <?=(string)$value===$opt?'selected':''?>><?=e($opt)?></option><?php endforeach;
else: ?><option value=""><?=$type==='shelter'?'Tidak terhubung':'Pilih kejadian'?></option><?php foreach($type==='incident'?$incidents:$shelters as $opt): ?><option value="<?=(int)$opt['id']?>" <?=(string)$value===(string)$opt['id']?'selected':''?>><?=e($opt['name'].($type==='shelter'?' — '.$opt['incident_name']:''))?></option><?php endforeach; endif; ?></select>
<?php else: ?><input type="<?=e($type)?>" name="<?=e($key)?>" value="<?=e($value)?>" required <?=$type==='number'?'min="0" max="'.($key==='age'?120:10000000).'" step="1"':'maxlength="200"'?>><?php endif; ?></label><?php endforeach; ?></div><p class="small">Rekap jumlah korban / penghuni tidak otomatis disinkronkan dengan registrasi individu. Periksa sumber sebelum menyimpan.</p><div class="actions"><button <?=$kind!=='incidents'&&!$incidents?'disabled':''?>>Simpan ke database</button><a class="button secondary" href="data.php?kind=<?=e($kind)?>">Batal</a></div></form></section><?php page_footer(); ?>
