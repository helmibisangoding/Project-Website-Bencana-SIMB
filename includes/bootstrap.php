<?php
declare(strict_types=1);
// Paket ini sengaja dibatasi ke komputer lokal karena memuat data pribadi.
// Jangan menghapus batasan ini untuk hosting tanpa peninjauan keamanan.
if (PHP_SAPI !== 'cli' && !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1','::1'], true)) {
    http_response_code(403); exit('Aplikasi latihan ini hanya dapat dibuka dari localhost.');
}
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly'=>true, 'samesite'=>'Strict', 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'none'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
header('Cache-Control: no-store');
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $c = require __DIR__.'/../config/database.php';
        $pdo = new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4", $c['username'], $c['password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
    }
    return $pdo;
}
function redirect(string $url): void { header('Location: '.$url); exit; }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_input(): string { return '<input type="hidden" name="csrf" value="'.e(csrf()).'">'; }
function check_csrf(): void {
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals(csrf(), $_POST['csrf'])) {
        http_response_code(403); exit('Sesi formulir tidak valid. Muat ulang halaman dan coba lagi.');
    }
}
function require_login(): void { if (empty($_SESSION['user_id'])) redirect('login.php'); }
function flash(string $text): void { $_SESSION['flash']=$text; }
function setup_error(): void {
    http_response_code(503);
    echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Siapkan database</title><link rel="stylesheet" href="assets/style.css"><main class="auth"><section class="panel"><h1>Database belum siap</h1><p>Jalankan MySQL di XAMPP, import <b>database/schema.sql</b> melalui phpMyAdmin, lalu periksa <b>config/database.php</b>.</p><a class="button" href="index.php">Coba lagi</a><p>Detail langkah ada di README.md.</p></section></main></html>'; exit;
}
require __DIR__.'/definitions.php';
try { db()->query('SELECT id FROM users LIMIT 1'); } catch (PDOException $ex) { setup_error(); }
