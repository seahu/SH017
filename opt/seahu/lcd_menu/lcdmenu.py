#!/usr/bin/python
#
# Created by Alan Aufderheide, February 2013
#
# This provides a menu driven application using the LCD Plates
# from Adafruit Electronics.

import commands
import os
import glob # proprochazeni adresaru
from string import split
from time import sleep, strftime, localtime
from datetime import datetime, timedelta
from xml.dom.minidom import *
#from Adafruit_I2C import Adafruit_I2C
#from Adafruit_MCP230xx import Adafruit_MCP230XX
from Adafruit_CharLCD import Adafruit_CharLCD
from MyListSelector import ListSelector

import smbus

configfile = '/opt/seahu/lcd_menu/lcdmenu.xml'
# set DEBUG=1 for print debug statements
DEBUG = 0
DISPLAY_ROWS = 4
DISPLAY_COLS = 16

# set to 0 if you want the LCD to stay on, 1 to turn off and on auto
AUTO_OFF_LCD = 1

# set busnum param to the correct value for your pi
lcd = Adafruit_CharLCD(DISPLAY_COLS, DISPLAY_ROWS)

# in case you add custom logic to lcd to check if it is connected (useful)
#if lcd.connected == 0:
#    quit()

lcd.set_backlight(True)

# commands

#--------------------------------------------------------------------------------------------------------------
#-------------------------------------------------- HARDWARE COMANDS ------------------------------------------
#--------------------------------------------------------------------------------------------------------------

def IOShowStatus():
    lcd.clear()
    lcd.home()
    status=lcd.readRegister()
    if (status & 0x10) == 0 : lcd.message('Input 1: ON')
    else: lcd.message('Input 1: OFF')
    lcd.set_cursor(0, 1)
    if (status & 0x20) == 0 : lcd.message('Input 2: ON')
    else: lcd.message('Input 2: OFF')
    lcd.set_cursor(0, 2)
    if (status & 0x40) == 0 : lcd.message('Output 1: ON')
    else: lcd.message('Output 1: OFF')
    lcd.set_cursor(0, 3)
    if (status & 0x80) == 0 : lcd.message('Output 2: ON')
    else: lcd.message('Output 2: OFF')
    while 1:
        if (lcd.buttonPressed(lcd.SELECT) or lcd.buttonPressed(lcd.LEFT) or lcd.buttonPressed(lcd.ESC)):
            break
        sleep(0.25)


def RelaysShowStatus():
    lcd.clear()
    lcd.home()
    status=lcd.readRegister()
    if (status & 0x01) == 0 : lcd.message('Realy 1: ON')
    else: lcd.message('Relay 1: OFF')
    lcd.set_cursor(0, 1)
    if (status & 0x02) == 0 : lcd.message('Realy 2: ON')
    else: lcd.message('Relay 2: OFF')
    lcd.set_cursor(0, 2)
    if (status & 0x04) == 0 : lcd.message('Realy 3: ON')
    else: lcd.message('Relay 3: OFF')
    lcd.set_cursor(0, 3)
    if (status & 0x08) == 0 : lcd.message('Realy 4: ON')
    else: lcd.message('Relay 4: OFF')
    while 1:
        if (lcd.buttonPressed(lcd.SELECT) or lcd.buttonPressed(lcd.LEFT) or lcd.buttonPressed(lcd.ESC)):
            break
        sleep(0.25)

# modify lcd.message - if inversion is fasle then print lower string an if inversion true print upper strenig
# I use becouse web view context of lcd ignore inversion
def message(text,inversion=False):
    if inversion==True : lcd.message(text.upper(), inversion)
    else: lcd.message(text.lower(), inversion)

def ControlRealy(cur):
    mask=[1,2,4,8]
    if ( lcd.readRegister() & mask[cur] ) == 0 : inversion=True
    else: inversion=False
    lcd.clear()
    lcd.home()
    lcd.message("REALY "+str(cur+1))
    lcd.set_cursor(0, 2)
    message('ON', inversion)
    lcd.set_cursor(5, 2)
    message('OFF', not(inversion))
    while 1:
        if lcd.buttonPressed(lcd.RIGHT): #OFF
            lcd.writeRegister(lcd.readRegister()|mask[cur]) #send 1
            lcd.set_cursor(0, 2)
            message('ON', False)
            lcd.set_cursor(5, 2)
            message('OFF', True)
        if lcd.buttonPressed(lcd.LEFT): #ON
            lcd.writeRegister( lcd.readRegister() & (mask[cur]^0xFF) ) #send 0
            lcd.set_cursor(0, 2)
            message('ON', True)
            lcd.set_cursor(5, 2)
            message('OFF', False)
        if lcd.buttonPressed(lcd.UP):
            if cur>0 : cur -= 1
            if ( lcd.readRegister() & mask[cur] ) == 0 : inversion=True
            else: inversion=False
            lcd.clear()
            lcd.home()
            lcd.message("REALY "+str(cur+1))
            lcd.set_cursor(0, 2)
            message('ON', inversion)
            lcd.set_cursor(5, 2)
            message('OFF', not(inversion))
        if lcd.buttonPressed(lcd.DOWN):
            if cur<3 : cur += 1
            if ( lcd.readRegister() & mask[cur] ) == 0 : inversion=True
            else: inversion=False
            lcd.clear()
            lcd.home()
            lcd.message("REALY "+str(cur+1))
            lcd.set_cursor(0, 2)
            message('ON', inversion)
            lcd.set_cursor(5, 2)
            message('OFF', not(inversion))
        if lcd.buttonPressed(lcd.SELECT):
            return
        if lcd.buttonPressed(lcd.ESC):
            return
        sleep(0.25)

def  ControlRealy1():
     ControlRealy(0)

def  ControlRealy2():
     ControlRealy(1)

def  ControlRealy3():
     ControlRealy(2)

def  ControlRealy4():
     ControlRealy(3)


def ControlOutput(cur):
    mask=[0x40,0x80]
    if ( lcd.readRegister() & mask[cur] ) == 0 : inversion=True
    else: inversion=False
    lcd.clear()
    lcd.home()
    lcd.message("OUTPUT "+str(cur+1))
    lcd.set_cursor(0, 2)
    message('ON', inversion)
    lcd.set_cursor(5, 2)
    message('OFF', not(inversion))
    while 1:
        if lcd.buttonPressed(lcd.RIGHT): #OFF
            lcd.writeRegister(lcd.readRegister()|mask[cur]) #send 1
            lcd.set_cursor(0, 2)
            message('ON', False)
            lcd.set_cursor(5, 2)
            message('OFF', True)
        if lcd.buttonPressed(lcd.LEFT): #ON
            lcd.writeRegister( lcd.readRegister() & (mask[cur]^0xFF) ) #send 0
            lcd.set_cursor(0, 2)
            message('ON', True)
            lcd.set_cursor(5, 2)
            message('OFF', False)
        if lcd.buttonPressed(lcd.UP):
            if cur>0 : cur -= 1
            if ( lcd.readRegister() & mask[cur] ) == 0 : inversion=True
            else: inversion=False
            lcd.clear()
            lcd.home()
            lcd.message("OUTPUT "+str(cur+1))
            lcd.set_cursor(0, 2)
            message('ON', inversion)
            lcd.set_cursor(5, 2)
            message('OFF', not(inversion))
        if lcd.buttonPressed(lcd.DOWN):
            if cur<1 : cur += 1
            if ( lcd.readRegister() & mask[cur] ) == 0 : inversion=True
            else: inversion=False
            lcd.clear()
            lcd.home()
            lcd.message("OUTPUT "+str(cur+1))
            lcd.set_cursor(0, 2)
            message('ON', inversion)
            lcd.set_cursor(5, 2)
            message('OFF', not(inversion))
        if lcd.buttonPressed(lcd.SELECT):
            return
        if lcd.buttonPressed(lcd.ESC):
            return
        sleep(0.25)

