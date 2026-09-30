<?php require __DIR__ . '/bootstrap.php';
$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok())
        $status = '<div class="notice error">Ungültige Sitzung. Bitte erneut versuchen.</div>';
    else {
        $name = clean($_POST['name'] ?? '');
        $message = clean($_POST['message'] ?? '');
        if ($name === '' || mb_strlen($name) > 80 || $message === '' || mb_strlen($message) > 1000)
            $status = '<div class="notice error">Bitte Name und Nachricht prüfen.</div>';
        elseif (!empty($_POST['website'] ?? ''))
            $status = '<div class="notice error">Spam erkannt.</div>';
        else {
            $stmt = $db->prepare('INSERT INTO guestbook(name,message,created_at) VALUES(?,?,?)');
            $stmt->execute([$name, $message, date('Y-m-d H:i:s')]);
            $status = '<div class="notice success">Danke für deinen Gruß! Der Eintrag wurde gespeichert.</div>';
        }
    }
}
$entries = $db->query('SELECT name,message,created_at FROM guestbook ORDER BY id DESC LIMIT 50')->fetchAll(PDO::FETCH_ASSOC);
?>
<?= '<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Gästebuch · Hravenhaim Sippe</title><link rel="stylesheet" href="css/main.css"></head><body>' ?>
<header class="topbar">
    <div class="container nav"><a class="brand" href="index.html"><img src="images/logo/hravenhaim-logo.png"
                onerror="this.src='images/logo/hravenhaim-logo.svg'"><span>HRAVENHAIM<br>SIPPE</span></a><button
            class="menuBtn">☰</button>
        <nav class="menu"><a href="index.html">Start</a><a href="koenigspaar.html">Königspaar</a><a
                href="jarle.html">Jarle</a><a href="events.html">Events</a><a href="galerie.html">Galerie</a><a
                href="buendnispartner.html">Bündnispartner</a><a href="buchempfehlungen.html">Bücher</a><a
                class="active" href="gaestebuch.php">Gästebuch</a><a href="kontakt.php">Kontakt</a></nav>
    </div>
</header>
<main>
    <div class="page-hero">
        <div class="container">
            <h1>Gästebuch</h1>
            <p>Hinterlasse der Hravenhaim Sippe einen Gruß.</p>
        </div>
    </div>
    <section class="section">
        <div class="container grid grid-2">
            <div class="card">
                <h3>Dein Gruß</h3><?= $status ?>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                    <div><label>Name</label><input name="name" maxlength="80" required></div>
                    <div><label>Nachricht</label><textarea name="message" maxlength="1000" required></textarea></div>
                    <div style="display:none"><label>Website</label><input name="website" autocomplete="off"></div>
                    <label class="check"><input type="checkbox" required> Ich stimme der Veröffentlichung meines Namens
                        und meiner Nachricht im Gästebuch zu.</label>
                    <button class="btn">Gruß hinterlassen</button>
                </form>
            </div>
            <div>
                <h3 style="font:700 1.2rem Cinzel;color:var(--gold2)">Gästebucheinträge</h3>
                <?php foreach ($entries as $x): ?>
                    <div class="guest"><strong><?= e($x['name']) ?></strong><small> ·
                            <?= e(date('d.m.Y', strtotime($x['created_at']))) ?></small>
                        <div><?= nl2br(e($x['message'])) ?></div>
                    </div><?php endforeach; ?>
                <?php if (!$entries): ?>
                    <div class="notice">Noch keine Einträge. Sei der Erste!</div><?php endif; ?>
            </div>
        </div>
    </section>
</main>
<footer>
    <div class="container copyright">© <span data-year></span> Hravenhaim Sippe · <a href="impressum.html">Impressum</a>
        · <a href="datenschutz.html">Datenschutz</a></div>
</footer>
<script src="js/main.js"></script>
</body>

</html>