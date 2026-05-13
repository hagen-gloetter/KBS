# Changelog

Alle wesentlichen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
und dieses Projekt verwendet [Semantic Versioning](https://semver.org/lang/de/).

## [Unreleased]

### Security
- **[Critical]** XSS-Schwachstelle in `index.php` durch unsanitisiertes `$_SERVER['PHP_SELF']` behoben
- **[Critical]** OS-Command-Injection in `socket_server.py` behoben — `os.system()` durch `subprocess.Popen()` ersetzt
- **[Critical]** Uninitialisiertes `$myObj` in `index.php` und `post.php` behoben
- **[High]** Fehlende `htmlspecialchars()`-Sanitisierung für `$person` in `index.php` hinzugefügt
- **[High]** Fehlende Input-Validierung in Flask-API — JSON-Felder werden jetzt sicher mit `.get()` und Defaults gelesen
- **[High]** Input-Sanitisierung für LCD-Text hinzugefügt (Längenbegrenzung, Steuerzeichen-Filter)
- **[High]** XSS in `index.php` `send_message_to_pi()` — URL/Content werden jetzt escaped in Fehlermeldungen

### Fixed
- **[High]** Fehlende Imports (`abort`, `make_response`) in `socket_server.py` ergänzt
- **[High]** Unbenutzte Imports (`requests`, `threading`, `Response`) in `socket_server.py` entfernt
- **[High]** Switch-Statement-Bug in `index.php`: `case 200 || 201:` (immer `true`) zu separaten Cases korrigiert
- **[High]** `curl_errno()` nach `curl_close()` in `index.php` behoben — Fehler wird jetzt vor dem Schließen gespeichert
- **[High]** Tautologische Bedingung bei Radio-Buttons in `index.php` korrigiert (`||` → `&&`)
- **[High]** `post.php`: Variable `$display` wurde mit `$_POST['zielperson']` überschrieben
- **[High]** `post.php`: Ungültiges `return` auf Top-Level entfernt
- **[High]** Task-Dictionary in `socket_server.py` speicherte String-Literale statt Variablenwerte
- **[Medium]** `bootscreen.py`: Python 3-Kompatibilität für `struct.pack()` (bytes statt string)
- **[Medium]** `lcd_blink.py`: Validierungslogik `or` → `and` (war immer true)
- **[Medium]** `lcd_off.py`: Fehlender `if __name__ == '__main__'`-Guard hinzugefügt
- **[Medium]** `crontab-install.txt`: Falscher Pfad `16x2_Display` → `kbs` korrigiert
- **[Medium]** `crontab.sh`: `python` → `python3` für bootscreen.py; stderr-Redirect hinzugefügt

### Changed
- **[Medium]** `setup.sh`: Python 2-Abhängigkeiten entfernt, nur noch Python 3
- **[Medium]** `setup.sh`: `/etc/modules`-Einträge werden nur hinzugefügt wenn nicht vorhanden
- **[Medium]** `setup.sh`: `-y` Flag für `apt-get install` hinzugefügt
- **[Low]** `lcd_off.py`: Unbenutzten `RPi.GPIO`-Import und doppelte `strftime`-Aufrufe entfernt

### Added
- `.gitignore` erstellt
- `CHANGELOG.md` erstellt
- `SECURITY.md` erstellt
- `README.md` umfassend aktualisiert mit Setup-Anleitung, API-Dokumentation und Projektstruktur
