# Changelog

Alle wesentlichen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
und dieses Projekt verwendet [Semantic Versioning](https://semver.org/lang/de/).

## [Unreleased]

### Security
- **[Critical]** jQuery 3.3.1 → 3.7.1 aktualisiert (CVE-2020-11022, CVE-2020-11023, CVE-2019-11358)
- **[High]** jQuery wird jetzt lokal geladen statt vom CDN (keine externe Abhängigkeit, kein Supply-Chain-Risiko)
- **[Critical]** XSS-Schwachstelle in `index.php` durch unsanitisiertes `$_SERVER['PHP_SELF']` behoben
- **[Critical]** OS-Command-Injection in `socket_server.py` behoben — `os.system()` durch `subprocess.Popen()` ersetzt
- **[Critical]** Uninitialisiertes `$myObj` in `index.php` und `post.php` behoben
- **[High]** Fehlende `htmlspecialchars()`-Sanitisierung für `$person` in `index.php` hinzugefügt
- **[High]** Fehlende Input-Validierung in Flask-API — JSON-Felder werden jetzt sicher mit `.get()` und Defaults gelesen
- **[High]** Input-Sanitisierung für LCD-Text hinzugefügt (Längenbegrenzung, Steuerzeichen-Filter)
- **[High]** XSS in `index.php` `send_message_to_pi()` — URL/Content werden jetzt escaped in Fehlermeldungen
- **[Medium]** XSS in `index.php` value-Attributen — `$line1`/`$line2` Output jetzt mit `htmlspecialchars()`
- **[Medium]** jQuery CDN-Einbindung mit SRI-Hash (Subresource Integrity) abgesichert
- **[Medium]** cURL-Timeout (10s) und Connect-Timeout (5s) in `send_message_to_pi()` hinzugefügt

### Fixed
- **[High]** Fehlende Imports (`abort`, `make_response`) in `socket_server.py` ergänzt
- **[High]** Unbenutzte Imports (`requests`, `threading`, `Response`, `RPi.GPIO`) in `socket_server.py` entfernt
- **[High]** Switch-Statement-Bug in `index.php`: `case 200 || 201:` (immer `true`) zu separaten Cases korrigiert
- **[High]** `curl_errno()` nach `curl_close()` in `index.php` behoben — Fehler wird jetzt vor dem Schließen gespeichert
- **[High]** Tautologische Bedingung bei Radio-Buttons in `index.php` korrigiert (`||` → `&&`)
- **[High]** `post.php`: Variable `$display` wurde mit `$_POST['zielperson']` überschrieben
- **[High]** `post.php`: Ungültiges `return` auf Top-Level entfernt
- **[High]** Task-Dictionary in `socket_server.py` speicherte String-Literale statt Variablenwerte
- **[High]** `bootscreen.py`: `not in` → `!=` — Substring-Check statt Gleichheitsprüfung ergab falsche Ergebnisse
- **[High]** `lcddriver.py`: Backlight-State-Tracking — Backlight wurde bei jedem `lcd_write()` wieder eingeschaltet, da `LCD_BACKLIGHT` hardcoded war. Jetzt wird `self.backlight_state` in `lcd_strobe()` und `lcd_write_four_bits()` verwendet
- **[High]** `buzzer3.py`: Tune 2 Duration-Bug — `[0.2,0.2,0.2,0.2,0.2,0,5]` war `0` und `5` statt `0.5`, Array hatte 7 Elemente für 5 Pitches
- **[Medium]** `bootscreen.py`: Python 3-Kompatibilität für `struct.pack()` (bytes statt string)
- **[Medium]** `lcd_blink.py`: Validierungslogik `or` → `and` (war immer true)
- **[Medium]** `lcd_off.py`: Fehlender `if __name__ == '__main__'`-Guard hinzugefügt
- **[Medium]** `lcd_off.py`: Doppelter `__main__`-Block entfernt
- **[Medium]** `lcd_off.py`: Unnötiger `heute.weekday()`-Aufruf ohne Zuweisung entfernt
- **[Medium]** `lcd_off.py`: Wochentagsnamen auf 2 Zeichen gekürzt (Zeile 2 passte nicht in 16 Zeichen, z.B. "Donnerstag, 13.05." = 20 Zeichen)
- **[Medium]** `lcddriver.py`: `sys.path.append("./lib")` durch absoluten Pfad ersetzt — funktioniert jetzt auch wenn aus anderem Verzeichnis gestartet (z.B. Crontab)
- **[Medium]** `i2c_lib.py`: Wildcard-Import `from time import *` durch `from time import sleep` ersetzt
- **[Medium]** `lcddriver.py`: Wildcard-Import `from time import *` durch `from time import sleep` ersetzt
- **[Medium]** `i2c_lib.py`: Unnötiges `sys.path.append("./lib")` entfernt
- **[Medium]** `socket_server.py`: File-Handle-Leak bei lastrun-Erstellung behoben (`with`-Statement)
- **[Medium]** `index.php`: Variable `$person` im else-Branch initialisiert (vermeidet undefined-Warnung)
- **[Medium]** `index.php`: Zero-Width-Character (U+200B) im HTML entfernt
- **[Medium]** `crontab-install.txt`: Falscher Pfad `16x2_Display` → `kbs` korrigiert
- **[Medium]** `crontab.sh`: `python` → `python3` für bootscreen.py; stderr-Redirect hinzugefügt
- **[Low]** `socket_server.py`: Tasks-Liste auf 100 Einträge begrenzt (Memory-Leak verhindert)

### Changed
- **[Medium]** `setup.sh`: Python 2-Abhängigkeiten entfernt, nur noch Python 3
- **[Medium]** `setup.sh`: `/etc/modules`-Einträge werden nur hinzugefügt wenn nicht vorhanden
- **[Medium]** `setup.sh`: `-y` Flag für `apt-get install` hinzugefügt
- **[Medium]** `setup.sh`: Nutzt jetzt `requirements.txt` statt manueller `pip3 install`-Befehle
- **[Low]** `deploy.sh`: Excludes für `.git`, `__pycache__`, Logs, `3d-case`, `wiring-diagram`, `webserver/`, Docs
- **[Low]** `lcd_off.py`: Unbenutzten `RPi.GPIO`-Import und doppelte `strftime`-Aufrufe entfernt
- **[Low]** `lcddriver.py`: `lcd_backlight()` nutzt `state.lower()` statt expliziter Aufzählung
- **[Medium]** `post.php`: Komplett modernisiert — Port 8080, Timeout, JSON-Response, sauberes Error-Handling

### Added
- `requirements.txt` für Python-Abhängigkeiten erstellt
- `.gitignore` erstellt
- `CHANGELOG.md` erstellt
- `SECURITY.md` erstellt
- `README.md` umfassend aktualisiert mit Setup-Anleitung, API-Dokumentation und Projektstruktur
- `socket_server.py`: Error-Handler für 400 Bad Request hinzugefügt
- `socket_server.py`: Health-Endpoint (`GET /health`) und GET-Endpoint für alle Tasks
- `socket_server.py`: Task-IDs und Timestamps für jeden gesendeten Task
- `socket_server.py`: Strukturiertes Logging mit Zeitstempel statt print()
- `socket_server.py`: JSON-Info auf Root-Endpoint (`/`) mit API-Übersicht
- `index.php`: Komplettes Webseiten-Redesign mit modernem Card-Layout
- `index.php`: LCD-Vorschau-Styling (monospace, grüne Schrift auf dunkelgrün)
- `index.php`: Vorlagen werden bei Select-Änderung automatisch übernommen
- `index.php`: Pi-Targets als konfigurierbare Array-Struktur statt if/elseif-Kette
- `index.php`: Status-Alerts pro Pi (einzelne Erfolg/Fehler-Meldungen)
- `css/style.css`: Eigenes CSS mit Cards, Shadows, abgerundeten Ecken
- `js/jquery-3.7.1.min.js`: jQuery lokal eingebunden

### Improved
- `bootscreen.py`: Socket wird jetzt geschlossen, LCD in main() initialisiert, saubere Fehlerbehandlung
- `bootscreen.py`: get_ip() gibt Exception-basiert "Not connected" zurück statt finally-return
- `bootscreen.py`: Hostname wird nur einmal ermittelt statt in jeder Schleife
- `lcd_blink.py`: Unbenutzter RPi.GPIO-Import entfernt, Eingabe-Validierung mit ValueError-Handling
- `buzzer3.py`: GPIO.cleanup() im finally-Block, Input-Validierung mit Bereichsprüfung
- `texte.json`: Vorlagen auf max. 16 Zeichen korrigiert, konsistente Formatierung
- `texte.json`: "Aufstehen" und "SOFORT"-Vorlagen mit Blinken aktiviert
