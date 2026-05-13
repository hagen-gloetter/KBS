#! /usr/bin/python3
# -*- coding: UTF-8 -*-
# setup
# sudo apt-get install python-dev python-rpi.gpio
# sudo apt-get install python-rpi.gpio python3-rpi.gpio python3-pigpio python3-smbus
# sudo apt-get install python3-pip
# sudo apt-get install i2c-tools
# sudo apt-get install python-smbus
# pip install --upgrade pip
# sudo pip3 install Flask

# pi setup
# Display
# GPIO2 / SDA (Pin 3)
# GPIO3 / SCL (Pin 5)
# Buzzer
# GPIO3 15
# Ground

# run
# python3 socket_server.py

from flask import Flask, jsonify, abort, make_response
from flask import request
import subprocess
import os
import sys
import time
import logging

app = Flask(__name__)
tasks = []

import RPi.GPIO as GPIO
import lcddriver

lcd = lcddriver.lcd()
mypath = os.path.dirname(os.path.abspath(__file__))
print("--> mypath: " + mypath)

# Maximale Laenge fuer LCD-Zeilen (16x2 Display)
MAX_LINE_LENGTH = 16

def sanitize_lcd_text(text):
    """Begrenze Text auf erlaubte Laenge und entferne Steuerzeichen."""
    if not isinstance(text, str):
        text = str(text)
    # Nur druckbare ASCII-Zeichen erlauben (LCD-kompatibel)
    text = ''.join(c for c in text if 32 <= ord(c) < 127)
    return text[:MAX_LINE_LENGTH]

@app.route('/')
def index():
    return "Kids Room Information System"

@app.route('/lcd/api/v1.0/lcds/<int:task_id>', methods=['GET'])
def get_task(task_id):
    task = [task for task in tasks if task['id'] == task_id]
    if len(task) == 0:
        abort(404)
    return jsonify({'task': task[0]})

@app.route('/lcd/api/v1.0/lcds', methods=['POST'])
def create_task():
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
        'line1': line1,
        'line2': line2,
        'bell': bell,
        'display': display,
    }
    try:
        lcd_print(line1, line2, display, bell)
    except Exception as e:
        logging.error("Fehler bei lcd_print: %s", e)
        return jsonify({'error': 'Hardware-Fehler'}), 500
    tasks.append(task)
    return jsonify({'task': task}), 201

@app.errorhandler(404)
def not_found(error):
    return make_response(jsonify({'error': 'Not found'}), 404)

def lcd_print(line1, line2, display, bell):
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
        open(lastrun_path, 'a').close()
        os.utime(lastrun_path, None)
    except OSError as e:
        logging.error("Fehler beim Erstellen von lastrun: %s", e)

def bell_play():
    print("Bell on")
    buzzer_script = os.path.join(mypath, "buzzer3.py")
    try:
        subprocess.Popen(
            [sys.executable, buzzer_script, "1"],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
    except OSError as e:
        logging.error("Fehler beim Starten von buzzer3.py: %s", e)

def lcd_blink():
    print("Blink on")
    blink_script = os.path.join(mypath, "lcd_blink.py")
    try:
        subprocess.Popen(
            [sys.executable, blink_script, "30"],
            stdout=subprocess.DEVNULL,
            stderr=subprocess.DEVNULL
        )
    except OSError as e:
        logging.error("Fehler beim Starten von lcd_blink.py: %s", e)

def lcd_off_timer():
    print("lcd_off_timer --> off")
    lcd.lcd_backlight("off")

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080, threaded=True)

# Beispiel:
# curl -i -H "Content-Type: application/json" -X POST -d '{"line1":"It ", "line2":"works", "bell":"on","display":"on" }' http://192.168.4.10:8080/lcd/api/v1.0/lcds