def  ControlOutput1():
     ControlOutput(0)

def  ControlOutput2():
     ControlOutput(1)


def w1_3A(directory):
    files=[directory+"/PIO.A", directory+"/PIO.B"]
    status=[]
    name=["PORT A","PORT B"]
    for i in range(2):
	f=open(files[i],'r')
	status.append( f.read(1) )
	f.close
    cur=0
    display=True
    while 1:
	if lcd.buttonPressed(lcd.UP):
	    if cur>0 :
		cur-=1
		display=True
	if lcd.buttonPressed(lcd.DOWN):
	    if (cur+1)<len(status) :
		cur+=1
		display=True
	if lcd.buttonPressed(lcd.LEFT):
	    if status[cur]=='0': #jepotreba zapnout
		status[cur]='1'
		f=open(files[cur],'w')
		f.write(status[cur])
		f.flush()
		f.close()
		display=True
	if lcd.buttonPressed(lcd.RIGHT):
	    if status[cur]=='1': #jepotreba zapnout
		status[cur]='0'
		f=open(files[cur],'w')
		f.write(status[cur])
		f.flush()
		f.close()
		display=True
	if ( lcd.buttonPressed(lcd.SELECT) or lcd.buttonPressed(lcd.ESC) ):
	    break
	if display==True:
	    lcd.clear()
	    lcd.home()
	    str=os.path.basename(directory)
	    lcd.message(str)
	    lcd.set_cursor(0, 1)
	    lcd.message(name[cur])
	    lcd.set_cursor(0, 2)
	    if status[cur]=='0':
		print "PORT OFF"
		lcd.message("ON", False)
		lcd.set_cursor(5, 2)
		lcd.message("OFF",True)
	    else:
		print "PORT ON"
		lcd.message("ON",True)
		lcd.set_cursor(5, 2)
		lcd.message("OFF",False)
	    display=False
	sleep(0.25)

def w1_28(directory):
    f=open(directory+"/temperature9",'r')
    temp=f.read()
    f.close()
    lcd.clear()
    lcd.home()
    str=os.path.basename(directory)
    lcd.message(str)
    lcd.set_cursor(0, 1)
    lcd.message("temperature:")
    lcd.set_cursor(0, 2)
    lcd.message(temp)
    while 1:
	if ( lcd.buttonPressed(lcd.SELECT) or lcd.buttonPressed(lcd.ESC) ): break
	sleep(0.25)



def w1():
    lcd.clear()
    lcd.home()
    bus=glob.glob("/mnt/1wire/bus.0/*")
    print bus
    bus.remove("/mnt/1wire/bus.0/interface")
    lenBus=len(bus)
    print "len", lenBus
    if (lenBus<1) :
	lcd.message("NO 1WIRE DEVICES")
	sleep(5)
	return
    shift=0
    cur=0
    display=True
    while 1:
	if lcd.buttonPressed(lcd.DOWN):
	    if (cur+1)<lenBus :
		cur+=1
		display=True
		if (shift+DISPLAY_ROWS) <= cur:
		    shift=cur-DISPLAY_ROWS
	if lcd.buttonPressed(lcd.UP):
	    if cur>0 :
		cur-=1
		display=True
		if shift > cur:
		    shift=cur
	if ( lcd.buttonPressed(lcd.RIGHT) or lcd.buttonPressed(lcd.SELECT) ):
	    #ukaz obsah cidla
	    lcd.clear()
	    lcd.home()
	    str=os.path.basename(bus[cur])
	    lcd.message(str)
	    if str[0:2]=="3A" : w1_3A(bus[cur])
	    elif str[0:2]=="28" : w1_28(bus[cur])
	    else:
		lcd.set_cursor(0, 1)
		lcd.message("This device\nnot have display\nsupport")
		while 1:
		    if ( lcd.buttonPressed(lcd.SELECT) or lcd.buttonPressed(lcd.ESC) or lcd.buttonPressed(lcd.LEFT)) : break
		    sleep(0.25)
	    display=True
	if ( lcd.buttonPressed(lcd.LEFT) or lcd.buttonPressed(lcd.ESC) ):
	    return
	if display==True :
	    lcd.clear()
	    lcd.home()
	    for i in range(DISPLAY_ROWS):
		if (shift+i)>=lenBus : break
		lcd.set_cursor(1, i)
		str=os.path.basename(bus[shift+i])
		if (shift+i)==cur : str="-"+str
		else : str=" "+str
		lcd.message(str)
	    display=False
	sleep(0.25)

#--------------------------------------------------------------------------------------------------------------
#-------------------------------------------------- SYSTEM COMANDS ------------------------------------------
#--------------------------------------------------------------------------------------------------------------


def DoQuit():
    lcd.clear()
    lcd.message('Are you sure?\nPress Sel for Y')
    while 1:
        if lcd.buttonPressed(lcd.LEFT):
            break
        if lcd.buttonPressed(lcd.SELECT):
            lcd.clear()
	    lcd.LCDoff()
            quit()
        sleep(0.25)

def DoTestHW():
    commands.getoutput("/opt/seahu/test_hardware.py")

def DoShutdown():
    lcd.clear()
    lcd.message('Are you sure?\nPress Sel for Y')
    while 1:
        if lcd.buttonPressed(lcd.LEFT):
            break
        if lcd.buttonPressed(lcd.SELECT):
            lcd.clear()
	    lcd.LCDoff()
            commands.getoutput("sudo shutdown -h now")
            quit()
        sleep(0.25)

def DoReboot():
    lcd.clear()
    lcd.message('Are you sure?\nPress Sel for Y')
    while 1:
        if lcd.buttonPressed(lcd.LEFT):
            break
        if lcd.buttonPressed(lcd.SELECT):
            lcd.clear()
            lcd.LCDoff()
            commands.getoutput("sudo reboot")
            quit()
        sleep(0.25)

def LcdOff():
    global currentLcd
    currentLcd = lcd.OFF
    lcd.backlight(currentLcd)

