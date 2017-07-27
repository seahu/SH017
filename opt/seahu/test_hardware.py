#!/usr/bin/python

# utility for test hardware Seahu SH017

import RPi.GPIO as GPIO
import smbus
import glob # proprochazeni adresaru
import socket
import time
import serial
import imp

cfg=imp.load_source('config', '/etc/seahu/pin_config.py')

passwd='OK'
base_board=cfg.addr2_i2c 	#0x38
display_board=cfg.addr_i2c 	#0x3c
left=0x01^0xFF
right=0x02^0xFF
up=0x04^0xFF
down=0x08^0xFF
ok=0x10^0xFF
esc=0x20^0xFF


GPIO.setmode(GPIO.BOARD)
bus = smbus.SMBus(1)

def main():
	#backligh on
	bus.write_byte(display_board, 0xFF)

	# test display board

	#test keys left
	lcd('c')
	lcd('p,01,01,press key left')
	if wait_for_key()!=left:
		lcd('c')
		lcd('p,01,01, Left key\ntest is FALSE')
		return
		
	#test keys right
	lcd('c')
	lcd('p,01,01,press key right')
	if wait_for_key()!=right:
		lcd('c')
		lcd('p,01,01, Right key\ntest is FALSE')
		return

	#test keys up
	lcd('c')
	lcd('p,01,01,press key up')
	if wait_for_key()!=up:
		lcd('c')
		lcd('p,01,01, Up key\ntest is FALSE')
		return

	#test keys down
	lcd('c')
	lcd('p,01,01,press key down')
	if wait_for_key()!=down:
		lcd('c')
		lcd('p,01,01, Down key\ntest is FALSE')
		return

	#test keys ok
	lcd('c')
	lcd('p,01,01,press key OK')
	if wait_for_key()!=ok:
		lcd('c')
		lcd('p,01,01, OK key\ntest is FALSE')
		return

	#test keys esc
	lcd('c')
	lcd('p,01,01,press key ESC')
	if wait_for_key()!=esc:
		lcd('c')
		lcd('p,01,01, ESC key\ntest is FALSE')
		return
		
	#test backligh off
	bus.write_byte(display_board, 0x40^0xFF)
	time.sleep(0.5)
	bus.write_byte(display_board, 0xFF)
	if check('display blink?')==False : exit(1)
	

	#test backligh beeb
	#bus.write_byte(display_board, 0x80^0xFF) # use beeb service simulate beeb button oreginaly beeb is controled direc by raspberryPI GPIO
	#if check('beep ON')==False : exit(1)
	#bus.write_byte(display_board, 0xFF)

	# --- test base board -----

	# test relay on base board
	bus.write_byte(base_board, 0x01^0xFF)
	if check('relay 1 set ON')==False : return
	bus.write_byte(base_board, 0x02^0xFF)
	if check('relay 2 set ON')==False : return
	bus.write_byte(base_board, 0x04^0xFF)
	if check('relay 3 set ON')==False : return
	bus.write_byte(base_board, 0x08^0xFF)
	if check('relay 4 set ON')==False : return
	bus.write_byte(base_board, 0x40^0xFF)
	if check('out 1 set ON')==False : return
	bus.write_byte(base_board, 0x80^0xFF)
	if check('out 2 set ON')==False : return

	# test input1
	lcd('c')
	lcd('p,00,00,* test INPUT 1 *\nvalue=?\n\npress OK or ESC')
	while 1:
		time.sleep(0.1)
		x=get_bit_of_io_expander(base_board, 4)
		lcd('p,06,01,'+str(x))
		key=bus.read_byte(display_board)|0xC0
		if (key)!=0xFF : beep()
		if key==ok: break
		if key==esc:
			lcd('c')
			lcd('p,01,01, INPUT1\nis FALSE')
			return False

	# test input2
	lcd('c')
	lcd('p,00,00,* test INPUT 1 *\nvalue=?\n\npress OK or ESC')
	while 1:
		time.sleep(0.1)
		x=get_bit_of_io_expander(base_board, 5)
		lcd('p,06,01,'+str(x))
		key=bus.read_byte(display_board)|0xC0
		if (key)!=0xFF : beep()
		if key==ok: break
		if key==esc:
			lcd('c')
			lcd('p,01,01, INPUT2\nis FALSE')
			return False

	# test output1
	#lcd('c')
	#lcd('p,00,00,* test OUTPUT 1\nvalue=?\npress UP to test\npress OK or ESC')
	#while 1:
	#	time.sleep(0.1)
	#	x=get_bit_of_io_expander(base_board, 6)
	#	lcd('p,06,01,'+str(x))
	#	key=bus.read_byte(display_board)|0xC0
	#	if key==0xFF:
	#		bus.write_byte(base_board, 0x00^0xFF)
	#	if key!=0xFF and key!=up: beep()
	#	if key==up:
	#		bus.write_byte(base_board, 0x40^0xFF)
	#	if key==ok: break
	#	if key==esc:
	#		lcd('c')
	#		lcd('p,01,01, OUTPUT1\nis FALSE')
	#		return False

	# test output2
	#lcd('c')
	#lcd('p,00,00,* test OUTPUT 2\nvalue=?\npress UP to test\npress OK or ESC')
	#while 1:
	#	time.sleep(0.1)
	#	x=get_bit_of_io_expander(base_board, 7)
	#	lcd('p,06,01,'+str(x))
	#	key=bus.read_byte(display_board)|0xC0
	#	if key==0xFF:
	#		bus.write_byte(base_board, 0x00^0xFF)
	#	if key!=0xFF and key!=up: beep()
	#	if key==up:
	#		bus.write_byte(base_board, 0x80^0xFF)
	#	if key==ok: break
	#	if key==esc:
	#		lcd('c')
	#		lcd('p,01,01, OUTPUT2\nis FALSE')
	#		return False

	# test real clock
	rtc_addr=0x51
	_REG_YEAR = 0x08
	try:
		returndata = bus.read_byte_data(rtc_addr, _REG_YEAR) # tray get actual set YEAR
	except:
		lcd('c')
		lcd('p,01,01, RTC test\n is FALSE')
		return
	# if no error then RTC is OK
	if check('RTC test OK\n press OK \n to next test', False)==False : return

	#test W1
	directory=glob.glob("/mnt/1wire/bus.1/*") #this directory contain directoy interface and adrectoy w1 devices
	print directory
	if len(directory)<2: # NO 1WIRE DEVICES
		lcd('c')
		lcd('p,01,01, W1 test is FALSE')
		exit(1)
	if check('W1 test OK\n press OK \n to next test', False)==False : return

	#test RS232
	if check('Connect RS232\n press OK \n to go test', False)==False : return
	if RS232_echo_test()==False :
		lcd('c')
		lcd('p,01,01, RS232 test\n is FALSE')
		return
	if check('RS232 test OK\n press OK \n to next test', False)==False : return

	#test RS485
	if check('Connect RS485\n press OK \n to go test', False)==False : return
	if RS485_echo_test()==False :
		lcd('c')
		lcd('p,01,01, RS485 test\n is FALSE')
		return
	if check('RS485 test OK\n press OK \n to next test', False)==False : return
		
	lcd('c')
	lcd('p,01,01,ALL TESTS IS OK\n----------------')
	

