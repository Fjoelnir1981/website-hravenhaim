# E-Mail-Versand einrichten

Die Website speichert gültige Kontaktanfragen zuerst in SQLite. Anschließend versucht sie den SMTP-Versand. Ohne eingerichteten Mailserver wird die Speicherung bestätigt, aber kein erfolgreicher E-Mail-Versand behauptet.

PHPMailer 7.1.1 ist mit den drei benötigten Quelldateien und seiner Lizenz in `lib/phpmailer/` enthalten. Composer ist zum Starten nicht nötig. Quelle: https://github.com/PHPMailer/PHPMailer/tree/v7.1.1

## Konfiguration außerhalb des Webverzeichnisses

Die Anwendung liest ausschließlich diese Umgebungsvariablen. Sie gehören in die Hosting-/Dienstkonfiguration oder in ein Startskript außerhalb des öffentlich erreichbaren Website-Ordners. Reale Passwörter niemals in Git oder diese Dokumentation eintragen.

| Variable | Bedeutung |
| --- | --- |
| `HRAVEN_SMTP_HOST` | SMTP-Server des Mailanbieters |
| `HRAVEN_SMTP_PORT` | Üblicherweise 587 für STARTTLS oder 465 für TLS |
| `HRAVEN_SMTP_SECURITY` | `tls` für STARTTLS, `ssl` für implizites TLS |
| `HRAVEN_SMTP_USER` | SMTP-Benutzername |
| `HRAVEN_SMTP_PASSWORD` | SMTP-Passwort oder App-Passwort |
| `HRAVEN_SMTP_FROM` | Beim Anbieter erlaubte Absenderadresse |
| `HRAVEN_SMTP_TO` | Empfängeradresse der Sippe |
| `HRAVEN_DB_PATH` | Optional: absoluter Pfad zur SQLite-Datei außerhalb des Webverzeichnisses |

Die Besucheradresse wird als `Reply-To` verwendet, nicht als Absender. Zertifikatsprüfung bleibt aktiv. `HRAVEN_SMTP_SECURITY=none` ist ausschließlich bei einem lokalen SMTP-Server (`127.0.0.1`, `localhost`, `::1`) möglich.

## Versandstatus

Die Tabelle `contacts` enthält zusätzlich:

- `delivery_status`: `not_sent` für ältere Einträge, `pending` während des Versands, `sent`, `unconfigured` oder `failed`.
- `sent_at`: Zeitpunkt der erfolgreichen Übergabe an den SMTP-Server.

`sent` bestätigt die Annahme durch den Mailserver, nicht den Eingang im Postfach. Fehlgeschlagene Nachrichten werden nicht automatisch erneut versendet. Die gespeicherte Anfrage bleibt erhalten.

## Abschließender Live-Test

Nach Einrichtung der echten Zugangsdaten eine eindeutig erkennbare Testanfrage über das Formular senden, den Eingang im Zielpostfach und die Antwortadresse prüfen. Dieser externe Versand wurde bei der Umsetzung nicht durchgeführt. Lokal wurden Annahme, Versandfehler und fehlende Konfiguration mit einer isolierten Testdatenbank geprüft.
