#!/usr/bin/python

# TCP server for print into display from more programs
# service care about framefuffer,so you can get content of dispalya
# service can display text or image
# actualy framebuffer store only text (ignore image and inversion text)
# protocol is very easy. Client send data on one packed with format explain bellow and close connection.
# Only when send "g" client wait to answer where get conntent of framebuffer and close connection.
# example of data:
#   "c" - clear lcd (no naswer)
#   "g" - return (answer) actual framebuffer (ignore inversion and image, only text).
#   "p,01,03,show text" - print "show text" on posicion x=1, y=3 (from top-left) (no answer)
#   "i,01,03,show text" - print inversion text "show text" on posicion x=1, y=3 (from top-left) (no answer)
#   "m,imagefile" - print image (must be black and white .tiff size 128x64 pixels stred at directory: (no answer)

#PS: server is very easy. Serve only one connection at some time, therefore client must send requirement and.
# as quickly as possible connection close for release connection forother.
# Printed into display consume some time and if client try open connection beafore other ended then may bigger delay.



import os
import sys
import socket

def help():
    print """
# TCP server for print into display from more programs
# service care about framefuffer,so you can get content of dispalya
# service can display text or image
# actualy framebuffer store only text (ignore image and inversion text)
# protocol is very easy. Client send data on one packed with format explain bellow and close connection.
# Only when send "g" client wait to answer where get conntent of framebuffer and close connection.
# example of data:
#   "c" - clear lcd (no naswer)
#   "g" - return (answer) actual framebuffer (ignore inversion and image, only text).
#   "p,01,03,show text" - print "show text" on posicion x=1, y=3 (from top-left) (no answer)
#   "i,01,03,show text" - print inversion text "show text" on posicion x=1, y=3 (from top-left) (no answer)
#   "m,imagefile" - print image (must be black and white .tiff size 128x64 pixels stred at directory: (no answer)

#PS: server is very easy. Serve only one connection at some time, therefore client must send requirement and.
# as quickly as possible connection close for release connection forother.
# Printed into display consume some time and if client try open connection beafore other ended then may bigger delay.
    """

def client(data):
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

if __name__ == "__main__":
    if len(sys.argv)<1 : exit()
    if sys.argv[1]=="-h": help()
    client(sys.argv[1])
