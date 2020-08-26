#! /usr/bin/python3
# -*- coding: UTF-8 -*-
# setup
# sudo apt-get install python-dev python-rpi.gpio
# sudo apt-get install python-rpi.gpio python3-rpi.gpio python3-pigpio
# sudo apt-get install python3-pip 
# pip install --upgrade pip
# sudo pip3 install Flask
# sudo pip3 install request

# pi setup 
# GPIO2 / SDA (Pin 3)
# GPIO3 / SCL (Pin 5)

# run
# sudo python socket_server.py


import RPi.GPIO as GPIO   #import the GPIO library
import lcddriver
import time
import sys

lcd = lcddriver.lcd()

def lcd_blink(dauer):
    print ("lcd_blink " + str(dauer) +"s")
    for x in range(dauer):
        time.sleep(.5) 
        lcd.lcd_backlight("off")
        time.sleep(.5) 
        lcd.lcd_backlight("on")
    print ("genug geblinkt")

def main():
    if len(sys.argv) < 2:
        print ("Time to blink in seconds 1-30:")
        sys.exit()
    zahl = int(sys.argv[1])
    if (zahl > 0 or zahl < 31):
        lcd_blink(zahl)
    else:
        print ("Time to blink in seconds 1-30:")

if __name__ == "__main__":
    main()


#test: curl -i -H "Content-Type: application/json" -X POST -d '{"line1":"It ", "line2":"works", "bell":"on","display":"on" }' http://192.168.4.10:80/lcd/api/v1.0/lcds
