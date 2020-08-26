#!flask/bin/python

# run
# sudo python socket_server.py

from flask import Flask, jsonify
from flask import request

app = Flask(__name__)
tasks = []

import RPi.GPIO as GPIO   #import the GPIO library
import lcddriver
import time
import grequests
#import asyncio

class Buzzer(object):
 def __init__(self):
  GPIO.setmode(GPIO.BCM)  
  self.buzzer_pin = 5 #set to GPIO pin 5
  GPIO.setup(self.buzzer_pin, GPIO.IN)
  GPIO.setup(self.buzzer_pin, GPIO.OUT)
  print("buzzer ready")

 def __del__(self):
  class_name = self.__class__.__name__
  print (class_name, "finished")
  
 def buzz(self,pitch,duration):
  if(pitch==0):
   time.sleep(duration)
   return
  period = 1.0 / pitch     #in physics, the period (sec/cyc) is the inverse of the frequency (cyc/sec)
  delay = period / 2     #calcuate the time for half of the wave  
  cycles = int(duration * pitch)   #the number of waves to produce is the duration times the frequency

  for i in range(cycles):
   GPIO.output(self.buzzer_pin, True)   #set pin 18 to high
   time.sleep(delay)
       #wait with pin 18 high
   GPIO.output(self.buzzer_pin, False)
       #set pin 18 to low
   time.sleep(delay)
       #wait with pin 18 low

#@asyncio.coroutine 
def play(self, tune):
  GPIO.setmode(GPIO.BCM)
  GPIO.setup(self.buzzer_pin, GPIO.OUT)
  x=0
  print("Playing tune ",tune)
  if(tune==1):
    pitches=[262,294,330,349,392,440,494,523, 587, 659,698,784,880,988,1047]
    duration=0.1
    for p in pitches:
      self.buzz(p, duration)
      time.sleep(duration *0.5)
    for p in reversed(pitches):
      self.buzz(p, duration)
      time.sleep(duration *0.5)

  elif(tune==2):
    pitches=[262,330,392,523,1047]
    duration=[0.2,0.2,0.2,0.2,0.2,0,5]
    for p in pitches:
      self.buzz(p, duration[x])
      time.sleep(duration[x] *0.5)
      x+=1
  elif(tune==3):
    pitches=[392,294,0,392,294,0,392,0,392,392,392,0,1047,262]
    duration=[0.2,0.2,0.2,0.2,0.2,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.8,0.4]
    for p in pitches:
      self.buzz(p, duration[x])
      time.sleep(duration[x] *0.5)
      x+=1
  elif(tune==4):
    pitches=[1047, 988,659]
    duration=[0.1,0.1,0.2]
    for p in pitches:
      self.buzz(p, duration[x])
      time.sleep(duration[x] *0.5)
      x+=1
  elif(tune==5):
    pitches=[1047, 988,523]
    duration=[0.1,0.1,0.2]
    for p in pitches:
      self.buzz(p, duration[x])
      time.sleep(duration[x] *0.5)
      x+=1
  GPIO.setup(self.buzzer_pin, GPIO.IN)
  

lcd = lcddriver.lcd()
buzzer = Buzzer()


@app.route('/')
#@asyncio.coroutine
def index():
    return "Kids Room Information System"

@app.route('/lcd/api/v1.0/lcds/<int:task_id>', methods=['GET'])
def get_task(task_id):
    task = [task for task in tasks if task['id'] == task_id]
    if len(task) == 0:
        abort(404)
    return jsonify({'task': task[0]})

#@asyncio.coroutine
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
    pass
#    return jsonify({'task': task}), 201

@app.errorhandler(404)
def not_found(error):
    return make_response(jsonify({'error': 'Not found'}), 404)

#@asyncio.coroutine
def lcd_print(line1,line2,display,bell):
    lcd.lcd_clear()
    lcd.lcd_backlight("on")
    lcd.lcd_display_string(line1,1)
    lcd.lcd_display_string(line2,2)
    if bell == "on":
      buzzer.play(int(3))
    if display == "on":
        for x in range(30):
            time.sleep(1) 
            lcd.lcd_backlight("off")
            time.sleep(1) 
            lcd.lcd_backlight("on")

def lcd_off_timer():
    print  ("lcd_off_timer --> off")
    lcd.lcd_backlight("off")

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080, threaded=True)




#curl -i -H "Content-Type: application/json" -X POST -d '{"line1":"It ", "line2":"works", "bell":"on","display":"on" }' http://192.168.4.10:80/lcd/api/v1.0/lcds
