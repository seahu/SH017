#!/usr/bin/python

import time
import RPi.GPIO as GPIO
import smbus
import imp

cfg=imp.load_source('config', '/etc/seahu/pin_config.py')

#GPIO.setup(pin, GPIO.IN, pull_up_down = GPIO.PUD_DOWN)

# beep from emulation buzzer on displayboard
def beep():
    bus = smbus.SMBus(1)
    addr_i2c = cfg.addr_i2c	#0x3c # address of i2c ioexpander on dispaly board
    mask=0x80
    Imask=mask^0xFF
    bus.write_byte(addr_i2c, bus.read_byte(addr_i2c)&Imask)
    time.sleep(0.2)
    bus.write_byte(addr_i2c, bus.read_byte(addr_i2c)|mask)

# beep direct
def beep_direct():
    pin=cfg.beep_pin  #12
    GPIO.setmode(GPIO.BOARD)
    #GPIO.setmode(GPIO.BCM)

    GPIO.setup(pin, GPIO.OUT)
    GPIO.output(pin, 1)
    i=0
    while i<80:
	GPIO.output(pin, 1)
	#GPIO.setup(pin, GPIO.IN, pull_up_down = GPIO.PUD_DOWN)
	time.sleep(0.001)
	GPIO.output(pin, 0)
	#GPIO.setup(pin, GPIO.IN, pull_up_down = GPIO.PUD_DOWN)
	time.sleep(0.001)
	i=i+1
    #GPIO.cleanup()

beep()
