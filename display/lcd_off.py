#! /usr/bin/python3
# -*- coding: UTF-8 -*-
#
# KBS – Display-Abschaltung
# Schaltet das LCD-Backlight nach 5 Minuten Inaktivitaet ab.
# Wird per Crontab (jede Minute) ausgefuehrt.
#
# Logik:
#   - Wenn 'lastrun'-Datei existiert und aelter als 5 Min → Display aus
#   - Wenn keine 'lastrun'-Datei → Uhrzeit anzeigen (Standby)
#   - Wenn 'lastrun' juenger als 5 Min → nichts tun (Display an lassen)
#

import lcddriver
import time
import datetime
from datetime import date
import os

def main():
    """Prueft Inaktivitaet und schaltet Display bei Bedarf ab."""
    mypath = os.path.dirname(os.path.abspath(__file__))
    myfile = mypath +"/lastrun"
    lcd = lcddriver.lcd()
    jetzt = datetime.datetime.now()
    heute = date.today()
    Wochentag = ("Mo", "Di", "Mi", "Do", "Fr", "Sa", "So")[heute.weekday()]
    Uhrzeit = jetzt.strftime('%H:%M')
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

if __name__ == '__main__':
    main()


