<?php
function page_header(string $title, string $active=''): void { ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> · Siaga Lereng</title><link rel="stylesheet" href="assets/style.css"></head><body><div class="shell"><aside><a class="brand" href="index.php"><span class="logo">△</span>siaga<span>lereng</span></a><small class="tagline">KENALI · SIAP · SELAMAT</small><p class="navlabel">PUSAT INFORMASI</p><nav>
<?php foreach (['dashboard'=>['index.php','Ringkasan'],'education'=>['edukasi.php','Edukasi Longsor'],'guide'=>['mitigasi.php','Panduan & Mitigasi'],'incidents'=>['data.php?kind=incidents','Data Kejadian'],'people'=>['data.php?kind=people','Korban & Orang Hilang'],'shelters'=>['data.php?kind=shelters','Data Pengungsian']] as $key=>$item): ?>
<a class="<?=$active===$key?'active':''?>" href="<?=e($item[0])?>"><span>◇</span><?=e($item[1])?></a><?php endforeach; ?></nav><section class="side-card"><b>Kesiapan dimulai dari kita.</b><p>Kenali tanda bahaya dan siapkan rencana evakuasi keluarga.</p><a href="mitigasi.php">Pelajari panduan ↗</a></section><p class="local">● Aplikasi lokal · PHP + MySQL</p></aside><div class="body"><header><span>Pusat informasi / <b><?=e($title)?></b></span><div><?php if (!empty($_SESSION['user_id'])): ?><span>Admin: <?=e($_SESSION['username'])?></span><form method="post" action="logout.php"><?=csrf_input()?><button class="text-button">Keluar</button></form><?php else: ?><a href="login.php">Login admin ↗</a><?php endif; ?></div></header><main>
<?php if (!empty($_SESSION['flash'])): ?><div class="notice" role="status"><?=e($_SESSION['flash'])?></div><?php unset($_SESSION['flash']); endif; ?>
<div class="page-heading"><div><span class="eyebrow">SIAGA LERENG / INDONESIA</span><h1><?=e($title)?></h1></div></div>
<?php }
function page_footer(): void { ?>
<footer><span>Siaga Lereng · Belajar, bersiap, saling menjaga.</span><span>PHP native · Tanpa framework</span></footer></main></div></div></body></html>
<?php }
function references(): void { ?>
<section class="references"><h3>Referensi materi</h3><a target="_blank" rel="noopener" href="https://www.esdm.go.id/assets/media/content/Pengenalan_Gerakan_Tanah">[1] ESDM — Pengenalan Gerakan Tanah ↗</a><a target="_blank" rel="noopener" href="https://bnpb.go.id/berita/tanah-longsor-1">[2] BNPB — Tanah Longsor ↗</a><a target="_blank" rel="noopener" href="https://bpbd.jogjaprov.go.id/berita/mitigasi-bencana-tanah-longsor-1">[3] BPBD DIY — Mitigasi Bencana Tanah Longsor ↗</a><small>Ringkasan edukasi. Ikuti arahan resmi sesuai kondisi wilayah. Bukan kanal pelaporan darurat.</small></section>
<?php }
