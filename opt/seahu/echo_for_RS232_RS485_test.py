#!/usr/bin/env python

import time
import serial
import RPi.GPIO as GPIO
import imp

cfg=imp.load_source('config', '/etc/seahu/pin_config.py')

pin=cfg.RS485_RE_DE #11
GPIO.setmode(GPIO.BOARD)
##GPIO.setmode(GPIO.BCM)
GPIO.setup(pin, GPIO.OUT)
GPIO.output(pin, 0) # set RS485 to ransmission (do not affect read RS232)
buf="" # pro fci. getSerialLine()

# zde nepouzivam , pouze jako ukazka alternativni fce.  k serial.readline() - ten vzdy ceka az do konce timeotu
def getSerialLine(): #get line from serial withou wait to timeout
    global buf
    GPIO.output(pin, 0) #receiver
    while 1:
	if buf.find('\n')!=-1 :
	    #print "find endline"
	    line=buf[:buf.find('\n')+1]
	    buf=buf[buf.find('\n')+1:]
	    return line
	buf=buf+ser.read(1)
	#print "buf:", buf

ser = serial.Serial(
    port='/dev/ttyS0',
    #port='/dev/ttyUSB0',
    baudrate = 9600,
    parity=serial.PARITY_NONE,
    stopbits=serial.STOPBITS_ONE,
    bytesize=serial.EIGHTBITS,
    timeout=1
    )
counter=0

while 1:
    # RS232 echo
    while 1:
	GPIO.output(pin, 0) #receiver RS485 = disable RS485 transmit (receive both RS232 and RS485)
	x=ser.readline()
	print 'get', x
	if x=="" : break
	if x=="end\n" : break
	if x=="RS232 echo test\n":
	    time.sleep(0.025) # wait for other side RS485 until will be prepared to receive
	    ser.write(x)
	    ser.flush()
	    print 'send', x
	elif x=="RS485 echo test\n":
	    time.sleep(0.025) # wait for other side until will be prepared to receive
	    GPIO.output(pin, 1) #transmission RS485
	    ser.write(x)
	    ser.flush()
	    print 'send', x

# notice (czech language) 
# Pri RS485 komunikci je potreba po prijeti dat chvili pockat nez se protistrana nastavi na prijem.
# Po prikazu write je dobre spustit flush, ten vse odesle a zaroven ceka na konec odeslani.
# Proto za prikazem flush musu okamzite nastvit pin na prijem a spustit read.

