#! /usr/bin/python3
# -*- coding: utf-8 -*-
#
# KBS – Buzzer Test (Legacy)
# Simple continuous buzzer test: beeps until CTRL+C is pressed.
# Used for hardware testing only.
#

import RPi.GPIO as GPIO # Import the GPIO library
import time             # Import the time library
 
buzzer_pin = 15  # GPIO pin 15 for buzzer
GPIO.setmode(GPIO.BCM)
GPIO.setup(buzzer_pin, GPIO.OUT)

print("Press CTRL+C to stop the program.")

try:
  while True:
    GPIO.output(buzzer_pin, True)  # Set buzzer pin high
    time.sleep(.3)                 # Wait briefly
    GPIO.output(buzzer_pin, False) # Set buzzer pin low
    time.sleep(.5)                 # Wait briefly

except KeyboardInterrupt:  
  GPIO.output(buzzer_pin, False)   # Set buzzer pin low
  # Clean up GPIO
  GPIO.cleanup()