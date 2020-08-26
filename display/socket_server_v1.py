#!flask/bin/python

# run
# sudo python socket_server.py

from flask import Flask, jsonify
from flask import request

app = Flask(__name__)
tasks = []

import lcddriver
import time


lcd = lcddriver.lcd()

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
    lcd_print(line1,line2,display)
    tasks.append(task)
    return jsonify({'task': task}), 201

@app.errorhandler(404)
def not_found(error):
    return make_response(jsonify({'error': 'Not found'}), 404)

def lcd_print(line1,line2,display):
    lcd.lcd_clear()
    lcd.lcd_backlight("on")
    lcd.lcd_display_string(line1,1)
    lcd.lcd_display_string(line2,2)
    if display == "on":
        for x in range(30):
            time.sleep(1) 
            lcd.lcd_backlight("off")
            time.sleep(1) 
            lcd.lcd_backlight("on")


def lcd_off_timer():
    print  "lcd_off_timer --> off"
    lcd.lcd_backlight("off")

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=80)

#curl -i -H "Content-Type: application/json" -X POST -d '{"line1":"It ", "line2":"works", "bell":"on","display":"on" }' http://192.168.4.10:80/lcd/api/v1.0/lcds
