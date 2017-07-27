#!/usr/bin/env python


import time
import serial
import RPi.GPIO as GPIO
#load pins config
import imp
cfg=imp.load_source('config', '/etc/seahu/pin_config.py')

pin=cfg.RS485_RE_DE         #11
GPIO.setmode(GPIO.BOARD)
#GPIO.setmode(GPIO.BCM)
GPIO.setup(pin, GPIO.OUT)
GPIO.output(pin, 0)


ser = serial.Serial(

    port='/dev/ttyS0',
    baudrate = 9600,
    parity=serial.PARITY_NONE,
    stopbits=serial.STOPBITS_ONE,
    bytesize=serial.EIGHTBITS,
    timeout=1
    )
counter=0

while 1:
    x=ser.readline()
    print x
