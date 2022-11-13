#!/usr/bin/python

# refres saved state of rfx433MHz devices who care my PHP program.
# PHP program is interface between 433utils and home automation system
# Domoticz

import time
import sys
from urllib.request import urlopen

while 1==1:
    time.sleep(15)
    url="http://localhost/rfx433MHz/homepage/refresh-rfx"
    #print (url)
    urlopen(url).read()
