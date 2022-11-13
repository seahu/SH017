#!/usr/bin/python

import time
import RPi.GPIO as GPIO
import smbus
import imp

cfg=imp.load_source('config', '/etc/seahu/pin_config.py')

# run as services and simulate non exist bepper on dispaly board
# if 8 bit  from i2c port onm dispaly board is set to 0 then beep other stop beep


#GPIO.setup(pin, GPIO.IN, pull_up_down = GPIO.PUD_DOWN)
bus = smbus.SMBus(1)
addr_i2c = cfg.addr_i2c        #0x3c # address of i2c ioexpander on dispaly board
#addr_i2c = 0x24 # address of i2c ioexpander on dispaly board
mask=0x80
Imask=mask^0xFF

def start():
    print("start beep service")
    pin=12
    GPIO.setmode(GPIO.BOARD)
    #GPIO.setmode(GPIO.BCM)

    GPIO.setup(pin, GPIO.OUT)
    GPIO.output(pin, 0)


    while True:
#        # for beer withou integrated rezonator
#        while (bus.read_byte(addr_i2c)&mask)==0:
#            i=0
#                while i<10:
#                GPIO.output(pin, 1)
#                #GPIO.setup(pin, GPIO.IN, pull_up_down = GPIO.PUD_DOWN)
#                time.sleep(0.001)
#                GPIO.output(pin, 0)
#                #GPIO.setup(pin, GPIO.IN, pull_up_down = GPIO.PUD_DOWN)
#                time.sleep(0.001)
#                i=i+1
        # for beeper with integrated beeper
        if (bus.read_byte(addr_i2c)&mask)==0: GPIO.output(pin, 1) 
        else: GPIO.output(pin, 0) #off

        time.sleep(0.05)

    GPIO.cleanup()

start()
