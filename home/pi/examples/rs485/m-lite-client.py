#!/usr/bin/python

#pokusny program pro M-lite vzdalenou komunikaci pomoci TCP.
# M-lite je specialni odlehceny protokol mbus pro komunikci
# cidel na rs485 sbernici.
# tento program resi seriovou cast premosteni teto komunikca pomoci tcp.
# client cyklicky sleduje komunikaci na RS485 a pakety urcene jemu preda pres tcp serveru
# a zaroven nasloucha serveru a pokud server posle nejake data tak jsou predany na RS485
# pro dosazeni co nejrychlejsi edezvy je program rozhozen do tri vlaken
# jedno vlakno se stara o kmunikaci na RS485 dalsi o odesilani dat na tcp a tredi o prijem 
# tcp dat, komunikace mezi vlakny je vyresena pomoci dvou front (buferu) jeden pro prijem dat
# z RS485 a druhy pro odesilani dat na RS485
# data na RS485 jsou rozclenena do packetu ruznych delek, paket z RS485 predavam v nezmenene podobe
# do tcp paketu

import RPi.GPIO as GPIO
import time
import sys
import signal
import serial
import socket
# pro http
import urllib
import urllib2
# vlakna
from threading import Thread,RLock,Event
#configurace pinu
import imp
cfg=imp.load_source('config', '/etc/seahu/pin_config.py')

FALSE=-1
BUSY=-2
NO_ACTIVE=-3
BAT_FORMAT=-4
NOT_FOR_ME=-5

NO_ACTIVE_TIMEOUT=0.1
READ_TIMEOUT=0.01
BUSY_TIMEOUT=2
WAIT_TX=0.5

SKUPINA=25
SLAVE=18
MAX_PAKETS=20 #maximalni pocet paketu ve fronte (jak In tak OUT)

IN_PACKETS=[] # FIFO fronta ,IN z pohledu RS485 tj. co se s RS485 precte jde do  IN_PACKETS
OUT_PACKETS=[] # FIFI fronta , OUT z pohledu RS485 tj. co se na ni zapise je z OUT_PACKETS
lock_buf=RLock(False)
lock_end_conn=RLock(False)
in_event=Event() # se vyvolava kdyz z RS485 dojde novy paket ktery je potreba vyslat do site.
end_conn=False # end_connection muze byt nastaveno pri rozpojeni spojeni (v takovem pripade se zkousi spojeni opakovane navazat znovu) nebo pri pozadavku na ukonceni programu
kill=False
conn=None # globalni promenna pro ulozeni objektu socketu, abych ho byl chopen restratovat z jineho vlakna

#---- funkce franene zamkem ----
#---- funkce pro praci s frontami (musi byt chranena zamky)----
def push_to_buf(buf, data):
	lock_buf.acquire()
	if len(buf)>=20 : buf.pop(0)
	buf.append(data)
	lock_buf.release()
	in_event.set() # nastav udalost pro spusteni odesilani

def pop_from_buf(buf):
	lock_buf.acquire()
	if len(buf)==0 :
		lock_buf.release()
		return False
	ret=buf.pop()
	lock_buf.release()
	return ret

# nastveni promene "run_conn" informujici o ukonceni TCP spojeni
def set_end_conn(val):
	global end_conn
	lock_end_conn.acquire()
	end_conn=val
	lock_end_conn.release()

def get_end_conn():
	global end_conn
	lock_end_conn.acquire()
	val=end_conn
	lock_end_conn.release()
	return val

#----- funkce pro obsluhu signalu pro ukonceni programu
def on_kill():
	global kill
	global conn
	print "Kill program"
	conn.shutdown(socket.SHUT_WR) #ukonceni spojeni ( conn.close() nefunguje pri spustene fci conn.recv() )
	kill=True
	set_end_conn(True)
	end_conn_event.set()
	in_event.set()	
	time.sleep(1)

def on_exit(signal, frame):
	on_kill()
	sys.exit(0)
	
#---- ini RS485 with control RX/TX pin
pin_RTS=cfg.RS485_RE_DE	#11
GPIO.setmode(GPIO.BOARD)
#GPIO.setmode(GPIO.BCM)
GPIO.setup(pin_RTS, GPIO.OUT)
GPIO.output(pin_RTS, 0) #0=receiver 1=transmission

#--- set SERIAL ---
port = "/dev/ttyAMA0"
baud = 9600
usart = serial.Serial(
    port='/dev/ttyAMA0',
    baudrate = baud,
    parity=serial.PARITY_NONE,
    stopbits=serial.STOPBITS_ONE,
    bytesize=serial.EIGHTBITS,
    timeout=1
    )

#--- RS485 function

def RS485_RX(start_timeout=NO_ACTIVE_TIMEOUT):
	global IN_PACKETS
	push_to_buf(IN_PACKETS, bytearray("ahoj"))
	time.sleep(1)
	return True
	t=time.time()
	usart.timeout=start_timeout
	data=usart.read(1)
	if len(data)==0 : return NO_ACTIVE
	usart.timeout=READ_TIMEOUT
	while True:
		byte=usart.read(1)
		if len(byte)==0 : break
		else : data = data + byte
		if (time.time()-t)> BUSY_TIMEOUT: return BUSY
	b_data=bytearray(data)
	if len(b_data)<4 : return BAT_FORMAT
	if b_data[0]!=SKUPINA : return NOT_FOR_ME
	if b_data[1]!=SLAVE : return NOT_FOR_ME
	push_to_buf(IN_PACKETS, b_data)
	return True


