<?php
declare(strict_types=1);
ini_set('display_errors', '0');
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off','path'=>'/']);
session_start();
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store');
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function field(string $key): string { return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : ''; }
function csrf_ok(): bool { return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], field('csrf')); }
function clean(string $value): string { return trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? ''); }
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
function database(): PDO {
    static $db = null;
    if ($db instanceof PDO) return $db;
    $path = getenv('HRAVEN_DB_PATH') ?: __DIR__.'/data/hravenhaim.sqlite';
    if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0700, true) && !is_dir(dirname($path))) throw new RuntimeException('Storage unavailable');
    $db = new PDO('sqlite:'.$path, null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $db->exec('PRAGMA busy_timeout = 5000');
    $db->exec('CREATE TABLE IF NOT EXISTS guestbook (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, message TEXT NOT NULL, created_at TEXT NOT NULL)');
    $db->exec('CREATE TABLE IF NOT EXISTS contacts (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL, subject TEXT NOT NULL, message TEXT NOT NULL, created_at TEXT NOT NULL)');
    $columns = array_column($db->query('PRAGMA table_info(contacts)')->fetchAll(), 'name');
    foreach (['delivery_status'=>"TEXT NOT NULL DEFAULT 'not_sent'",'sent_at'=>'TEXT'] as $column=>$definition) {
        if (!in_array($column,$columns,true)) $db->exec("ALTER TABLE contacts ADD COLUMN $column $definition");
    }
    if (is_file($path)) chmod($path,0600);
    return $db;
}
function process_contact(): array {
    if (!csrf_ok()) return ['ok'=>false,'stored'=>false,'code'=>403,'message'=>'Deine Sitzung ist abgelaufen. Bitte lade die Seite neu.'];
    $name=clean(field('name')); $email=field('email'); $subject=clean(field('subject')); $message=clean(field('message'));
    if (!$name || mb_strlen($name)>80 || preg_match('/[\r\n]/',$name) || strlen($email)>254 || !filter_var($email,FILTER_VALIDATE_EMAIL) || !$subject || mb_strlen($subject)>150 || preg_match('/[\r\n]/',$subject) || !$message || mb_strlen($message)>3000) return ['ok'=>false,'stored'=>false,'code'=>422,'message'=>'Bitte fülle Name, gültige E-Mail-Adresse, Betreff und Nachricht korrekt aus.'];
    if (field('privacy') !== '1') return ['ok'=>false,'stored'=>false,'code'=>422,'message'=>'Bitte stimme der Verarbeitung deiner Angaben zu.'];
    if (field('website') !== '') return ['ok'=>false,'stored'=>false,'code'=>422,'message'=>'Die Anfrage konnte nicht angenommen werden.'];
    $fingerprint=hash('sha256',json_encode([$name,$email,$subject,$message]));
    if (isset($_SESSION['last_contact']) && $_SESSION['last_contact']['fingerprint']===$fingerprint && time()-$_SESSION['last_contact']['time']<300) return $_SESSION['last_contact']['result'];
    if (time()-($_SESSION['contact_time']??0)<20) return ['ok'=>false,'stored'=>false,'code'=>429,'message'=>'Bitte warte einen Moment, bevor du erneut eine Anfrage sendest.'];
    try {
        $db=database();
        $statement=$db->prepare('INSERT INTO contacts(name,email,subject,message,created_at,delivery_status) VALUES(?,?,?,?,?,?)');
        $statement->execute([$name,$email,$subject,$message,date(DATE_ATOM),'pending']);
        $id=(int)$db->lastInsertId();
        require_once __DIR__.'/includes/mailer.php';
        $delivery=send_contact_mail($name,$email,$subject,$message);
        $db->prepare('UPDATE contacts SET delivery_status=?,sent_at=? WHERE id=?')->execute([$delivery,$delivery==='sent'?date(DATE_ATOM):null,$id]);
        $_SESSION['contact_time']=time();
        $result=['ok'=>$delivery==='sent','stored'=>true,'code'=>200,'message'=>$delivery==='sent'?'Danke! Deine Anfrage wurde gespeichert und per E-Mail an die Sippe übermittelt.':'Deine Anfrage wurde gespeichert. Der E-Mail-Versand ist derzeit nicht verfügbar; eine Zustellung wurde nicht bestätigt.'];
        $_SESSION['last_contact']=['fingerprint'=>$fingerprint,'time'=>time(),'result'=>$result];
        return $result;
    } catch (Throwable $error) {
        error_log('Hravenhaim: Kontaktverarbeitung fehlgeschlagen ('.get_class($error).').');
        return ['ok'=>false,'stored'=>isset($id),'code'=>503,'message'=>isset($id)?'Deine Anfrage wurde gespeichert, aber der Versand konnte nicht bestätigt werden.':'Die Anfrage konnte nicht gespeichert werden. Bitte versuche es später erneut.'];
    }
}
