<?php
require __DIR__.'/bootstrap.php';
require __DIR__.'/includes/layout.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $result=process_contact();
    if (str_contains($_SERVER['HTTP_ACCEPT']??'', 'application/json')) {
        http_response_code($result['code']);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result,JSON_UNESCAPED_UNICODE);exit;
    }
    $_SESSION['contact_result']=$result;
    $_SESSION['contact_values']=$result['stored']?[]:['name'=>field('name'),'email'=>field('email'),'subject'=>field('subject'),'message'=>field('message')];
    header('Location: kontakt.php',true,303);exit;
}
$result=$_SESSION['contact_result']??null;unset($_SESSION['contact_result']);
$values=$_SESSION['contact_values']??[];unset($_SESSION['contact_values']);
page_start('Kontakt');
?>
<div class="content-grid two"><article class="panel content-card"><h2>Schreibe uns</h2><p>Du möchtest uns kennenlernen, ein Bündnis anfragen oder mit uns in Kontakt treten?</p><p>Nutze das Formular für allgemeine Anfragen, Veranstaltungen, Bündnisse oder Fragen zur Sippe.</p><h3>Gemeinsam sind wir stark</h3><p>– wie die Raben im Sturm.</p><a class="btn" href="ueber-uns.html">Über die Sippe →</a></article><section class="panel content-card" aria-label="Kontaktformular">
<?php if($result): ?><p class="notice <?= $result['ok']?'success':'error' ?>" role="status"><?= e($result['message']) ?></p><?php endif; ?>
<form method="post" action="kontakt.php"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><label for="name">Name *<input id="name" name="name" autocomplete="name" maxlength="80" value="<?= e($values['name']??'') ?>" required></label><label for="email">E-Mail *<input id="email" name="email" type="email" autocomplete="email" maxlength="254" value="<?= e($values['email']??'') ?>" required></label><label for="subject">Betreff *<input id="subject" name="subject" maxlength="150" value="<?= e($values['subject']??'') ?>" required></label><label for="message">Nachricht *<textarea id="message" name="message" maxlength="3000" required><?= e($values['message']??'') ?></textarea></label><div class="honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div><label class="consent"><input type="checkbox" name="privacy" value="1" required> <span>Ich stimme der Verarbeitung meiner Angaben zur Bearbeitung der Anfrage zu. <a href="datenschutz.html">Datenschutz</a></span></label><button class="btn">Nachricht senden ›</button></form></section></div>
<?php page_end(); ?>
