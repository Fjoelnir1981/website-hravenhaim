# Hravenhaim Sippe

Responsive Website nach der gelieferten Gestaltungsvorlage. Startseite und Unterseiten verwenden HTML, CSS und JavaScript; Kontakt und Gästebuch verwenden PHP und SQLite. Keine Build-Schritte erforderlich.

## Lokal starten

Voraussetzung: PHP 8.2+ mit PDO SQLite, mbstring und OpenSSL.

Im Website-Ordner:

```sh
php -S 127.0.0.1:8765 router.php
```

Dann http://127.0.0.1:8765 öffnen. Auf dem verwendeten Mac liegt PHP unter `/Applications/XAMPP/xamppfiles/bin/php`; dieser Pfad kann `php` im Befehl ersetzen. Die Website über HTTP starten, nicht per Doppelklick als `file://` öffnen.

Der lokale Router sperrt interne Verzeichnisse. Für Apache/XAMPP sind passende `.htaccess`-Dateien enthalten; `AllowOverride` muss sie zulassen. Bei anderen Webservern müssen `.git`, `data`, `includes`, `lib`, versteckte Dateien und interne PHP-/Dokumentationsdateien entsprechend gesperrt werden. Der eingebaute PHP-Server ist für lokale Vorschauen gedacht.

## Seiten

Start, Königspaar, Jarle, Events, Kalender, Galerie, Bündnispartner, Buchempfehlungen, Über uns, Gästebuch, Kontakt, Impressum und Datenschutz.

## Inhalte pflegen

- Termine zentral in `events.json` bearbeiten. Startseite, Eventübersicht und Kalender lesen dieselbe Datei. Datumsformat: `YYYY-MM-DD`; eindeutige `id` für den Direktlink beibehalten.
- Die Termine von 2025 und die sichtbaren Startseitentexte stammen ausdrücklich aus der Vorlage. Die Eventseite kennzeichnet die Termine als vergangen.
- Seiteninhalte direkt in den HTML-Dateien bearbeiten. Kontakt und Gästebuch liegen in ihren PHP-Dateien.
- Die PHP-Seiten verwenden `includes/header.html` und `includes/footer.html`. Bei Änderungen der Navigation auch die statischen HTML-Seiten anpassen; `includes/page-template.html` dient als Vorlage für weitere statische Seiten.
- Gestaltung: `css/main.css`; Interaktionen: `js/main.js`.
- Der Gruß von Thorsten auf der Startseite ist ein gekennzeichnetes Gestaltungsbeispiel. Echte Gästebucheinträge werden ausschließlich auf der Gästebuchseite gespeichert und angezeigt.

## Formulare und Speicherung

Die SQLite-Datei wird beim ersten Datenbankzugriff automatisch angelegt. Standard: `data/hravenhaim.sqlite`. Für den Livebetrieb bevorzugt `HRAVEN_DB_PATH` auf eine Datei außerhalb des Webverzeichnisses setzen. PHP benötigt Schreibrechte im zugehörigen Verzeichnis.

Kontakt: serverseitige Validierung, CSRF-Schutz, Einwilligung, Spamfalle, Sitzungs-Ratenbegrenzung und Schutz gegen unmittelbare doppelte Übermittlung. Kontaktanfragen werden zuerst gespeichert, dann per SMTP übermittelt. Ohne SMTP-Konfiguration bleibt die Anfrage gespeichert und wird nicht als zugestellt ausgegeben.

Gästebuch: Einträge werden nach bestätigter Einwilligung unmittelbar veröffentlicht. Ausgabe wird HTML-escaped. Kein Administrations- oder Moderationsbereich enthalten.

Details: `SMTP-EINRICHTUNG.md`. Zugangsdaten und Datenbanken werden von Git ausgeschlossen.

## Git und Rücknahme

- Ausgangsstand: Tag `vor-designumbau` und Branch `main`.
- Umbau: Branch `design/zielbild` mit getrennten Commits.
- Anleitung: `GIT-ANLEITUNG.md`.
- Kein Push und keine Veröffentlichung durchgeführt.

## Noch für den Livebetrieb zu ergänzen

SMTP-Zugangsdaten und tatsächlichen E-Mail-Empfang prüfen. Betreiberangaben in Impressum und Datenschutz vervollständigen. Die Entwürfe sind ausdrücklich als unvollständig gekennzeichnet.

Bildquellen und Generierungsprompt stehen in `ASSETS.md`.
