# Hravenhaim Sippe – professionelle Website

## Start
`index.html`

## Mehrseitig
Start · Königspaar · Jarle · Events · Galerie · Bündnispartner · Buchempfehlungen · Gästebuch · Kontakt · Impressum · Datenschutz

## Backend
`gaestebuch.php` und `kontakt.php` verwenden SQLite, PDO, vorbereitete SQL-Statements, CSRF-Schutz, einfache Validierung und Honeypot-Spam-Schutz.

Voraussetzung:
- PHP 8+
- PDO SQLite (`pdo_sqlite`)
- Schreibrechte für `data/`

## Originales Logo
Lege das echte Logo unter `images/logo/hravenhaim-logo.png` ab. Eine SVG-Platzhaltergrafik ist enthalten.

## Eigene Bilder
Ersetze die Bilder in `images/koenigspaar`, `images/jarle`, `images/events`, `images/galerie`, `images/partner` und `images/books`.

## WICHTIG vor dem Livegang
- Impressum mit echten Betreiberangaben ausfüllen.
- Datenschutzerklärung an tatsächliches Hosting und Dienste anpassen.
- Kontaktformular an echte E-Mail/SMTP anbinden, wenn gewünscht.
- Gästebuch moderieren und Löschfunktion für Verantwortliche ergänzen.
- HTTPS aktivieren.
- Backup der SQLite-Datenbank einrichten.
- Falls externe Google Fonts genutzt werden, Datenschutz/Einbindung prüfen; alternativ Fonts lokal hosten.

Die Website enthält bewusst keine echten Personenfotos oder privaten Kontaktdaten.
