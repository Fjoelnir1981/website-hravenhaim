<?php require __DIR__ . '/bootstrap.php';
$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_ok())
    $status = '<div class="notice error">Ungültige Sitzung. Bitte erneut versuchen.</div>';
  else {
    $name = clean($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');
    if ($name === '' || mb_strlen($name) > 80 || !$email || mb_strlen($subject) > 150 || $subject === '' || $message === '' || mb_strlen($message) > 3000)
      $status = '<div class="notice error">Bitte alle Felder korrekt ausfüllen.</div>';
    elseif (empty($_POST['privacy'] ?? ''))
      $status = '<div class="notice error">Bitte der Verarbeitung zur Bearbeitung der Anfrage zustimmen.</div>';
    elseif (!empty($_POST['website'] ?? ''))
      $status = '<div class="notice error">Spam erkannt.</div>';
    else {
      $stmt = $db->prepare('INSERT INTO contacts(name,email,subject,message,created_at) VALUES(?,?,?,?,?)');
      $stmt->execute([$name, $email, $subject, $message, date('Y-m-d H:i:s')]);
      $status = '<div class="notice success">Danke! Deine Anfrage wurde gespeichert. Für echten E-Mail-Versand kann zusätzlich mail() oder SMTP eingerichtet werden.</div>';
    }
  }
}
?>
<!doctype html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kontakt · Hravenhaim Sippe</title>
  <link rel="stylesheet" href="css/main.css">
</head>

<body>
  <header class="topbar">
    <div class="container nav"><a class="brand" href="index.html"><img src="images/logo/hravenhaim-logo.png"
          onerror="this.src='images/logo/hravenhaim-logo.svg'"><span>HRAVENHAIM<br>SIPPE</span></a><button
        class="menuBtn">☰</button>
      <nav class="menu"><a href="index.html">Start</a><a href="koenigspaar.html">Königspaar</a><a
          href="jarle.html">Jarle</a><a href="events.html">Events</a><a href="galerie.html">Galerie</a><a
          href="buendnispartner.html">Bündnispartner</a><a href="buchempfehlungen.html">Bücher</a><a
          href="gaestebuch.php">Gästebuch</a><a class="active" href="kontakt.php">Kontakt</a></nav>
    </div>
  </header>
  <main>
    <div class="page-hero">
      <div class="container">
        <h1>Kontakt</h1>
        <p>Du möchtest uns kennenlernen, ein Bündnis anfragen oder mit uns in Kontakt treten?</p>
      </div>
    </div>
    <section class="section">
      <div class="container grid grid-2">
        <div><span class="meta">SCHREIBT UNS</span>
          <h2 style="font:800 2.7rem MedievalSharp;color:var(--gold2)">Wir hören zu.</h2>
          <p>Nutze das Formular für allgemeine Anfragen, Veranstaltungen, Bündnisse oder Fragen zur Sippe.</p>
          <div class="card">
            <h3>Hravenhaim Sippe</h3>
            <p>E-Mail: <strong>info@hravenhaim-sippe.de</strong><br>Deutschland</p>
          </div>
        </div>
        <div class="card"><?= $status ?>
          <form method="post"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
            <div><label>Name</label><input name="name" maxlength="80" required></div>
            <div><label>E-Mail</label><input name="email" type="email" required></div>
            <div><label>Betreff</label><input name="subject" maxlength="150" required></div>
            <div><label>Nachricht</label><textarea name="message" maxlength="3000" required></textarea></div>
            <div style="display:none"><label>Website</label><input name="website" autocomplete="off"></div><label
              class="check"><input type="checkbox" name="privacy" value="1" required> Ich stimme der Verarbeitung meiner
              Angaben zur Bearbeitung der Anfrage zu.</label><button class="btn">Nachricht senden</button>
          </form>
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