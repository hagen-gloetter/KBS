#! /usr/bin/python3
# -*- coding: UTF-8 -*-
#
# LCD-Backlight blinken lassen fuer eine bestimmte Dauer
# Verwendung: python3 lcd_blink.py <Sekunden 1-30>
#

import lcddriver
import time
import sys

lcd = lcddriver.lcd()

def lcd_blink(dauer):
    print("lcd_blink " + str(dauer) + "s")
    for x in range(dauer):
        time.sleep(.5)
        lcd.lcd_backlight("off")
        time.sleep(.5)
        lcd.lcd_backlight("on")
    print("genug geblinkt")

def main():
    if len(sys.argv) < 2:
        print("Verwendung: python3 lcd_blink.py <Sekunden 1-30>")
        sys.exit(1)
    try:
        zahl = int(sys.argv[1])
    except ValueError:
        print("Fehler: Bitte eine Zahl angeben.")
        sys.exit(1)
    if zahl > 0 and zahl < 31:
        lcd_blink(zahl)
    else:
        print("Fehler: Dauer muss zwischen 1 und 30 liegen.")
        sys.exit(1)

if __name__ == "__main__":
    main()
