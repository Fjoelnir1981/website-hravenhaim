<?php
require __DIR__.'/bootstrap.php';
require __DIR__.'/includes/layout.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=clean(field('name'));$message=clean(field('message'));
    $result=['ok'=>false,'message'=>''];
    if(!csrf_ok())$result['message']='Deine Sitzung ist abgelaufen. Bitte lade die Seite neu.';
    elseif(!$name||mb_strlen($name)>80||!$message||mb_strlen($message)>1000)$result['message']='Bitte prüfe deinen Namen und deinen Gruß (höchstens 1.000 Zeichen).';
    elseif(field('privacy')!=='1')$result['message']='Bitte stimme der Veröffentlichung deines Namens und Grußes zu.';
    elseif(field('website')!=='')$result['message']='Der Eintrag konnte nicht angenommen werden.';
    elseif(time()-($_SESSION['guest_time']??0)<30)$result['message']='Bitte warte einen Moment, bevor du einen weiteren Gruß hinterlässt.';
    else {try{database()->prepare('INSERT INTO guestbook(name,message,created_at) VALUES(?,?,?)')->execute([$name,$message,date(DATE_ATOM)]);$_SESSION['guest_time']=time();$result=['ok'=>true,'message'=>'Danke für deinen Gruß! Dein Eintrag wurde veröffentlicht.'];}catch(Throwable $error){$result['message']='Der Eintrag konnte nicht gespeichert werden. Bitte versuche es später erneut.';}}
    $_SESSION['guest_result']=$result;
    $_SESSION['guest_values']=$result['ok']?[]:['name'=>$name,'message'=>$message];
    header('Location: gaestebuch.php',true,303);exit;
}
$result=$_SESSION['guest_result']??null;unset($_SESSION['guest_result']);
$values=$_SESSION['guest_values']??[];unset($_SESSION['guest_values']);
try{$entries=database()->query('SELECT name,message,created_at FROM guestbook ORDER BY id DESC LIMIT 50')->fetchAll();$available=true;}catch(Throwable $error){$entries=[];$available=false;}
page_start('Gästebuch');
?>
<p class="intro">Hinterlasse einen Gruß. Wir freuen uns, dass du hier bist.</p><div class="content-grid two"><section class="panel content-card"><h2>Dein Gruß an die Sippe</h2>
<?php if($result): ?><p class="notice <?= $result['ok']?'success':'error' ?>" role="status"><?= e($result['message']) ?></p><?php endif; ?>
<form method="post" action="gaestebuch.php"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><label for="guest-name">Name *<input id="guest-name" name="name" maxlength="80" required value="<?= e($values['name']??'') ?>"></label><label for="guest-message">Dein Gruß *<textarea id="guest-message" name="message" maxlength="1000" required><?= e($values['message']??'') ?></textarea></label><div class="honeypot" aria-hidden="true"><label>Website<input name="website" autocomplete="off" tabindex="-1"></label></div><label class="consent"><input type="checkbox" name="privacy" value="1" required> <span>Ich stimme der öffentlichen Anzeige meines Namens und Grußes zu. <a href="datenschutz.html">Datenschutz</a></span></label><button class="btn">Gruß hinterlassen ›</button></form></section><section aria-label="Grüße aus dem Gästebuch">
<?php foreach($entries as $entry): ?><article class="guest-entry"><h2><?= e($entry['name']) ?></h2><time datetime="<?= e($entry['created_at']) ?>"><?= e(date('d.m.Y',strtotime($entry['created_at']))) ?></time><p><?= e($entry['message']) ?></p></article><?php endforeach; ?>
<?php if(!$entries): ?><p class="notice"><?= $available?'Noch keine Einträge. Sei der Erste!':'Das Gästebuch ist vorübergehend nicht erreichbar.' ?></p><?php endif; ?></section></div>
<?php page_end(); ?>
