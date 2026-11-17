<?php
function selected_kind(): string {
 $k=is_string($_GET['kind']??null)?$_GET['kind']:'incidents';
 if (!array_key_exists($k,definitions())) { http_response_code(404); exit('Jenis data tidak ditemukan.'); } return $k;
}
function filtered_records(string $kind): array {
 $search=is_string($_GET['q']??null)?trim($_GET['q']):'';
 $year=is_string($_GET['year']??null)?$_GET['year']:'';
 if ($year!=='' && !preg_match('/^\d{4}$/',$year)) $year='';
 $sql="SELECT t.*"; $from=" FROM $kind t";
 if ($kind!=='incidents') { $sql.=', i.name AS incident_name'; $from.=' JOIN incidents i ON i.id=t.incident_id'; }
 if ($kind==='people') { $sql.=', s.name AS shelter_name'; $from.=' LEFT JOIN shelters s ON s.id=t.shelter_id'; }
 $where=[];$params=[];
 if ($search!=='') {
  $cols=$kind==='incidents'?['t.name','t.province','t.district','t.location','t.type']:($kind==='people'?['t.name','t.status','t.last_location','i.name']:['t.name','t.location','t.needs','i.name']);
  $parts=[];foreach($cols as $col) { $parts[]="$col LIKE ?"; $params[]='%'.$search.'%'; } $where[]='('.implode(' OR ',$parts).')';
 }
 if ($year!=='') { $col=$kind==='incidents'?'t.event_date':'i.event_date'; $where[]="YEAR($col)=?";$params[]=$year; }
 $sql.=$from.($where?' WHERE '.implode(' AND ',$where):'').' ORDER BY '.($kind==='incidents'?'t.event_date DESC, ':'').'t.id DESC';
 $q=db()->prepare($sql);$q->execute($params);return $q->fetchAll();
}
