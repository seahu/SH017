#!/usr/bin/python

import smbus
import time

import beep
from graph_lcd import *

bus = smbus.SMBus(1)
addr_i2c=0x24

left=0xFE
right=0xFD
up=0xFB
down=0xF7
ok=0xEF
esc=0xDF
nokey=0xFF
key=nokey

#bus.write_byte(0x20, 0x00)

beep.beep()
io_init()
lcd_init()
lcd_ascii168_string(10,0,"This is demo of")

def LCDon():
    bus.write_byte(addr_i2c, 0xBF)

def LCDoff():
    bus.write_byte(addr_i2c, 0xFF)

def getKey():
    act_key=bus.read_byte(addr_i2c)|0xC0
    if (act_key!=key and act_key!=nokey): beep.beep()



LCDon()
try:
    while True:
	getKey()
except KeyboardInterrupt:
    pass


