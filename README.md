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

- **Webserver** (`webserver/`): PHP-basierte Weboberfläche zum Senden von Nachrichten
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
│   ├── index.php           # Haupt-UI
│   ├── post.php            # API-Proxy
│   ├── edit.php            # Vorlagen-Editor
│   └── texte.json          # Nachrichtenvorlagen
├── 3d-case/                # 3D-Druck-Dateien
├── wiring-diagram/         # Fritzing-Schaltplan
├── setup.sh                # Installations-Skript
├── deploy.sh               # Deployment-Skript
├── crontab.sh              # Startup-Skript
└── crontab-install.txt     # Crontab-Vorlage
```

## Konfiguration

Die Pi-Hostnamen werden in folgenden Dateien konfiguriert:
- `webserver/index.php`: URLs der Raspberry Pis (`pi-zero1`, `pi-zero2`, `pi-zero3`)
- `deploy.sh`: Deployment-Ziele

## Bekannte Einschränkungen

- Keine Authentifizierung auf der REST-API (nur für lokale Netzwerke geeignet)
- Die Weboberfläche lädt jQuery von einem externen CDN
- Maximale Textlänge: 16 Zeichen pro Zeile (Hardware-Limitation)
- `socket_server_v1.py`, `socket_server_v2.py`, `socket_server_v3.py` sind ältere Entwicklungsversionen

## Lizenz

Siehe Projektdateien.