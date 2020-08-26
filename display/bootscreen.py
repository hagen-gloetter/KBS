#
# Nach reboot Hostnamen und IP anzeigen
#

import lcddriver
import time

# import required modules
import socket
import fcntl
import struct

lcd = lcddriver.lcd()
lcd.lcd_clear()
lcd.lcd_backlight("on")
max_waittime=60

# main function
def main():
    ret = get_ip_loop()

def get_ip_loop():
    t = 0 # timer
    while t <= max_waittime:
      hostname = socket.gethostname()
      lcd.lcd_clear()
      lcd.lcd_display_string(hostname,1)
      txt=("loading: " + str(t))
      lcd.lcd_display_string(txt,2)
      ip_addr = get_ip("wlan0")
      print ("1: " + ip_addr)  # debug 
      if ip_addr not in "Not connected":
          print ("if: " + ip_addr)  # debug
          ip_addr= "w: %s" % get_ip("wlan0") # get wifi connection
          t=max_waittime # got an IP
          break
      ip_addr = get_ip("eth0")
      if ip_addr != "Not connected":
          print ("if: " + ip_addr)  # debug
          ip_addr= "e: %s" % get_ip("eth0") # get cable connection
          t=max_waittime # got an IP
          break
      time.sleep(1)
      t += 1

    print ("Hostname:"+ hostname) # debug
    print ("IP:" + ip_addr)  # debug
    lcd.lcd_display_string(hostname,1)
    lcd.lcd_display_string(ip_addr,2)

# function to get ip address of given interface
def get_ip(interface):
  ip_addr = "Not connected"
  s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
  try:
    ip_addr = socket.inet_ntoa(fcntl.ioctl(s.fileno(), 0x8915, struct.pack('256s', interface[:15]))[20:24])
  finally:
    return ip_addr


if __name__ == '__main__':
  main()
