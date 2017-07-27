#!/usr/bin/python

# this script use AT command to get from modem strenght of signal
# return string contain "*" max signa is 31x"*"
# use one argument who define serial port use modem default is /dev/ttyUSB1
# for this usage modem must be disconnect.
# wite by Ing. Ondrej Lycka

import sys
import serial

#print "len argument", len(sys.argv)
#print sys.argv[1]

if len(sys.argv)<2 : dev="/dev/ttyUSB1"
else: dev=sys.argv[1]
if dev!="/dev/ttyUSB1" : #must be ttyUSB1 (only for Huawei E3372, etc ttyAMA0 du problem freeze program
    print "No modem" 
    exit(0)
s=serial.Serial(port=dev, timeout=0.2)
s.flush()
s.write("AT+CSQ\r\n")
d=s.read(100)
#print "d=",d
#d='\r\n+CSQ: 21,99\r\n\r\n\r\nOK\r\n'
d=d.split() #d=['+CSQ:', '21,99', 'OK']
if len(d)<1 : 
	s.close()
	exit(1)
if d[0]!="+CSQ:" : 
	s.close()
	exit(1)
if len(d)<3 :
	s.close()
	exit(1)
if d[2]!="OK":
	s.close()
	exit(1)
d=d[1] # d="21,99"
d=d.split(',') #d=['21', '99']
d=d[0]
d=int(d)
sys.stdout.write("|")
for i in range(d):
	sys.stdout.write("*")
s.close()
while i<31:
	sys.stdout.write(".")
	i=i+1
sys.stdout.write("|")
sys.stdout.write("\n")
exit(0)



