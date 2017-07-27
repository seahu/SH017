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
# data na RS485 jsou rozclenena do packetu ruznych delek, pri preposilani po tcp je nejdrive
# uveden byte s jejich delkou a pak nasleduje samotny paket
# 

import time
import sys
import socket

#------- set TCP server --------
def TCP_server():
	TCP_IP = '127.0.0.1'
	TCP_PORT = 5005

	s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
	s.bind((TCP_IP, TCP_PORT))
	s.listen(1)
	while True:
		print 'Waiting for a connection'
		conn, addr = s.accept()
		print 'Connection address:', addr
		while True:
			try:
				print "cekam na prichozi paket"
				data=conn.recv(1025)
				if len(data)==0 : 
					print "nic nedoslo"
					break
				b_data=bytearray(data)
				print len(b_data)
				print b_data 
				print "odesilam data"
				conn.sendall(b_data)
			except socket.error:
				print "spojeni predcasne ukoncene druhou stranou"
				break;
			finally:
				print "konce cyklu"
				#print "koncim"
				#conn.close()
				#time.sleep(1)
		print "end connection"
		conn.close()

TCP_server()
