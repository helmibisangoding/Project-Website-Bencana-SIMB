<?php
// Alat pemulihan lokal: tidak dapat dipanggil melalui HTTP.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Hanya CLI.'); }
$c=require __DIR__.'/../config/database.php';
echo "Username admin: ";$username=trim(fgets(STDIN));
echo "Password baru (terlihat, 10-72 byte): ";$password=rtrim(fgets(STDIN),"\r\n");
if(strlen($password)<10||strlen($password)>72){fwrite(STDERR,"Panjang password tidak valid.\n");exit(1);}
try{
 $pdo=new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4",$c['username'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false]);
 $q=$pdo->prepare('UPDATE users SET password_hash=? WHERE username=?');$q->execute([password_hash($password,PASSWORD_DEFAULT),$username]);
 if(!$q->rowCount()){fwrite(STDERR,"Username tidak ditemukan.\n");exit(1);}echo "Password diperbarui.\n";
}catch(PDOException $ex){fwrite(STDERR,"Gagal mengakses database. Periksa konfigurasi.\n");exit(1);}