def wait_for_key():
	while 1==1:
		key=bus.read_byte(display_board)|0xC0
		if (key)!=0xFF : 
			beep()
			return key
		time.sleep(0.02)

def check(query,press=True):
	lcd('c')
	lcd('p,00,00,'+query)
	if press==True :
		time.sleep(0.02)
		lcd('p,00,01,press\nOK-if test ok\nESC-if false')
		time.sleep(0.02)
	if wait_for_key()!=ok:
		passwd='FALSE'
		return False
	return True


def get_bit_of_io_expander(addr_i2c, bit_number):
	bus = smbus.SMBus(1)
	mask = 1<<bit_number
	if bus.read_byte(addr_i2c)&mask==0 :
		return 0
	else :
		return 1


def beep():
	bus.write_byte(display_board, 0x80^0xFF) # use beeb service simulate beeb button oreginaly beeb is controled direc by raspberryPI GPIO
	time.sleep(0.25)
	bus.write_byte(display_board, 0xFF)

def RS232_echo_test():
	pin=11
	GPIO.setup(pin, GPIO.OUT)
	GPIO.output(pin, 0)

	ser = serial.Serial(
		port='/dev/ttyS0',
		#port='/dev/ttyUSB0',
		baudrate = 9600,
		parity=serial.PARITY_NONE,
		stopbits=serial.STOPBITS_ONE,
		bytesize=serial.EIGHTBITS,
		timeout=1
		)
	GPIO.output(pin, 0) #receiver RS485 = disable RS485 transmit
	ser.write("RS232 echo test\n")
	ser.flush()
	GPIO.output(pin, 1) #transmission RS485 = disable RS485 receivre
	x=ser.readline()
	GPIO.output(pin, 0) #receiver RS485 = disable RS485 transmit
	time.sleep(0.025)
	ser.write("RS232 echo test\n")
	ser.flush
	GPIO.output(pin, 1) #transmission RS485 = disable RS485 receivre
	x=ser.readline()
	GPIO.output(pin, 0) #receiver RS485 = disable RS485 transmit
	time.sleep(0.025)
	ser.write("end\n")
	ser.flush
	ser.close()
	if x=="RS232 echo test\n" :
		print "serial OK"
		return True
	else:
		print "serial FALSE"
		return False

def RS485_echo_test():
	pin=11
	GPIO.setup(pin, GPIO.OUT)
	GPIO.output(pin, 0)

	ser = serial.Serial(
		port='/dev/ttyS0',
		baudrate = 9600,
		parity=serial.PARITY_NONE,
		stopbits=serial.STOPBITS_ONE,
		bytesize=serial.EIGHTBITS,
		timeout=1
		)
	x=ser.readline()
	GPIO.output(pin, 1) #transmission
	ser.write("RS485 echo test\n")
	ser.flush()
	GPIO.output(pin, 0) #receiver
	x=ser.readline()
	time.sleep(0.025) # wait for other side until will be prepared to receive
	GPIO.output(pin, 1) #transmission
	ser.write("end\n")
	ser.close()
	if x=="RS485 echo test\n" :
		print "RS485 OK"
		return True
	else:
		print "RS485 FALSE"
		return False
	
	
	print x
		
		#ser.write("end\n")
	ser.close()
	if x=="RS485 echo test\n" :
		print "RS485 OK"
		return True
	else:
		print "RS485 FALSE"
		return False

	

# example of data:
#   "c" - clear lcd (no naswer)
#   "g" - return (answer) actual framebuffer (ignore inversion and image, only text).
#   "p,01,03,show text" - print "show text" on posicion x=1, y=3 (from top-left) (no answer)
#   "i,01,03,show text" - print inversion text "show text" on posicion x=1, y=3 (from top-left) (no answer)
#   "m,imagefile" - print image (must be black and white .tiff size 128x64 pixels stred at directory: (no answer)
def lcd(data):
	# use tcp socken on localhost with port 10000 to send display service
	# display service use before more proces can by use display on some time
	if data=="": return
	print data
	client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
	client_socket.connect(('localhost', 10000))
	client_socket.send(data)
	if data=="g":
		data = client_socket.recv(100)
		print data
	client_socket.close()

main()
wait_for_key()

