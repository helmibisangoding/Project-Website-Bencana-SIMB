<?php
const LANDSLIDE_TYPES = ['Longsoran Translasi','Longsoran Rotasi','Pergerakan Blok','Runtuhan Batu','Rayapan Tanah','Aliran Bahan Rombakan'];
// Metadata formulir adalah allowlist kolom; nama tabel/kolom tidak berasal dari pengguna.
function definitions(): array {
 return [
 'incidents'=>['title'=>'Data Kejadian','singular'=>'kejadian','fields'=>[
 'name'=>['Nama kejadian','text'], 'event_date'=>['Tanggal kejadian','date'],
 'province'=>['Provinsi','text'], 'district'=>['Kabupaten / kota','text'], 'location'=>['Lokasi / desa','text'],
 'type'=>['Jenis longsor','select',LANDSLIDE_TYPES], 'status'=>['Status penanganan','select',['Penanganan','Pemulihan','Selesai']],
 'deaths'=>['Meninggal (orang)','number'], 'injured'=>['Luka-luka (orang)','number'], 'missing'=>['Hilang (orang)','number'], 'damaged_houses'=>['Rumah rusak (unit)','number'], 'notes'=>['Catatan / sumber laporan','textarea']]],
 'shelters'=>['title'=>'Data Pengungsian','singular'=>'posko','fields'=>[
 'incident_id'=>['Kejadian terkait','incident'], 'name'=>['Nama posko','text'], 'location'=>['Alamat posko / lokasi aman','text'],
 'capacity'=>['Kapasitas (orang)','number'], 'occupancy'=>['Penghuni saat ini (orang)','number'], 'needs'=>['Kebutuhan logistik','textarea']]],
 'people'=>['title'=>'Korban & Orang Hilang','singular'=>'orang','fields'=>[
 'incident_id'=>['Kejadian terkait','incident'], 'shelter_id'=>['Posko terkait (opsional, harus satu kejadian)','shelter'], 'name'=>['Nama / kode identitas','text'], 'age'=>['Usia (tahun)','number'],
 'status'=>['Status individu','select',['Hilang','Ditemukan selamat','Luka-luka','Meninggal','Mengungsi']], 'last_location'=>['Lokasi terakhir / ditemukan','text'], 'notes'=>['Catatan pendataan','textarea']]],
 ];
}
function validate_record(string $kind, array $input, PDO $pdo): array {
 $fields=definitions()[$kind]['fields']; $clean=[];
 foreach ($fields as $key=>$field) {
  $v=$input[$key] ?? ''; if (!is_scalar($v)) throw new InvalidArgumentException('Format isian tidak valid.'); $v=trim((string)$v);
  if ($field[1]==='shelter' && $v==='') { $clean[$key]=null; continue; }
  if ($v==='' && $field[1]!=='textarea') throw new InvalidArgumentException($field[0].' wajib diisi.');
  if (in_array($field[1],['number','incident','shelter'],true)) {
   $n=filter_var($v,FILTER_VALIDATE_INT); $min=$field[1]==='number'?0:1;
   if ($n===false || $n<$min || $n>($key==='age'?120:10000000)) throw new InvalidArgumentException('Nilai '.$field[0].' tidak valid.');
   $clean[$key]=$n;
  } elseif ($field[1]==='select') {
   if (!in_array($v,$field[2],true)) throw new InvalidArgumentException('Pilihan '.$field[0].' tidak valid.'); $clean[$key]=$v;
  } elseif ($field[1]==='date') {
   $date=DateTimeImmutable::createFromFormat('!Y-m-d',$v);
   if (!$date || $date->format('Y-m-d')!==$v) throw new InvalidArgumentException('Tanggal tidak valid.'); $clean[$key]=$v;
  } else {
   // Batas byte konservatif juga aman untuk kolom UTF-8.
   $limit=$field[1]==='textarea'?2000:(in_array($key,['province','district'],true)?100:200);
   if (strlen($v)>$limit) throw new InvalidArgumentException($field[0].' terlalu panjang.'); $clean[$key]=$v;
  }
 }
 if ($kind!=='incidents') {
  $q=$pdo->prepare('SELECT id FROM incidents WHERE id=?'); $q->execute([$clean['incident_id']]);
  if (!$q->fetch()) throw new InvalidArgumentException('Kejadian terkait tidak ditemukan.');
 }
 if ($kind==='shelters' && $clean['occupancy']>$clean['capacity']) throw new InvalidArgumentException('Jumlah penghuni melebihi kapasitas. Koreksi kapasitas atau jumlah penghuni.');
 if ($kind==='people' && $clean['shelter_id']!==null) {
  $q=$pdo->prepare('SELECT incident_id FROM shelters WHERE id=?'); $q->execute([$clean['shelter_id']]); $s=$q->fetch();
  if (!$s || (int)$s['incident_id']!==$clean['incident_id']) throw new InvalidArgumentException('Posko harus terkait dengan kejadian yang sama.');
 }
 return $clean;
}
