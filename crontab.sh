#! /bin/bash
#
# KBS – Startup-Skript (Crontab @reboot)
# Wird beim Booten des Raspberry Pi ausgefuehrt.
# 1. Zeigt Hostname/IP auf dem LCD (bootscreen.py)
# 2. Startet den Flask REST-API Server (socket_server.py)
#

/usr/bin/python3 /home/pi/kbs/display/bootscreen.py
sleep 5
# twice because my nano sometimes misses the first command
/usr/bin/python3 /home/pi/kbs/display/bootscreen.py
sleep 5
/usr/bin/python3 /home/pi/kbs/display/socket_server.py >> /home/pi/kbs/display/logfile.log 2>&1

# @reboot /home/pi/kbs/crontab.sh