def LcdOn():
    global currentLcd
    currentLcd = True
    lcd.set_backlight(currentLcd)


#--------------------------------------------------------------------------------------------------------------
#-------------------------------------------------- DATE TIME COMMANDS ------------------------------------------
#--------------------------------------------------------------------------------------------------------------


def ShowDateTime():
    if DEBUG:
        print('in ShowDateTime')
    lcd.clear()
    while not(lcd.buttonPressed(lcd.LEFT)):
        sleep(0.25)
        lcd.home()
        lcd.message(strftime('%a %b %d %Y\n%I:%M:%S %p', localtime()))
    
def ValidateDateDigit(current, curval):
    # do validation/wrapping
    if current == 0: # Mm
        if curval < 1:
            curval = 12
        elif curval > 12:
            curval = 1
    elif current == 1: #Dd
        if curval < 1:
            curval = 31
        elif curval > 31:
            curval = 1
    elif current == 2: #Yy
        if curval < 1950:
            curval = 2050
        elif curval > 2050:
            curval = 1950
    elif current == 3: #Hh
        if curval < 0:
            curval = 23
        elif curval > 23:
            curval = 0
    elif current == 4: #Mm
        if curval < 0:
            curval = 59
        elif curval > 59:
            curval = 0
    elif current == 5: #Ss
        if curval < 0:
            curval = 59
        elif curval > 59:
            curval = 0
    return curval

def SetDateTime():
    if DEBUG:
        print('in SetDateTime')
    # M D Y H:M:S AM/PM
    curtime = localtime()
    month = curtime.tm_mon
    day = curtime.tm_mday
    year = curtime.tm_year
    hour = curtime.tm_hour
    minute = curtime.tm_min
    second = curtime.tm_sec
    ampm = 0
    if hour > 11:
        hour -= 12
        ampm = 1
    curr = [0,0,0,1,1,1]
    #curc = [2,5,11,1,4,7]
    curc = [0,4,8,0,3,6] #columb position on display
    #curs = [0,4,8,13,16,19] #start_position in date string
    curs = [0,4,8,15,18,21] #start_position in date string
    cure = [3,6,12,17,20,23] #end position  elements of datestring
    curvalues = [month, day, year, hour, minute, second]
    current = 0 # start with month, 0..14

    lcd.clear()
    lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime))
    lcd.blink()
    lcd.set_cursor(curc[current], curr[current])
    sleep(0.5)
    while 1:
        curval = curvalues[current]
        if lcd.buttonPressed(lcd.UP):
            curval += 1
            curvalues[current] = ValidateDateDigit(current, curval)
            curtime = (curvalues[2], curvalues[0], curvalues[1], curvalues[3], curvalues[4], curvalues[5], 0, 0, 0)
            lcd.home()
            lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime))
            lcd.set_cursor(curc[current], curr[current])
            lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime)[curs[current]:cure[current]],True)
            lcd.set_cursor(curc[current], curr[current])
        if lcd.buttonPressed(lcd.DOWN):
            curval -= 1
            curvalues[current] = ValidateDateDigit(current, curval)
            curtime = (curvalues[2], curvalues[0], curvalues[1], curvalues[3], curvalues[4], curvalues[5], 0, 0, 0)
            lcd.home()
            lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime))
            lcd.set_cursor(curc[current], curr[current])
            lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime)[curs[current]:cure[current]],True)
            lcd.set_cursor(curc[current], curr[current])
        if lcd.buttonPressed(lcd.RIGHT):
            current += 1
            if current > 5:
                current = 5
            lcd.home()
            lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime))
            lcd.set_cursor(curc[current], curr[current])
            lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime)[curs[current]:cure[current]],True)
            lcd.set_cursor(curc[current], curr[current])
        if lcd.buttonPressed(lcd.LEFT):
            current -= 1
            if current < 0:
                lcd.noBlink()
                return
            lcd.home()
            lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime))
            lcd.set_cursor(curc[current], curr[current])
            lcd.message(strftime("%b %d, %Y  \n%I:%M:%S %p  ", curtime)[curs[current]:cure[current]],True)
            lcd.set_cursor(curc[current], curr[current])
        if lcd.buttonPressed(lcd.SELECT):
            # set the date time in the system
            lcd.noBlink()
            os.system(strftime('sudo date --set="%d %b %Y %H:%M:%S"', curtime))
            break
        sleep(0.25)

    lcd.noBlink()


#--------------------------------------------------------------------------------------------------------------
#-------------------------------------------------- NETWORK COMANDS ------------------------------------------
#--------------------------------------------------------------------------------------------------------------

# show networ statusk line is selected line of output cammand /opt/seahu/getNetSeting.sh 
# line:
# 0 - mac
# 1 - ip
# 2 - netmask
# 3 - gateway
# 4 - DNS
# 5 - dhcp client (dhcp or static)
def ShowActualyNetworkStatus(title,line):
    lcd.clear()
    lcd.message(title)
    lcd.set_cursor(0,1)
    lcd.message("-"*len(title))
    lcd.set_cursor(0,2)
    val=commands.getoutput("/opt/seahu/getNetSeting.sh").split("\n")[line]
    if len(val)>16:
	lcd.message(val[:16])
	lcd.set_cursor(0,3)
	lcd.message(val[16:])
    else:
	lcd.message(val)
    while 1:
        if ( lcd.buttonPressed(lcd.LEFT) or lcd.buttonPressed(lcd.SELECT) or lcd.buttonPressed(lcd.ESC) ):
            break
        sleep(0.25)


def ShowIPAddress():
    ShowActualyNetworkStatus("IP address:",1)

def ShowNetmask():
    ShowActualyNetworkStatus("Netmask:",2)

def ShowGateway():
    ShowActualyNetworkStatus("Gateway:",3)

def ShowDNS():
    ShowActualyNetworkStatus("DNS server:",4)

def ShowMAC():
    ShowActualyNetworkStatus("Mac address:",0)

def ShowDHCP():
    ShowActualyNetworkStatus("Setting TCP/IP:",5)


