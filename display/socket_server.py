#! /usr/bin/python3
# -*- coding: UTF-8 -*-
#
# KBS – Kinder Benachrichtigungs System
# Flask REST-API fuer LCD-Display und Buzzer auf dem Raspberry Pi
#
# Hardware:
#   Display: GPIO2/SDA (Pin 3), GPIO3/SCL (Pin 5)
#   Buzzer:  GPIO 15, Ground
#
# Start: python3 socket_server.py

from flask import Flask, jsonify, abort, make_response, request
import subprocess
import os
import sys
import time
import logging

# Logging konfigurieren
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S'
)
logger = logging.getLogger(__name__)

app = Flask(__name__)

# Task-Speicher
tasks = []
MAX_TASKS = 100
next_task_id = 1

import lcddriver

lcd = lcddriver.lcd()
mypath = os.path.dirname(os.path.abspath(__file__))
logger.info("Arbeitsverzeichnis: %s", mypath)

# Maximale Laenge fuer LCD-Zeilen (16x2 Display)
MAX_LINE_LENGTH = 16

def sanitize_lcd_text(text):
    """Begrenze Text auf erlaubte Laenge und entferne Steuerzeichen.

    Nur druckbare ASCII-Zeichen (32-126) werden durchgelassen,
    da das LCD keine Unicode-Zeichen darstellen kann.

    Args:
        text: Eingabetext (wird bei Bedarf zu String konvertiert)

    Returns:
        Bereinigter Text, maximal MAX_LINE_LENGTH Zeichen
    """
    if not isinstance(text, str):
        text = str(text)
    # Nur druckbare ASCII-Zeichen erlauben (LCD-kompatibel)
    text = ''.join(c for c in text if 32 <= ord(c) < 127)
    return text[:MAX_LINE_LENGTH]

@app.route('/')
def index():
    return jsonify({
        'name': 'KBS - Kids Room Information System',
        'version': '2.0',
        'endpoints': {
            'POST /lcd/api/v1.0/lcds': 'Nachricht an LCD senden',
            'GET /lcd/api/v1.0/lcds': 'Alle gesendeten Nachrichten abrufen',
            'GET /lcd/api/v1.0/lcds/<id>': 'Einzelne Nachricht abrufen',
            'GET /health': 'Health-Check',
        }
    })

@app.route('/health')
def health():
    return jsonify({'status': 'ok'})

@app.route('/lcd/api/v1.0/lcds', methods=['GET'])
def get_tasks():
    return jsonify({'tasks': tasks, 'count': len(tasks)})

@app.route('/lcd/api/v1.0/lcds/<int:task_id>', methods=['GET'])
def get_task(task_id):
    task = [t for t in tasks if t['id'] == task_id]
    if len(task) == 0:
        abort(404)
    return jsonify({'task': task[0]})

@app.route('/lcd/api/v1.0/lcds', methods=['POST'])
def create_task():
    global next_task_id
    if not request.json or 'line1' not in request.json:
        abort(400)
    line1 = sanitize_lcd_text(request.json.get('line1', ''))
    line2 = sanitize_lcd_text(request.json.get('line2', ''))
    bell = request.json.get('bell', 'off')
    display = request.json.get('display', 'off')
    # Nur "on"/"off" erlauben
    bell = 'on' if bell == 'on' else 'off'
    display = 'on' if display == 'on' else 'off'
    task = {
        'id': next_task_id,
        'line1': line1,
        'line2': line2,
        'bell': bell,
        'display': display,
        'timestamp': time.strftime('%Y-%m-%d %H:%M:%S'),
    }
    try:
        lcd_print(line1, line2, display, bell)
        logger.info("Nachricht gesendet: '%s' / '%s' (bell=%s, blink=%s)", line1, line2, bell, display)
    except Exception as e:
        logger.error("Fehler bei lcd_print: %s", e)
        return jsonify({'error': 'Hardware-Fehler'}), 500
    next_task_id += 1
    tasks.append(task)
    if len(tasks) > MAX_TASKS:
        tasks.pop(0)
    return jsonify({'task': task}), 201

@app.errorhandler(400)
def bad_request(error):
    return make_response(jsonify({'error': 'Bad request'}), 400)

@app.errorhandler(404)
def not_found(error):
    return make_response(jsonify({'error': 'Not found'}), 404)

def lcd_print(line1, line2, display, bell):
    """Zeigt Text auf dem LCD an und loest optional Blinken/Klingel aus.

    Args:
        line1:   Text fuer Zeile 1 (max. 16 Zeichen)
        line2:   Text fuer Zeile 2 (max. 16 Zeichen)
        display: 'on' fuer Blink-Effekt, 'off' fuer normal
        bell:    'on' fuer Buzzer, 'off' fuer stumm
    """
    lcd.lcd_clear()
    lcd.lcd_backlight("on")
    if display == "on":  # Blinken an
        lcd_blink()
    if bell == "on":     # Klingel an
        bell_play()
    time.sleep(.5)  # Pause ist wichtig, da sonst kein Text auf dem Display angezeigt wird!
    lcd.lcd_display_string(line1, 1)  # nach dem Blinken muss das Display neu gesetzt werden
    lcd.lcd_display_string(line2, 2)
    lastrun_path = os.path.join(mypath, "lastrun")
    try:
        with open(lastrun_path, 'a'):
            os.utime(lastrun_path, None)
    except OSError as e:
        logger.error("Fehler beim Erstellen von lastrun: %s", e)

def bell_play():
    """Startet buzzer3.py als Subprocess fuer den Klingelton."""
    logger.info("Bell on")
    buzzer_script = os.path.join(mypath, "buzzer3.py")
    try:
        subprocess.Popen(
            [sys.executable, buzzer_script, "1"],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
    except OSError as e:
        logger.error("Fehler beim Starten von buzzer3.py: %s", e)

def lcd_blink():
    """Startet lcd_blink.py als Subprocess fuer den Blink-Effekt."""
    logger.info("Blink on")
    blink_script = os.path.join(mypath, "lcd_blink.py")
    try:
        subprocess.Popen(
            [sys.executable, blink_script, "30"],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
    except OSError as e:
        logger.error("Fehler beim Starten von lcd_blink.py: %s", e)

def lcd_off_timer():
    """Schaltet das LCD-Backlight aus (wird nicht aktiv genutzt)."""
    logger.info("lcd_off_timer --> off")
    lcd.lcd_backlight("off")

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080, threaded=True)

# Beispiel:
# curl -i -H "Content-Type: application/json" -X POST -d '{"line1":"It ", "line2":"works", "bell":"on","display":"on" }' http://192.168.4.10:8080/lcd/api/v1.0/lcds
