<?php
declare(strict_types=1);
session_start();
const DB_FILE = __DIR__ . '/data/hravenhaim.sqlite';

if (!is_dir(__DIR__.'/data')) mkdir(__DIR__.'/data', 0750, true);
$db = new PDO('sqlite:'.DB_FILE);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("CREATE TABLE IF NOT EXISTS guestbook (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 name TEXT NOT NULL,
 message TEXT NOT NULL,
 created_at TEXT NOT NULL
)");
$db->exec("CREATE TABLE IF NOT EXISTS contacts (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 name TEXT NOT NULL,
 email TEXT NOT NULL,
 subject TEXT NOT NULL,
 message TEXT NOT NULL,
 created_at TEXT NOT NULL
)");
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function csrf_ok(): bool { return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']); }
function clean(string $v): string { return trim(preg_replace('/[\x00-\x1F\x7F]/u','',$v)); }