# input ip addres
def inputIP(title,addr=""):
    lcd.clear()
    lcd.home()
    lcd.message(title)
    lcd.set_cursor(0,1)
    lcd.message("-"*len(title))
    lcd.set_cursor(0,2)
    if addr=="": addr="0.0.0.0"
    arr=addr.split(".")
    addr=""
    for item in arr:
	while len(item)<3:
	    item="0"+item
	if addr=="": addr=item
	else: addr+="."+item
    CharList=['0','1','2','3','4','5','6','7','8','9']
    curposition = 0
    curCharList=ord(addr[curposition+curposition//3])-ord('0')
    change=True
    while 1:
        if lcd.buttonPressed(lcd.UP):
	    if (curCharList<9) : curCharList += 1
	    else: curCharList=0
	    mcur=curposition+curposition//3
	    addr = addr[:mcur] + CharList[curCharList] + addr[mcur+1:]
	    change=True
        if lcd.buttonPressed(lcd.DOWN):
	    if (curCharList>0): curCharList -= 1
	    else: curCharList=9
	    mcur=curposition+curposition//3
	    addr = addr[:mcur] + CharList[curCharList] + addr[mcur+1:]
	    change=True
        if lcd.buttonPressed(lcd.RIGHT):
            if curposition < 14: curposition += 1
	    curCharList=ord(addr[curposition+curposition//3])-ord('0')
	    change=True
        if lcd.buttonPressed(lcd.LEFT):
	    if curposition >  0: curposition -= 1
	    curCharList=ord(addr[curposition+curposition//3])-ord('0')
	    change=True
        if lcd.buttonPressed(lcd.SELECT):
            # return the word
            break
        if lcd.buttonPressed(lcd.ESC):
            return False

	if change==True:
	    lcd.set_cursor(0,2)
    	    lcd.message(addr)
	    lcd.set_cursor(curposition+curposition//3, 2)
    	    lcd.message(CharList[curCharList],True)
	    change=False
        sleep(0.15)

    arr=addr.split(".")
    addr=""
    check=True
    for item in arr:
	item=int(item)
	if item>255: check=False
	print "item:",item
	if addr=="": addr="%d" %item
	else: addr+=".%d" % item
    if check==False:
	lcd.clear()
	lcd.set_cursor(0,1)
	lcd.message("Numbers must be")
	lcd.set_cursor(0,2)
	lcd.message("in range 0-255")
	sleep(4)
	inputIP(title,addr)
    return addr


def netReadInterfaceSeting():
    ans={'address':'','netmask':'','gateway':'','dns-nameservers':'','dhcp':''}
    f = open('/etc/network/interfaces.d/eth0', 'r')
    for line in f:
	words=line.strip().split(" ")
	if words[0]=="address": ans['address'] = words[1]
	if words[0]=="netmask": ans['netmask'] = words[1]
	if words[0]=="gateway": ans['gateway'] = words[1]
	if words[0]=="dns-nameservers": ans['dns-nameservers'] = words[1]
	if words[0]=="iface": 
	    if words[3]=="dhcp": ans['dhcp'] = True
	    else: ans['dhcp'] = False
    f.close()
    return ans

def netWriteInterfaceSeting(set):
    f = open('/etc/network/interfaces.d/eth0', 'w')
    if set['dhcp']==False:
	f.write("iface eth0 inet static\n")
	for key in set.keys():
	    if key=="dhcp": continue
	    if set[key]=="": continue
	    f.write("	%s %s\n" %(key, set[key]) )
    else:
	f.write("iface eth0 inet dhcp\n")
    f.close

# if set manualy ip seting then automaticaly off dhcp clinet this is hleper hwo inform user about it
def checkOffDHCP(set):
    if set['dhcp']==True:
	lcd.clear()
	lcd.set_cursor(0,1)
	lcd.message("Add seting")
	lcd.set_cursor(0,2)
	lcd.message("No use DHCP")
	sleep(3)
    return False

def SetIPAddress():
    set=netReadInterfaceSeting()
    print set
    ip=set['address']
    if ip=='': ip=commands.getoutput("/opt/seahu/getNetSeting.sh").split("\n")[1]
    newIP=inputIP("IPaddress:",ip)
    if newIP==False: return
    set['address']=newIP
    set['dhcp']=checkOffDHCP(set)
    print set
    netWriteInterfaceSeting(set)

def SetNetmask():
    set=netReadInterfaceSeting()
    print set
    netmask=set['netmask']
    if netmask=='': netmask=commands.getoutput("/opt/seahu/getNetSeting.sh").split("\n")[2]
    newNetmask=inputIP("Netmask:",netmask)
    if newNetmask==False: return
    set['netmask']=newNetmask
    set['dhcp']=checkOffDHCP(set)
    print set
    netWriteInterfaceSeting(set)
    
def SetGateway():
    set=netReadInterfaceSeting()
    print set
    gateway=set['gateway']
    if gateway=='': gateway=commands.getoutput("/opt/seahu/getNetSeting.sh").split("\n")[3]
    newGateway=inputIP("Gateway:",gateway)
    if newGateway==False: return
    set['gateway']=newGateway
    set['dhcp']=checkOffDHCP(set)
    print set
    netWriteInterfaceSeting(set)

def SetDNS():
    set=netReadInterfaceSeting()
    print set
    dns=set['dns-nameservers']
    if dns=='': dns=commands.getoutput("/opt/seahu/getNetSeting.sh").split("\n")[4]
    newDNS=inputIP("DNS:",dns)
    if newDNS==False: return
    set['dns-nameservers']=newDNS
    set['dhcp']=checkOffDHCP(set)
    print set
    netWriteInterfaceSeting(set)

def SetDHCP():
    set=netReadInterfaceSeting()
    dhcp=set['dhcp']
    lcd.clear()
    lcd.home()
    lcd.message("Use DHCP:")
    lcd.set_cursor(0,1)
    lcd.message("---------")
    change=True
    while 1:
	if lcd.buttonPressed(lcd.LEFT):
	    dhcp=False
	    change=True
	if lcd.buttonPressed(lcd.RIGHT):
	    dhcp=True
	    change=True
	if lcd.buttonPressed(lcd.ESC):
	    return False
	if lcd.buttonPressed(lcd.SELECT):
	    break
        if change==True:
	    lcd.set_cursor(0,2)
	    lcd.message("NO ",not dhcp)
	    lcd.set_cursor(5,2)
	    lcd.message("YES",dhcp)
	    change=False
	sleep(0.15)
    set['dhcp']=dhcp
    netWriteInterfaceSeting(set)


#--------------------------------------------------------------------------------------------------------------
#-------------------------------------------------- WIFI COMANDS ------------------------------------------
#--------------------------------------------------------------------------------------------------------------

# show networ status line is selected line of output cammand /opt/seahu/getNetSeting.sh 
# line:
# 0 - mac
# 1 - ip
# 2 - netmask
# 3 - gateway
# 4 - DNS
# 5 - dhcp client (dhcp or static)
# 6 - sid
# 7 - psk

def ShowActualyWifiStatus(title,line):
    lcd.clear()
    lcd.message(title)
    lcd.set_cursor(0,1)
    lcd.message("-"*len(title))
    lcd.set_cursor(0,2)
    val=commands.getoutput("/opt/seahu/getWifiSeting.sh").split("\n")[line]
    if len(val)>16:
	lcd.message(val[:16])
	lcd.set_cursor(0,3)
	lcd.message(val[16:])
    else:
	lcd.message(val)
    while 1:
        if ( lcd.buttonPressed(lcd.LEFT) or lcd.buttonPressed(lcd.SELECT) or lcd.buttonPressed(lcd.ESC) ):
            break
        sleep(0.25)

def WifiShowIPAddress():
    ShowActualyWifiStatus("IP address:",1)

def WifiShowNetmask():
    ShowActualyWifiStatus("Netmask:",2)

def WifiShowGateway():
    ShowActualyWifiStatus("Gateway:",3)

def WifiShowDNS():
    ShowActualyWifiStatus("DNS server:",4)

def WifiShowMAC():
    ShowActualyWifiStatus("Mac address:",0)

def WifiShowDHCP():
    ShowActualyWifiStatus("Setting TCP/IP:",5)

def WifiShowSID():
    ShowActualyWifiStatus("SID:",6)

def WifiShowType():
    ShowActualyWifiStatus("Type:",8)

def WifiShowChannel():
    ShowActualyWifiStatus("Cannel:",9)

def WifiShowType():
    ShowActualyWifiStatus("Forward:",10)

def WifiShowEnable():
    ShowActualyWifiStatus("Enable:",11)

def WifiShowDhcpd():
    ShowActualyWifiStatus("DHCPD:",12)

def WifiShowDhcpdRngeIP1():
    ShowActualyWifiStatus("DHCPD IP from:",13)

def WifiShowDhcpdRngeIP2():
    ShowActualyWifiStatus("DHCPD IP to:",14)

def WifiShowDhcpdNat():
    ShowActualyWifiNat("NAT:",14)

def WifiShowState():
    ShowActualyWifiStatus("State:",16)




def remoteQuote(text):
    if text[0]=="\"" : text=text[1:]
    if text[-1]=="\"" : text=text[:-1]
    return text

def WifiReadInterfaceSeting():
    ans={'ssid':'','psk':'','address':'','netmask':'','gateway':'','dns-nameservers':'','dhcp':''}
    f = open('/etc/wpa_supplicant/wpa_supplicant.conf', 'r')
    for line in f:
	words=line.strip().split("=")
	if len(words)<2 :continue
	if words[0]=="ssid" : ans['ssid'] = remoteQuote(words[1])
	if words[0]=="psk" :  ans['psk'] = remoteQuote(words[1])
    f.close()
    f = open('/etc/network/interfaces.d/wlan0', 'r')
    for line in f:
	words=line.strip().split(" ")
	if words[0]=="address": ans['address'] = words[1]
	if words[0]=="netmask": ans['netmask'] = words[1]
	if words[0]=="gateway": ans['gateway'] = words[1]
	if words[0]=="dns-nameservers": ans['dns-nameservers'] = words[1]
	if words[0]=="iface": 
	    if words[3]=="dhcp": ans['dhcp'] = True
	    else: ans['dhcp'] = False
    f.close()
    return ans

def WifiWriteInterfaceSeting(set):
    f = open('/etc/network/interfaces.d/wlan0', 'w')
    f.write("#wlan0_is_enable\n")
    if set['dhcp']==False:
	f.write("iface wlan0 inet static\n")
	for key in set.keys():
	    if key=="dhcp": continue
	    if key=="ssid": continue
	    if key=="psk": continue
	    if set[key]=="": continue
	    f.write("	%s %s\n" %(key, set[key]) )
    else:
	f.write("iface wlan0 inet dhcp\n")
    f.write("	wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf\n")
    f.close
    f = open('/etc/wpa_supplicant/wpa_supplicant.conf', 'w')
    f.write("country=GB\n")
    f.write("ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n")
    f.write("update_config=1\n")
    f.write("network={\n")
    f.write("	ssid=\"%s\"\n" %set['ssid'])
    if set['psk']=="":
	f.write("	key_mgmt=NONE\n")
    else :
	f.write("	psk=\"%s\"\n" %set['psk'])
    f.write("}\n")
    f.close()

def WifiSetSID():
    set=WifiReadInterfaceSeting()
    lcd.clear();
    lcd.set_cursor(0,1)
    lcd.message("Plese wait:\nto scan wifi")
    lines=commands.getoutput("/opt/seahu/scanWifi.sh").split("\n")
    list = []
    for line in lines:
	line=line.split(";")
	# 1234567890123456
	# sidName   -99K
	if len(line)!=7 : continue
	sid=line[1]
	strength=line[5]
	encrypt=line[6]
	print "sid", sid
	print "strength", strength
	print "encrypt", encrypt
	ent=sid[:9]
	ent += " "*(14-len(ent)-len(strength)-1)
	ent += strength
	if encrypt=="on": ent += "K"
	else: ent += "F"
	list.append([ent,sid,strength,encrypt])
    selector = ListSelector(list, lcd)
    cur = selector.Pick()
    # do something useful
    if cur==False: return False
    cur=cur[0] # slector.Pick return cursor as list with one item
    sid=list[cur][1]
    encrypt=list[cur][3]
    print "list", list
    passwd=""
    if (encrypt=="on"):
	passwd=GetWord("Passowrd:",set['psk'])
	if passwd==False : return False
    set['ssid']=sid
    set['psk']=passwd
    WifiWriteInterfaceSeting(set)

def WifiSetIPAddress():
    set=WifiReadInterfaceSeting()
    print set
    ip=set['address']
    if ip=='': ip=commands.getoutput("/opt/seahu/getWifiSeting.sh").split("\n")[1]
    newIP=inputIP("IPaddress:",ip)
    if newIP==False: return
    set['address']=newIP
    set['dhcp']=checkOffDHCP(set)
    print set
    WifiWriteInterfaceSeting(set)

def WifiSetNetmask():
    set=WifiReadInterfaceSeting()
    print set
    netmask=set['netmask']
    if netmask=='': netmask=commands.getoutput("/opt/seahu/getWifiSeting.sh").split("\n")[2]
    newNetmask=inputIP("Netmask:",netmask)
    if newNetmask==False: return
    set['netmask']=newNetmask
    set['dhcp']=checkOffDHCP(set)
    print set
    WifiWriteInterfaceSeting(set)
    
def WifiSetGateway():
    set=WifiReadInterfaceSeting()
    print set
    gateway=set['gateway']
    if gateway=='': gateway=commands.getoutput("/opt/seahu/getWifiSeting.sh").split("\n")[3]
    newGateway=inputIP("Gateway:",gateway)
    if newGateway==False: return
    set['gateway']=newGateway
    set['dhcp']=checkOffDHCP(set)
    print set
    WifiWriteInterfaceSeting(set)

def WifiSetDNS():
    set=WifiReadInterfaceSeting()
    print set
    dns=set['dns-nameservers']
    if dns=='': dns=commands.getoutput("/opt/seahu/getWifiSeting.sh").split("\n")[4]
    newDNS=inputIP("DNS:",dns)
    if newDNS==False: return
    set['dns-nameservers']=newDNS
    set['dhcp']=checkOffDHCP(set)
    print set
    WifiWriteInterfaceSeting(set)

def WifiSetDHCP():
    set=WifiReadInterfaceSeting()
    dhcp=set['dhcp']
    lcd.clear()
    lcd.home()
    lcd.message("Use DHCP:")
    lcd.set_cursor(0,1)
    lcd.message("---------")
    change=True
    while 1:
	if lcd.buttonPressed(lcd.LEFT):
	    dhcp=False
	    change=True
	if lcd.buttonPressed(lcd.RIGHT):
	    dhcp=True
	    change=True
	if lcd.buttonPressed(lcd.ESC):
	    return False
	if lcd.buttonPressed(lcd.SELECT):
	    break
        if change==True:
	    lcd.set_cursor(0,2)
	    lcd.message("NO ",not dhcp)
	    lcd.set_cursor(5,2)
	    lcd.message("YES",dhcp)
	    change=False
	sleep(0.15)
    set['dhcp']=dhcp
    WifiWriteInterfaceSeting(set)

#--------------------------------------------------------------------------------------------------------------
#------------------------------------ NEW EASY WIFI FUNCTION  ------------------------------------------
#--------------------------------------------------------------------------------------------------------------

def WifiInfo():
    lines=commands.getoutput("/opt/seahu/getWifiSeting.sh").split("\n")
    lcd.clear()
    lcd.set_cursor(0,0)
    lcd.message("Wi-Fi - "+lines[8] )
    if lines[16]=="UP":
	lcd.set_cursor(0,1)
        lcd.message("IP:\n"+lines[1]+"/\n"+lines[2] )
    else:
	lcd.set_cursor(0,2)
	lcd.message("DOWN" )
    waitToAnyKey()


def WifiSetCilentSID():
    if WifiSetSID()==False : 
	return False #set   set['ssid'], set['psk']
    set=WifiReadInterfaceSeting()
    set['dhcp']=True
    lcd.clear()
    lcd.home()
    lcd.message("STOP\nNETWORK\nPLEASE WAIT")
    commands.getoutput("/sbin/ifdown wlan0")

    WifiWriteInterfaceSeting(set)

    # unset dhcpd
    f = open("/etc/dnsmasq.d/dnsmasq-wlan0.conf", 'w')
    f.write("#wlan0_is_enable\n")
    f.write("interface=wlan0\n")
    f.write("no-dhcp-interface=wlan0\n")
    f.write("dhcp-range=192.168.128.50,192.168.128.200,12h\n")
    f.close()

    # unset forwarding
    commands.getoutput("/sbin/sysctl -w net.ipv4.ip_forward=0")
    commands.getoutput("/bin/sed -i 's/^net.ipv4.ip_forward=1/#net.ipv4.ip_forward=1/g' /etc/sysctl.conf")

    # unset NAT
    f = open("/etc/seahu/firewall.sh", 'w')
    f.write("#!/bin/bash\n\n")
    f.write("# set SEAHU firewall\n")
    f.write("/sbin/iptables --flush\n")
    f.write("/sbin/iptables --delete-chain\n")
    f.write("/sbin/iptables --table nat --flush\n")
    f.write("/sbin/iptables --table nat --delete-chain\n")
    f.close()

    lcd.clear()
    lcd.home()
    lcd.message("STARTING\nNETWORK\nPLEASE WAIT")
    #lcd.set_cursor(0,1)

    commands.getoutput("/opt/seahu/restartWifi.sh")
    lcd.clear()
    lcd.home()
    lcd.message("OK")

def WifiSetApSID():

    f = open('/etc/hostapd/hostapd.conf', 'r')
    for line in f:
	words=line.strip().split("=")
	if len(words)<2 :continue
	if words[0]=="ssid" : SSID = words[1]
	if words[0]=="wpa_passphrase" :  PSK = words[1]
    f.close()

    newSSID=GetWord("SSID:",SSID)
    #newSSID=inputIP("SSID:","SSID")
    if newSSID==False:
	commands.getoutput("/sbin/ifup wlan0")
	return False #set   set['ssid'], set['psk']
    newPSK=GetWord("PASSWORD:",PSK)
    if newPSK==False:
	commands.getoutput("/sbin/ifup wlan0")
	return False #set   set['ssid'], set['psk']

    lcd.clear()
    lcd.home()
    lcd.message("STOP\nNETWORK\nPLEASE WAIT")

    commands.getoutput("/sbin/ifdown wlan0")

    # set interface
    f = open('/etc/network/interfaces.d/wlan0', 'w')
    f.write("#wlan0_is_enable\n")
    f.write("allow-hotplug wlan0\n")
    f.write("iface wlan0 inet static\n")
    f.write("	address 192.168.128.1\n")
    f.write("	netmask 255.255.255.0\n")
    f.write("	hostapd /etc/hostapd/hostapd.conf\n")
    f.close()

    # set hostapd
    f = open("/etc/hostapd/hostapd.conf", 'w')
    f.write("interface=wlan0\n")
    f.write("driver=nl80211\n")
    f.write("ssid=%s\n" %newSSID)
    f.write("hw_mode=g\n")
    f.write("channel=6\n")
    f.write("macaddr_acl=0\n")
    f.write("ignore_broadcast_ssid=0\n")
    f.write("auth_algs=1\n")
    f.write("wpa=3\n")
    f.write("wpa_passphrase=%s\n" %newPSK)
    f.write("wpa_key_mgmt=WPA-PSK\n")
    f.write("wpa_pairwise=TKIP\n")
    f.write("rsn_pairwise=CCMP\n")
    f.close()

    # set dhcpd
    f = open("/etc/dnsmasq.d/dnsmasq-wlan0.conf", 'w')
    f.write("#wlan0_is_enable\n")
    f.write("interface=wlan0\n")
    f.write("dhcp-range=192.168.128.50,192.168.128.200,12h\n")
    f.close()

    # set forwarding
    commands.getoutput("/sbin/sysctl -w net.ipv4.ip_forward=1")
    commands.getoutput("/bin/sed -i 's/#net.ipv4.ip_forward=1/net.ipv4.ip_forward=1/g' /etc/sysctl.conf")

    # set NAT
    f = open("/etc/seahu/firewall.sh", 'w')
    f.write("#!/bin/bash\n\n")
    f.write("# set SEAHU firewall\n")
    f.write("/sbin/iptables --flush\n")
    f.write("/sbin/iptables --delete-chain\n")
    f.write("/sbin/iptables --table nat --flush\n")
    f.write("/sbin/iptables --table nat --delete-chain\n")
    f.write("/sbin/iptables --table nat --append POSTROUTING ! --out-interface wlan0 -j MASQUERADE\n")
    f.write("/sbin/iptables --append FORWARD --in-interface wlan0 -j ACCEPT\n")
    f.close()

    lcd.clear()
    lcd.home()
    lcd.message("STARTING\nNETWORK\nPLEASE WAIT")
    #lcd.set_cursor(0,1)

    commands.getoutput("/opt/seahu/restartWifi.sh")
    lcd.clear()
    lcd.home()
    lcd.message("OK")

def DisableWifi():
    lcd.clear()
    lcd.home()
    lcd.message("STOP\nNETWORK\nPLEASE WAIT")

    commands.getoutput("/sbin/ifdown wlan0")

    f = open('/etc/network/interfaces.d/wlan0', 'w')
    f.write("#wlan0_is_disable\n")
    f.close

    f = open('/etc/dnsmasq.d/dnsmasq-wlan0.conf', 'w')
    f.write("#wlan0_is_disable\n")
    f.close
    
    commands.getoutput("/opt/seahu/restartWifi.sh")

#--------------------------------------------------------------------------------------------------------------
#------------------------------------ OTHER FUNCTION (some example) ------------------------------------------
#--------------------------------------------------------------------------------------------------------------


#only use the following if you find useful
def Use10Network():
    "Allows you to switch to a different network for local connection"
    lcd.clear()
    lcd.message('Are you sure?\nPress Sel for Y')
    while 1:
        if lcd.buttonPressed(lcd.LEFT):
            break
        if lcd.buttonPressed(lcd.SELECT):
            # uncomment the following once you have a separate network defined
            #commands.getoutput("sudo cp /etc/network/interfaces.hub.10 /etc/network/interfaces")
            lcd.clear()
            lcd.message('Please reboot')
            sleep(1.5)
            break
        sleep(0.25)

#only use the following if you find useful
def UseDHCP():
    "Allows you to switch to a network config that uses DHCP"
    lcd.clear()
    lcd.message('Are you sure?\nPress Sel for Y')
    while 1:
        if lcd.buttonPressed(lcd.LEFT):
            break
        if lcd.buttonPressed(lcd.SELECT):
            # uncomment the following once you get an original copy in place
            #commands.getoutput("sudo cp /etc/network/interfaces.orig /etc/network/interfaces")
            lcd.clear()
            lcd.message('Please reboot')
            sleep(1.5)
            break
        sleep(0.25)

def ShowLatLon():
    if DEBUG:
        print('in ShowLatLon')

def SetLatLon():
    if DEBUG:
        print('in SetLatLon')
    
def SetLocation():
    if DEBUG:
        print('in SetLocation')
    global lcd
    list = []
    # coordinates usable by ephem library, lat, lon, elevation (m)
    list.append(['New York', '40.7143528', '-74.0059731', 9.775694])
    list.append(['Paris', '48.8566667', '2.3509871', 35.917042])
    selector = ListSelector(list, lcd)
    item = selector.Pick()
    # do something useful
    if (item >= 0):
        chosen = list[item]

def CompassGyroViewAcc():
    if DEBUG:
        print('in CompassGyroViewAcc')

def CompassGyroViewMag():
    if DEBUG:
        print('in CompassGyroViewMag')

def CompassGyroViewHeading():
    if DEBUG:
        print('in CompassGyroViewHeading')

def CompassGyroViewTemp():
    if DEBUG:
        print('in CompassGyroViewTemp')

def CompassGyroCalibrate():
    if DEBUG:
        print('in CompassGyroCalibrate')
    
def CompassGyroCalibrateClear():
    if DEBUG:
        print('in CompassGyroCalibrateClear')
    
def TempBaroView():
    if DEBUG:
        print('in TempBaroView')

def TempBaroCalibrate():
    if DEBUG:
        print('in TempBaroCalibrate')
    
def AstroViewAll():
    if DEBUG:
        print('in AstroViewAll')

def AstroViewAltAz():
    if DEBUG:
        print('in AstroViewAltAz')
    
def AstroViewRADecl():
    if DEBUG:
        print('in AstroViewRADecl')

def CameraDetect():
    if DEBUG:
        print('in CameraDetect')
    
def CameraTakePicture():
    if DEBUG:
        print('in CameraTakePicture')

def CameraTimeLapse():
    if DEBUG:
        print('in CameraTimeLapse')

# Get a word from the UI, a character at a time.
# Click select to complete input, or back out to the left to quit.
# Return the entered word, or None if they back out.
def GetWord(title="",word="A"):
    if word=="":
	word=" "
    lcd.clear()
    lcd.home()
    lcd.message(title)
    lcd.set_cursor(0,1)
    lcd.message("-"*len(title))
    curword = list()
    for c in word:
	curword.append(c)
    curposition = 0
    change=True
    while 1:
        if lcd.buttonPressed(lcd.UP):
            if (ord(curword[curposition]) < 126):
                curword[curposition] = chr(ord(curword[curposition])+1)
            else:
                curword[curposition] = chr(32)
	    change=True
        if lcd.buttonPressed(lcd.DOWN):
            if (ord(curword[curposition]) > 32):
                curword[curposition] = chr(ord(curword[curposition])-1)
            else:
                curword[curposition] = chr(126)
	    change=True
        if lcd.buttonPressed(lcd.RIGHT):
            if curposition < DISPLAY_COLS - 1:
                if curposition == len(curword) - 1: curword.append('A')
                curposition += 1
	    change=True
        if lcd.buttonPressed(lcd.LEFT):
	    if curposition >  0:
                curposition -= 1
	    change=True
        if lcd.buttonPressed(lcd.ESC):
            return False
        if lcd.buttonPressed(lcd.SELECT):
            # return the word
            sleep(0.2)
            return ''.join(curword)
	if change==True:
    	    #lcd.home()
	    lcd.set_cursor(0, 2)
    	    lcd.message(''.join(curword))
    	    lcd.set_cursor(curposition, 2)
    	    lcd.message(curword[curposition],True)
    	    #lcd.set_cursor(curposition, 2)
	    change=False
        sleep(lcd.buttonSleep)

#wait toany key
def waitToAnyKey():
    lcdstart = datetime.now()
    while 1:
	if ( lcd.buttonPressed(lcd.LEFT) or lcd.buttonPressed(lcd.RIGHT) or lcd.buttonPressed(lcd.UP) or lcd.buttonPressed(lcd.DOWN) or lcd.buttonPressed(lcd.SELECT) or lcd.buttonPressed(lcd.ESC) ): break
        if AUTO_OFF_LCD:
	    lcdtmp = lcdstart + timedelta(seconds=30)
	    if (datetime.now() > lcdtmp):
		lcd.set_backlight(False)
	sleep(0.15)


# An example of how to get a word input from the UI, and then
# do something with it
def EnterWord():
    if DEBUG:
        print('in EnterWord')
    word = GetWord()
    lcd.clear()
    lcd.home()
    if word is not None:
        lcd.message('>'+word+'<')
        sleep(5)

class CommandToRun:
    def __init__(self, myName, theCommand):
        self.text = myName
        self.commandToRun = theCommand
    def Run(self):
        self.clist = split(commands.getoutput(self.commandToRun), '\n')
        if len(self.clist) > 0:
            lcd.clear()
            lcd.message(self.clist[0])
            for i in range(1, len(self.clist)):
                while 1:
                    if lcd.buttonPressed(lcd.DOWN):
                        break
                    sleep(0.25)
                lcd.clear()
                lcd.message(self.clist[i-1]+'\n'+self.clist[i])          
                sleep(0.5)
        while 1:
            if lcd.buttonPressed(lcd.LEFT):
                break

class Widget:
    def __init__(self, myName, myFunction):
        self.text = myName
        self.function = myFunction
        
class Folder:
    def __init__(self, myName, myParent):
        self.text = myName
        self.items = []
        self.parent = myParent

def HandleSettings(node):
    global lcd
    if node.getAttribute('lcdColor').lower() == 'red':
        LcdRed()
    elif node.getAttribute('lcdColor').lower() == 'green':
        LcdGreen()
    elif node.getAttribute('lcdColor').lower() == 'blue':
        LcdBlue()
    elif node.getAttribute('lcdColor').lower() == 'yellow':
        LcdYellow()
    elif node.getAttribute('lcdColor').lower() == 'teal':
        LcdTeal()
    elif node.getAttribute('lcdColor').lower() == 'violet':
        LcdViolet()
    elif node.getAttribute('lcdColor').lower() == 'white':
        LcdOn()
    if node.getAttribute('lcdBacklight').lower() == 'on':
        LcdOn()
    elif node.getAttribute('lcdBacklight').lower() == 'off':
        LcdOff()

def ProcessNode(currentNode, currentItem):
    children = currentNode.childNodes

    for child in children:
        if isinstance(child, xml.dom.minidom.Element):
            if child.tagName == 'settings':
                HandleSettings(child)
            elif child.tagName == 'folder':
                thisFolder = Folder(child.getAttribute('text'), currentItem)
                currentItem.items.append(thisFolder)
                ProcessNode(child, thisFolder)
            elif child.tagName == 'widget':
                thisWidget = Widget(child.getAttribute('text'), child.getAttribute('function'))
                currentItem.items.append(thisWidget)
            elif child.tagName == 'run':
                thisCommand = CommandToRun(child.getAttribute('text'), child.firstChild.data)
                currentItem.items.append(thisCommand)

class Display:
    def __init__(self, folder):
        self.curFolder = folder
        self.curTopItem = 0
        self.curSelectedItem = 0
    def display(self):
        if self.curTopItem > len(self.curFolder.items) - DISPLAY_ROWS:
            self.curTopItem = len(self.curFolder.items) - DISPLAY_ROWS
        if self.curTopItem < 0:
            self.curTopItem = 0
        if DEBUG:
            print('------------------')
        str = ''
        for row in range(self.curTopItem, self.curTopItem+DISPLAY_ROWS):
            if row > self.curTopItem:
                str += '\n'
            if row < len(self.curFolder.items):
                if row == self.curSelectedItem:
                    cmd = '-'+self.curFolder.items[row].text
                    if len(cmd) < 16:
                        for row in range(len(cmd), 16):
                            cmd += ' '
                    if DEBUG:
                        print('|'+cmd+'|')
                    str += cmd
                else:
                    cmd = ' '+self.curFolder.items[row].text
                    if len(cmd) < 16:
                        for row in range(len(cmd), 16):
                            cmd += ' '
                    if DEBUG:
                        print('|'+cmd+'|')
                    str += cmd
        if DEBUG:
            print('------------------')
        lcd.home()
        lcd.message(str)

    def update(self, command):
        global currentLcd
        global lcdstart
        lcd.set_backlight(currentLcd)
        lcdstart = datetime.now()
        if DEBUG:
            print('do',command)
        if command == 'u':
            self.up()
        elif command == 'd':
            self.down()
        elif command == 'r':
            self.right()
        elif command == 'l':
            self.left()
        elif command == 's':
            self.select()
    def up(self):
        if self.curSelectedItem == 0:
            return
        elif self.curSelectedItem > self.curTopItem:
            self.curSelectedItem -= 1
        else:
            self.curTopItem -= 1
            self.curSelectedItem -= 1
    def down(self):
        if self.curSelectedItem+1 == len(self.curFolder.items):
            return
        elif self.curSelectedItem < self.curTopItem+DISPLAY_ROWS-1:
            self.curSelectedItem += 1
        else:
            self.curTopItem += 1
            self.curSelectedItem += 1
    def left(self):
        lcd.clear()
        if isinstance(self.curFolder.parent, Folder):
            # find the current in the parent
            itemno = 0
            index = 0
            for item in self.curFolder.parent.items:
                if self.curFolder == item:
                    if DEBUG:
                        print('foundit')
                    index = itemno
                else:
                    itemno += 1
            if index < len(self.curFolder.parent.items):
                self.curFolder = self.curFolder.parent
                self.curTopItem = index
                self.curSelectedItem = index
            else:
                self.curFolder = self.curFolder.parent
                self.curTopItem = 0
                self.curSelectedItem = 0
    def right(self):
        lcd.clear()
        if isinstance(self.curFolder.items[self.curSelectedItem], Folder):
            self.curFolder = self.curFolder.items[self.curSelectedItem]
            self.curTopItem = 0
            self.curSelectedItem = 0
        elif isinstance(self.curFolder.items[self.curSelectedItem], Widget):
            if DEBUG:
                print('eval', self.curFolder.items[self.curSelectedItem].function)
            eval(self.curFolder.items[self.curSelectedItem].function+'()')
        elif isinstance(self.curFolder.items[self.curSelectedItem], CommandToRun):
            self.curFolder.items[self.curSelectedItem].Run()

    def select(self):
        lcd.clear()
        if DEBUG:
            print('check widget')
        if isinstance(self.curFolder.items[self.curSelectedItem], Widget):
            if DEBUG:
                print('eval', self.curFolder.items[self.curSelectedItem].function)
            eval(self.curFolder.items[self.curSelectedItem].function+'()')



# now start things up
#SetIPAddress()
lcd.image("logo.tif")
waitToAnyKey()

uiItems = Folder('root','')

dom = parse(configfile) # parse an XML file by name

top = dom.documentElement

currentLcd = False
lcd.set_backlight(False)

ProcessNode(top, uiItems)

display = Display(uiItems)
display.display()

if DEBUG:
    print('start while')

lcdstart = datetime.now()
while 1:
    if (lcd.buttonPressed(lcd.LEFT) or lcd.buttonPressed(lcd.ESC)):
       	display.update('l')
        display.display()
        sleep(0.25)

    if (lcd.buttonPressed(lcd.UP)):
        display.update('u')
        display.display()
        sleep(0.25)

    if (lcd.buttonPressed(lcd.DOWN)):
        display.update('d')
        display.display()
        sleep(0.25)

    if (lcd.buttonPressed(lcd.RIGHT) or lcd.buttonPressed(lcd.SELECT)):
        display.update('r')
        display.display()
        sleep(0.25)

    if (lcd.buttonPressed(lcd.SELECT)):
        display.update('s')
        display.display()
        sleep(0.25)

    if AUTO_OFF_LCD:
        lcdtmp = lcdstart + timedelta(seconds=30)
        if (datetime.now() > lcdtmp):
            lcd.set_backlight(False)
    sleep(0.15)
