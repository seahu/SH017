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
import urllib2
import imp

cfg=imp.load_source('config', '/etc/seahu/pin_config.py')


pin_data=cfg.RX_data	#40
GPIO.setmode(GPIO.BOARD)
#GPIO.setmode(GPIO.BCM)
GPIO.setup(pin_data, GPIO.IN)
FALSE=-1
end=False

# priprava pole s tlacitky
set = [1,1,1,1,1,1,1,1,1,1] #kod sady nastavovany na dalkovem ovladaci
A   = [0,0,0,1,0,1,0,1,0,1] #kody jednotlivych tlacitek
B   = [0,1,0,0,0,1,0,1,0,1]
C   = [0,1,0,1,0,0,0,1,0,1]
D   = [0,1,0,1,0,1,0,0,0,1]
E   = [0,1,0,1,0,1,0,1,0,0]
ON  = [0,1,0,0] #kody ON/OFF
OFF = [0,0,0,1]

A_ON  = set+A+ON #seataveni celeho kodu
A_OFF = set+A+OFF
B_ON  = set+B+ON
B_OFF = set+B+OFF
C_ON  = set+C+ON
C_OFF = set+C+OFF
D_ON  = set+D+ON
D_OFF = set+D+OFF
E_ON  = set+E+ON
E_OFF = set+E+OFF

#konecne pole s kody pro tlacitka
buttons = { "A_ON":A_ON, "A_OFF":A_OFF,
	    "B_ON":B_ON, "B_OFF":B_OFF,
	    "C_ON":C_ON, "C_OFF":C_OFF,
	    "D_ON":D_ON, "D_OFF":D_OFF,
	    "E_ON":E_ON, "E_OFF":E_OFF }

def get_bit():
    time0=time.time()
    while True: #cekam na 1
	if GPIO.input(pin_data)==1: break
	if (time.time()-time0)>0.002: 
	    #print "konec"
	    return FALSE #konec paketu
	time.sleep(0.000001)
    time1=time.time() #zactek 1
    while True:
	if GPIO.input(pin_data)==0: break
	time.sleep(0.000001)
    time2=time.time() #konec 1 zacatek 0
    while True:
	if GPIO.input(pin_data)==1: break
	#if (time.time()-time1)>0.0007: break #toto odkomentovat v pripde ze bych ctel, aby soucasti kodu byla i ukoncujici 0
	time.sleep(0.000001)
    time3=time.time() #konec 0
    if (time3-time1)>0.0009 : return FALSE
    if (time3-time1)<0.0004 : return FALSE
    #print (time1-time0)
    #print (time3-time1)
    #print (time2-time1)
    if (time2-time1)>0.00025 :
	#print 1
	return 1
    else:
	#print 0
	return 0

def get_one_packet():
    packet=[]
    #end=False
    while True:
	bit=get_bit()
	if bit==FALSE : break
	packet.append(bit)
	#if end==True : break
    if len(packet)==0:
	#print "FALSE PACKET"
	return FALSE
    else : 
	#print packet
	return packet

def get_packet(lenght,repeate=3):
    packet=get_one_packet()
    #print "NEW"
    if packet==FALSE : return FALSE
    for i in range(repeate-1):
	new_packet=get_one_packet()
	if new_packet!=packet :
	    #print "FALSE REPEATE"
	    return FALSE
    return packet


def main_cycle():
    while 1==1:
	p=get_packet(25)
	if p==FALSE : continue
	if len(p)<5 : continue
	t=""
	for n in p:
	    t=t+str(n)
	print t
	sys.stdout.flush() #stdout is buffered, but I need pipe output to input another program immediately when code is detect
	#url="http://localhost/rfx433Mhz/homepage/serve-rfx?code="+t
	#print url
	#urllib2.urlopen(url).read()

main_cycle()
