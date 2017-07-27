#!/usr/bin/python

import serial
import RPi.GPIO as GPIO
import time
#load pins config
import imp
cfg=imp.load_source('config', '/etc/seahu/pin_config.py')

pin_RTS=cfg.RS485_RE_DE		#11
GPIO.setmode(GPIO.BOARD)
#GPIO.setmode(GPIO.BCM)
GPIO.setup(pin_RTS, GPIO.OUT)
GPIO.output(pin_RTS, 1) #defaul value=transmission

ser = serial.Serial(
    port='/dev/ttyAMA0',
    baudrate = 9600,
    parity=serial.PARITY_NONE,
    stopbits=serial.STOPBITS_ONE,
    bytesize=serial.EIGHTBITS,
    timeout=1
    )

print ("Test RS485 Master side:")

ser.flushInput()

counter=0
while True:
    GPIO.output(pin_RTS, 1) #transmission
    ser.write('Write counter: %d \n'%(counter)) #this only very quicky send data to bufer not wait to send
    time.sleep(0.1) #wait to send date over serial line
    GPIO.output(pin_RTS, 0) #receiver
    receive = ser.read(100)
    if len(receive)==0 : print ("No slave contact.")
    else : print (receive)
    time.sleep(1)
    counter += 1
