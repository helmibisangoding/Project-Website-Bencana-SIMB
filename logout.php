<?php
require __DIR__.'/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); exit('Gunakan tombol Keluar.'); }
check_csrf(); $_SESSION=[]; session_regenerate_id(true); redirect('login.php');
