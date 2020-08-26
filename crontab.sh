#! /bin/bash
/usr/bin/python /home/pi/kbs/display/bootscreen.py
sleep 5
/usr/bin/python /home/pi/kbs/display/bootscreen.py
sleep 5
/usr/bin/python3 /home/pi/kbs/display/socket_server.py >> /home/pi/kbs/display/logfile.log

# @reboot /home/pi/kbs/crontab.sh
