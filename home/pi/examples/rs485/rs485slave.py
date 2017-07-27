#!/usr/bin/env python

import time
import serial
import RPi.GPIO as GPIO
#load pins config
import imp
cfg=imp.load_source('config', '/etc/seahu/pin_config.py')


pin_RST=cfg.RS485_RE_DE #11
GPIO.setmode(GPIO.BOARD)
#GPIO.setmode(GPIO.BCM)
GPIO.setup(pin_RST, GPIO.OUT)
GPIO.output(pin_RST, 1) #defaul value=transmission


ser = serial.Serial(
    port='/dev/ttyS0',
    baudrate = 9600,
    parity=serial.PARITY_NONE,
    stopbits=serial.STOPBITS_ONE,
    bytesize=serial.EIGHTBITS,
    timeout=0.1
    )
print ("Test RS485 Slave side:")

ser.flushInput()

GPIO.output(pin_RST, 0) #receiver
last_time=time.time()
while 1:
    receive = ser.read(100)
    if len(receive)==0 : 
	if (time.time()-last_time)>3 :
	    print ("No master contact.")
	    last_time=time.time()
    else : 
	last_time=time.time()
	print (receive)
	GPIO.output(pin_RST, 1) #transmission
	ser.write(receive)
	time.sleep(0.1)
	GPIO.output(pin_RST, 0) #receiver