def RS485_WAIT_TO_CAN_TX():
	ret=RS485_RX(WAIT_TX)
	if ret==BUSY: return BUSY
	if ret==NO_ACTIVE : return True
	else : time.sleep(WAIT_TX-READ_TIMEOUT)
	return True

def RS485_TX(data):
	RS485_WAIT_TO_CAN_TX()
	GPIO.output(pin_RTS, 1) # prepni na vysilani
	usart.flush()
	usart.write(data)
	#while 1:
	#	if usart.outWaiting()==0:break #tento python to jeste neumi
	t=((len(data)*10)/baud)*1.1 #spocitani potrebne doby pro vyslani vsech dat na seriovou linku (*1.1 => je rezerva)
	time.sleep(t)
	GPIO.output(pin_RTS, 0) # prepni spet na prijem


#------ send packet to http ----
def packtet_to_http(data):
	url='http://127.0.0.1/m-lite.php'
	values={'packet': data}
	url_values=urllib.urlencode(values)
	full_url=url + '?' + url_values
	try:
		response = urllib2.urlopen(full_url)
		the_page = response.read()
		print the_page
	except URLError:
		return False
	if the_page="OK" : return True
	else: return False

#------------------ tridy vlaken -----------
class RS485_loop(Thread):
	def run(self):
		print "RS485 loop Thread"
		global OUT_PACKETS
		while True:
			if kill==True : break
			print "read RS485"
			RS485_RX()
			tx_data=pop_from_buf(OUT_PACKETS)
			if tx_data!=False :
				RS485_TX(tx_data)
		print "END - RS485 loop Thread"


class TCP_send_loop(Thread):
	def __init__(self, conn):
		Thread.__init__(self)
		self.conn=conn

	def run(self):
		global IN_PACKETS
		print "TCP send loop Thread"
		while True:
			in_event.wait()
			in_event.clear()
			if get_end_conn()==True : break
			rx_data=pop_from_buf(IN_PACKETS)
			if rx_data!=False :
				#packtet_to_http(rx_data) #preposilani na web
				try:
					print "posilam", rx_data
					self.conn.sendall(rx_data)
				except socket.error:
					set_end_conn(True)
					in_event.set()
					break
		print "END - TCP send loop Thread"

class TCP_recv_loop(Thread):
	def __init__(self,conn):
		Thread.__init__(self)
		self.conn=conn

	def run(self):
		global OUT_PACKETS
		print "TCP recv loop Thread"
		while True:
			if get_end_conn()==True : break
			data=self.conn.recv(1024)
			if len(data)==0 : #end connection
				set_end_conn(True)
				in_event.set()
				break
			print "prijimam:", data
			b_data=bytearray(data)
			push_to_buf(OUT_PACKETS,b_data)
		print "END - TCP recv loop Thread"


#------ set TCP client ------
def TCP_client():
	global conn
	TCP_IP = '127.0.0.1'
	TCP_PORT = 5005

	
	while True:
		if kill==True : break
		set_end_conn(False)
		try:
			print "create socket"
			s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
			print "Try connect"
			s.connect((TCP_IP, TCP_PORT))
		except socket.error:
			print "cannot connect"
			time.sleep(1)
			continue
		conn=s # uozit spojeni do globalni promenne, aby bylo mozne spojeni zrusit z obsluhy signalu ukonceni programu
		print "Create threaders"
		t_TCP_send_loop=TCP_send_loop(s)
		t_TCP_recv_loop=TCP_recv_loop(s)
		t_TCP_send_loop.start()
		t_TCP_recv_loop.start()
		while get_end_conn()==False: #cekej na ukonceni spojeni (zkosel jsem to resit pres event ale pri cekani na event to nereaguje na ctrl+c coz pri vyvoji je neprakticke, takze to resim obycejnou smysckou)
			time.sleep(1)
		print "END CONNECTION"
		time.sleep(1) #pockej nez skonci i vlakno ktre vyvjimku nevyvolalo
			
	print "End connection and end program"
	s.close()
	

#------ set TCP client pokus ------
def TCP_client_pokus1():
	TCP_IP = '127.0.0.1'
	TCP_PORT = 5005


	
	while True:
		s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		try:
			print "Try connect"
			s.connect((TCP_IP, TCP_PORT))
		except socket.error:
			print "Nepodarilo se navazat spojeni"
			time.sleep(1)
			continue
		while True:
			try:
				print "try send"
				data=bytearray()
				data.append(5)
				data.append('a')
				data.append('h')
				data.append('o')
				data.append('j')
				data.append('s')
				s.sendall(data)
				print "cekam odpoved"
				data=s.recv(1024)
				if len(data)==0:
					print "nic"
					break
				data=bytearray(data)
				print data
				#time.sleep(1)
			finally:
				#end_conn=True
				#in_event.set()
				#time.sleep(1) #pockej nez skonci i vlakno ktre vyvjimku nevyvolalo
				#s.close()
				print "End cycle"
				time.sleep(1)
		s.close()

#signal.signal(signal.SIGTERM, on_exit)
try:
	t_RS485_loop=RS485_loop()
	t_RS485_loop.start()
	TCP_client()
	#TCP_client_pokus1()
	while True:
		print "cekam na konec"
		time.sleep(1)
except KeyboardInterrupt:
	on_kill()
	print "kill end"
