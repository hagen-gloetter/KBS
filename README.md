# KBS – Kinder Benachrichtigungs System

**English:** Tired of shouting through the house to call the kids for dinner or to get up? The Child Notification System (KBS) is a Raspberry Pi-based solution using a 16x2 LCD display and a piezo buzzer. Messages are sent via a REST API, so any system capable of HTTP POST requests can trigger notifications.

**Deutsch:** Ist es nicht nervig, immer durch das Haus oder die Wohnung zu brüllen, um die Kinder zum Essen oder Aufstehen zu rufen? Das Kinder Benachrichtigungs System (KBS) löst genau dieses Problem. Das System basiert auf einem Raspberry Pi (Zero), einem 16x2 LCD-Display und einem Piezo-Buzzer. Das Display und der Buzzer werden über eine REST-API angesteuert.

## Architektur

```
┌──────────────┐     HTTP POST      ┌──────────────────┐
│  Webserver   │ ─────────────────> │  Raspberry Pi    │
│  (PHP)       │                    │  Flask API :8080 │
│              │                    │  LCD + Buzzer    │
└──────────────┘                    └──────────────────┘
```

- **Webserver** (`webserver/`): PHP-basierte Weboberfläche zum Senden von Nachrichten (Vanilla JS, keine externen Abhängigkeiten)
- **Display-Server** (`display/`): Flask REST-API auf dem Raspberry Pi, steuert LCD und Buzzer
- **3D-Gehäuse** (`3d-case/`): 3D-Druck-Dateien für das Gehäuse
- **Schaltplan** (`wiring-diagram/`): Fritzing-Datei für die Verkabelung

## Hardware

- Raspberry Pi Zero (oder vergleichbar)
- 16x2 LCD-Display mit I2C-Adapter (Adresse: `0x27`)
- Piezo-Buzzer (an GPIO Pin 15)
- Verkabelung:
  - Display: GPIO2/SDA (Pin 3), GPIO3/SCL (Pin 5)
  - Buzzer: GPIO 15 + Ground

## Installation

### Raspberry Pi vorbereiten

```bash
# Repository klonen
git clone <repository-url> ~/kbs
cd ~/kbs

# Setup-Skript ausführen (installiert Abhängigkeiten, aktiviert I2C)
sudo bash setup.sh
```

### Cronjob einrichten

```bash
# Crontab bearbeiten
crontab -e

# Folgende Zeilen einfügen:
@reboot /bin/bash /home/pi/kbs/crontab.sh
*/1 * * * * /usr/bin/python3 /home/pi/kbs/display/lcd_off.py
```

Der Cronjob startet den Flask-Server beim Boot und schaltet das Display nach 5 Minuten Inaktivität ab.

### Webserver einrichten

Die Dateien aus `webserver/` auf einen Webserver mit PHP und cURL-Unterstützung kopieren:

```bash
# Beispiel: Deployment auf den Webserver
rsync -ave ssh webserver/* user@webserver:/var/www/html/
```

## Verwendung

### REST-API (direkt)

```bash
curl -i -H "Content-Type: application/json" \
  -X POST \
  -d '{"line1":"Essen ist", "line2":"fertig!", "bell":"on", "display":"on"}' \
  http://<pi-ip>:8080/lcd/api/v1.0/lcds
```

Parameter:
| Parameter | Beschreibung | Werte |
|-----------|-------------|-------|
| `line1` | Erste Zeile (max. 16 Zeichen) | Text |
| `line2` | Zweite Zeile (max. 16 Zeichen) | Text |
| `bell` | Buzzer abspielen | `on` / `off` |
| `display` | Display blinken lassen | `on` / `off` |

### Post-API (für externe Systeme)

Die `post.php` bietet eine einfache HTTP-Schnittstelle für Homeautomation-Systeme oder Skripte:

```bash
curl -X POST http://<webserver>/post.php \
  -d "line1=Essen ist" \
  -d "line2=fertig!" \
  -d "bell=on" \
  -d "display=on" \
  -d "person=K0"
```

