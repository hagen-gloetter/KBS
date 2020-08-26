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
# sudo pip3 install request

# pi setup 
# Display
# GPIO2 / SDA (Pin 3)
# GPIO3 / SCL (Pin 5)
# Buzzer 
# GPIO3 15
# Ground

# run
# python3 socket_server.py

from flask import Flask, jsonify
from flask import request

app = Flask(__name__)
tasks = []

import RPi.GPIO as GPIO   #import the GPIO library
import lcddriver
import time
import requests
import threading
import os

lcd = lcddriver.lcd()
mypath = os.path.dirname(os.path.abspath(__file__))
print ("--> mypath: " + mypath)

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
    if not request.json or not 'line1' in request.json:
        abort(400)
    line1 = request.json['line1']
    line2 = request.json['line2']
    bell = request.json['bell']
    display = request.json['display']
    task = {
        'line1': "line1",
        'line2': "line2",
        'bell' : "bell",
        'display' : "display",
    }
    lcd_print(line1,line2,display,bell)
    tasks.append(task)
    return jsonify({'task': task}), 201
#    return ('', 201)

@app.errorhandler(404)
def not_found(error):
    return Response(jsonify({'error': 'Not found'}), 404)

def lcd_print(line1,line2,display,bell):
    lcd.lcd_clear()
    lcd.lcd_backlight("on")
    if display == "on": # Blinken an
        lcd_blink()
    if bell == "on":    # Klingel an
        bell_play()
    time.sleep(.5) #Pause ist wichtig, da sonst kein Text auf dem Displayangezeigt wird!
    lcd.lcd_display_string(line1,1) # nach dem Blinken muss das Dieplay neu gesetzt werden
    lcd.lcd_display_string(line2,2)
    cmd = "touch "+ mypath +"/lastrun"
    os.system(cmd)

def bell_play():
    print ("Bell on")
    cmd = "nohup python "+ mypath +"/buzzer3.py 1 &"
    os.system(cmd)
#    os.system('nohup python buzzer3.py 1 &')

def lcd_blink():
    print ("Blink on")
    cmd = "nohup python "+ mypath +"/lcd_blink.py 30 &"
    os.system(cmd)
#    os.system('nohup python lcd_blink.py 30 &')

def lcd_off_timer():
    print  ("lcd_off_timer --> off")
    lcd.lcd_backlight("off")

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080, threaded=True)


#curl -i -H "Content-Type: application/json" -X POST -d '{"line1":"It ", "line2":"works", "bell":"on","display":"on" }' http://192.168.4.10:80/lcd/api/v1.0/lcds
