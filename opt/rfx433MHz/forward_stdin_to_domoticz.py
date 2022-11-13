#!/usr/bin/python

# forward code from stdin to PHP who process this code 
# and send signal to appropriate domoticz device

#default code have binary string format
#use frist argumend "d" change to decimal format

import time
import sys
import urllib
from urllib.request import urlopen


typeCode='b'
if len(sys.argv)==2:
    if sys.argv[1]=='d': typeCode='d'


while 1==1:
    code=sys.stdin.readline()
    if code=='' : continue
    code=code[:-1] #delete enter from end string
    if code=='Unknown encoding' : continue
    if len(code) > len('Received '):
        if code[:len('Received ')]=='Received ':
            code=code[len('Received '):]
    print(code)
    if typeCode=='d':
        binary_code="{0:b}".format(int(code))
        binary_code=(24-len(binary_code))*'0'+binary_code # add head zero
    else:
        binary_code=code
    url_code=urllib.parse.quote(binary_code)
    url="http://localhost/rfx433MHz/homepage/serve-rfx?code="+url_code
    print (url)
    urlopen(url).read()