Parameter:
| Parameter | Beschreibung | Werte | Default |
|-----------|-------------|-------|---------|
| `line1` | Erste Zeile (max. 16 Zeichen) | Text | Datum |
| `line2` | Zweite Zeile (max. 16 Zeichen) | Text | Uhrzeit |
| `bell` | Buzzer abspielen | `on` / `off` | `off` |
| `display` | Display blinken lassen | `on` / `off` | `off` |
| `person` | Zielperson | `K0` (alle) / `K1` / `K2` / `K3` | `K0` |

Antwort: JSON mit Ergebnis pro Ziel-Pi.

### Weboberfläche

Die PHP-Weboberfläche bietet:
- Vordefinierte Nachrichtenvorlagen (`texte.json`)
- Auswahl der Zielperson (einzelne Kinder oder alle)
- Klingel- und Blink-Optionen

## Deployment

```bash
# Auf alle Raspberry Pis und den Webserver deployen
bash deploy.sh
```

## Projektstruktur

```
KBS/
├── display/                # Flask-Server und Hardware-Steuerung
│   ├── socket_server.py    # Haupt-Server (aktuelle Version)
│   ├── bootscreen.py       # Zeigt Hostname/IP beim Boot
│   ├── buzzer3.py          # Buzzer-Steuerung
│   ├── lcd_blink.py        # Display-Blink-Funktion
│   ├── lcd_off.py          # Display-Abschaltung (Cronjob)
│   ├── lcddriver.py        # LCD I2C-Treiber
│   └── i2c_lib.py          # I2C-Bibliothek
├── webserver/              # PHP-Weboberfläche
│   ├── config.php          # Zentrale Konfiguration (Hosts, Ports, Personen)
│   ├── index.php           # Haupt-UI
│   ├── post.php            # API-Proxy (mit Zielauswahl)
│   ├── edit.php            # Vorlagen-Editor (Legacy)
│   ├── texte.json          # Nachrichtenvorlagen
│   └── css/style.css       # Eigenes CSS (keine externen Abhängigkeiten)
├── 3d-case/                # 3D-Druck-Dateien
├── wiring-diagram/         # Fritzing-Schaltplan
├── setup.sh                # Installations-Skript
├── deploy.sh               # Deployment-Skript
├── crontab.sh              # Startup-Skript
└── crontab-install.txt     # Crontab-Vorlage
```

## Konfiguration

Alle netzwerkspezifischen Einstellungen werden zentral in `webserver/config.php` verwaltet:

```php
$KBS_TARGETS = array(
    'K1' => array('name' => 'Ramona', 'host' => 'pi-zero1.fritz.box', 'emoji' => '&#x1F467;'),
    'K2' => array('name' => 'Denise', 'host' => 'pi-zero2.fritz.box', 'emoji' => '&#x1F467;'),
    'K3' => array('name' => 'Vater',  'host' => 'pi-zero3.fritz.box', 'emoji' => '&#x1F468;'),
);
```

Neue Personen/Pis können einfach als weitere Einträge ergänzt werden. Die Weboberfläche generiert die Radio-Buttons automatisch aus dieser Konfiguration.

Weitere Einstellungen:
- `KBS_API_PORT` — Port der Flask-Server (Default: 8080)
- `KBS_CURL_TIMEOUT` — cURL-Timeout in Sekunden (Default: 10)
- `KBS_CURL_CONNECT_TIMEOUT` — Connect-Timeout (Default: 5)

Die Deployment-Ziele in `deploy.sh` müssen separat angepasst werden.

## Bekannte Einschränkungen

- Keine Authentifizierung auf der REST-API (nur für lokale Netzwerke geeignet)
- Maximale Textlänge: 16 Zeichen pro Zeile (Hardware-Limitation)
- `socket_server_v1.py`, `socket_server_v2.py`, `socket_server_v3.py` sind ältere Entwicklungsversionen
- `buzzer.py`, `buzzer2.py` sind ältere Test-Dateien (Python 2-Syntax, nicht aktiv genutzt)
- `template.html`, `edit.php`, `styles.css` sind Legacy-Dateien (nicht aktiv genutzt)

## Lizenz

Siehe Projektdateien.