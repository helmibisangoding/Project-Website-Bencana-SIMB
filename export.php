<?php
require __DIR__.'/includes/bootstrap.php';require_login();require __DIR__.'/includes/records.php';
$kind=selected_kind();$rows=filtered_records($kind);$fields=definitions()[$kind]['fields'];
// Spreadsheet formula injection: awalan berbahaya dinetralkan sebagai teks.
function csv_safe($value): string { $s=(string)($value??'');return preg_match('/^[\s]*[=+@-]/u',$s)||preg_match('/^[\t\r\n]/',$s)?"'".$s:$s; }
header('Content-Type: text/csv; charset=UTF-8');header('Content-Disposition: attachment; filename="siaga-lereng-'.$kind.'-'.date('Y-m-d').'.csv"');
$out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");
fputcsv($out,array_merge(['ID'],array_map(static function($f){return $f[0];},array_values($fields))),',','"','');
foreach($rows as $row){$values=[$row['id']];foreach($fields as $key=>$f)$values[]=csv_safe($row[$key]);fputcsv($out,$values,',','"','');}fclose($out);exit;
