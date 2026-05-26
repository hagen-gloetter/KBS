#! /usr/bin/python3
# -*- coding: UTF-8 -*-
#
# KBS – Bootscreen
# Zeigt nach dem Booten Hostname und IP-Adresse auf dem LCD-Display an.
# Wartet bis zu MAX_WAITTIME Sekunden auf eine Netzwerkverbindung.
#
# Wird per Crontab (@reboot) automatisch gestartet.
#

import lcddriver
import time
import socket
import fcntl
import struct

MAX_WAITTIME = 60  # Maximale Wartezeit auf Netzwerk (Sekunden)


def main():
    """LCD initialisieren und IP-Adresse anzeigen."""
    lcd = lcddriver.lcd()
    lcd.lcd_clear()
    lcd.lcd_backlight("on")
    ip_addr = get_ip_loop(lcd)

def get_ip_loop(lcd):
    """Wartet auf Netzwerkverbindung und zeigt Ladefortschritt an.

    Prueft abwechselnd WLAN (wlan0) und Ethernet (eth0).
    Zeigt Hostname und IP auf dem Display sobald verbunden.
    """
    t = 0
    hostname = socket.gethostname()
    ip_addr = "Not connected"

    while t <= MAX_WAITTIME:
        lcd.lcd_clear()
        lcd.lcd_display_string(hostname, 1)
        lcd.lcd_display_string("loading: " + str(t), 2)

        # WLAN pruefen
        ip_addr = get_ip("wlan0")
        if ip_addr != "Not connected":
            ip_addr = "w: " + ip_addr
            break

        # Ethernet pruefen
        ip_addr = get_ip("eth0")
        if ip_addr != "Not connected":
            ip_addr = "e: " + ip_addr
            break

        time.sleep(1)
        t += 1

    print("Hostname: " + hostname)
    print("IP: " + ip_addr)
    lcd.lcd_display_string(hostname, 1)
    lcd.lcd_display_string(ip_addr, 2)
    return ip_addr

def get_ip(interface):
    """IP-Adresse eines Netzwerk-Interfaces ermitteln."""
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    try:
        ip_addr = socket.inet_ntoa(
            fcntl.ioctl(s.fileno(), 0x8915,
                        struct.pack('256s', interface[:15].encode('utf-8')))[20:24]
        )
        return ip_addr
    except (IOError, OSError):
        return "Not connected"
    finally:
        s.close()


if __name__ == '__main__':
    main()
