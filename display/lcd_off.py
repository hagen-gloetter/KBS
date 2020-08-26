#! /usr/bin/python3
# -*- coding: UTF-8 -*-
# setup

# http://www.learningaboutelectronics.com/Articles/How-to-get-the-last-modified-date-of-a-file-in-Python.php
# https://stackoverflow.com/questions/375154/how-do-i-get-the-time-a-file-was-last-modified-in-python

import RPi.GPIO as GPIO   #import the GPIO library
import lcddriver
import time
import datetime
from datetime import date
import sys
import os

def main():
    mypath = os.path.dirname(os.path.abspath(__file__))
    myfile = mypath +"/lastrun"
    lcd = lcddriver.lcd()
    jetzt = datetime.datetime.now()
    heute = date.today()
    heute.weekday()
    Wochentag = ("Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag")[heute.weekday()]
    Uhrzeit = jetzt.strftime('%H:%M:%S')
    Uhrzeit = jetzt.strftime('%H:%M')
    Datum = jetzt.strftime('%d.%m.%Y')
    Datum = jetzt.strftime('%d.%m.') # huebscher ohne Jahr
    line1 = "  -- " + Uhrzeit + " -- "
    line2 = Wochentag + ", " + Datum

    if os.path.isfile( myfile ) : # check if file exists
        lastmodified = os.path.getmtime( myfile )
        now = time.time()
        diff = now - lastmodified
        diff = diff/60
        print ("lastmodified " + str(lastmodified) + " now " + str(now) + " diff " + str(diff) )
        if diff > 5 :
            print("display off")
            lcd.lcd_display_string(line1,1) # nach dem Blinken muss das Display neu gesetzt werden
            lcd.lcd_display_string(line2,2)
            lcd.lcd_backlight("off")
            os.remove( myfile )
        else:
            print("display an lassen")
    else:
        print("Uhrzeit ausgeben " + line1 + " " + line2)
        lcd.lcd_display_string(line1,1) # nach dem Blinken muss das Display neu gesetzt werden
        lcd.lcd_display_string(line2,2)


if __name__ == "__main__":
    main()


