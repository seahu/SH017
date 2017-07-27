#!/usr/bin/python

#Pokusny program pro sadu dalkove ovladanych zasuvek fi. Kanlux typ APO TM3
# web vyrobku: http://www.kanlux.pl/en/7980
# obsahuje 3KS dalkove ovladanych zasuvek a 1KS dalkovy ovladac pro ovladani az 5-ti zasuvek.
# vysilac i zasuvky maji switch na kterem lze nastvit kod 
# vysilac ma switch s 5 prepinaci on/off a nastavuje se na nem kod sady (tj. az 32 ruznych sad)
# zasuvka ma switch s 10 prepinaci prvnich 5 nastvuje kod sady a dalsich pet kod zasuvky v sade (tj. az 32 zasuvek v jedne sade, celkem se vsemi sadami je mozne rozlisit az 1024 zasuvek)

# Popis ridiciho paketu:
# ----------------------

# |------------------------------- vyslany paket --------------------------------------------------------------------------------------|
# |-------------kod zarizeni------------|  |----------- tlacitka ----------|
# |----kod sady----|  |----kod zasuvky--|  |-tlacitko ON-|  |-tlacitko OFF-| |-ukoncujici bit-| |-vysilaci klid v delce min. 5-ti bitu-|
# ON  OFF ON  ON  ON  OFF ON  OFF OFF OFF  press            no-press          bez funkce         bez funkce
# 11  01  11  11  11  01  00  01  01  01   01               00                0                  X

# PS: aby zasuvka zareagovala je potreba setjny paket poslat besprostredne min. 3x za sebou

# Casovani vyslani jednoho bitu s hod 0:
# --------------------------------------

# |------------------ cca 500us ----------------------|
# |------ 110 us --||---------------- 380 us ---------|


# Casovani vyslani jednoho bitu s hod 1:
# --------------------------------------

# |------------------ cca 500us ----------------------|
# |---------------- 380 us ---------||------ 110 us --|


import RPi.GPIO as GPIO
import time
import sys
import imp

cfg=imp.load_source('config', '/etc/seahu/pin_config.py')


pin_data=cfg.TX_data	#38
GPIO.setmode(GPIO.BOARD)
#GPIO.setmode(GPIO.BCM)
GPIO.setup(pin_data, GPIO.OUT)
t=0.00001

def send_1():
    GPIO.output(pin_data, 1)
    time.sleep(0.00039)
    GPIO.output(pin_data, 0)
    time.sleep(0.00008)

def send_0():
    GPIO.output(pin_data, 1)
    time.sleep(0.00008)
    GPIO.output(pin_data, 0)
    time.sleep(0.00039)

def send(arr):
    GPIO.output(pin_data, 0)
    #time.sleep(0.00350)
    for val in arr:
	if val=="1" : send_1()
	else : send_0()
    GPIO.output(pin_data, 0)
    time.sleep(0.00350)

def pokus2():
    while 1==1:
	send(A_ON)

def pokus(t):
    while 1==1:
	GPIO.output(pin_data, 1)
	time.sleep(t)
	GPIO.output(pin_data, 0)
	time.sleep(t)


#print ("SEND to 433Mhz:")

A_ON ="1111111111000101010101000"
A_OFF="1111111111000101010100010"
B_ON ="1111111111010001010101000"
B_OFF="1111111111010001010100010"
C_ON ="1111111111010100010101000"
C_OFF="1111111111010100010100010"
D_ON ="1111111111010101000101000"
D_OFF="1111111111010101000100010"
E_ON ="1111111111010101010001000" # z ne prilis prukazneho vzorku
E_OFF="1111111111010101010000010"

def pokus2():
    while 1==1:
	send(A_ON)

#pokus(0.00001)
#pokus(0.00038)
#send(A_ON)
#pokus2()

if len(sys.argv)>1 :
    for i in range(5):
    #while 1:
	print "send:",sys.argv[1]
	send(sys.argv[1]+'0') #add zero as end flag


GPIO.output(pin_data, 0)
