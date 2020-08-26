import lcddriver
import time
lcd = lcddriver.lcd()

lcd.lcd_clear()
lcd.lcd_backlight("on")

lcd.lcd_display_string("Ramona",1)
lcd.lcd_display_string("Hagen",2)

time.sleep(1)
lcd.lcd_backlight("on")
time.sleep(1)
lcd.lcd_backlight("off")
time.sleep(1)
lcd.lcd_backlight("on")
time.sleep(1)
lcd.lcd_backlight("off")
time.sleep(1)
lcd.lcd_backlight("on")
time.sleep(1)
lcd.lcd_backlight("off")
time.sleep(1)
lcd.lcd_backlight("on")
time.sleep(1)
