#! /usr/bin/python3
# -*- coding: utf-8 -*-
#
# KBS – Buzzer-Steuerung (aktive Version)
# Spielt verschiedene Melodien ueber einen Piezo-Buzzer an GPIO Pin 15.
#
# Verwendung: python3 buzzer3.py <Tune 1-5>
#
# Tunes:
#   1 – Tonleiter auf/ab (C4-C6)
#   2 – Aufsteigend kurz
#   3 – Melodie mit Pausen
#   4 – Absteigend hoch
#   5 – Absteigend tief
#
# Hardware: Piezo-Buzzer an GPIO 15 + Ground
#

import RPi.GPIO as GPIO   #import the GPIO library
import time               #import the time library
import sys


class Buzzer(object):
 def __init__(self):
  GPIO.setmode(GPIO.BCM)  
  self.buzzer_pin = 15 #set to GPIO pin 15
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
    duration=[0.2,0.2,0.2,0.2,0.5]
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


def main():
    if len(sys.argv) < 2:
        print("Verwendung: python3 buzzer3.py <Tune 1-5>")
        sys.exit(1)
    try:
        zahl = int(sys.argv[1])
    except ValueError:
        print("Fehler: Bitte eine Zahl zwischen 1 und 5 angeben.")
        sys.exit(1)
    if zahl < 1 or zahl > 5:
        print("Fehler: Tune muss zwischen 1 und 5 liegen.")
        sys.exit(1)
    buzzer = Buzzer()
    try:
        buzzer.play(zahl)
    finally:
        GPIO.cleanup()


if __name__ == "__main__":
    main()
