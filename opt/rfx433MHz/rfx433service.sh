#!/bin/bash
# run RFSniffer and forward codes to PHP program who procces this code
# and send signal to appropriate domoticz device

# for this may by used my python snifer (get_433Mhz.py) or wide used RFSniffer

# FORWARDING SCHEME
#
#         --------------
#         | 433Sniffer |
#         --------------
#              | pipe
#             \/
#         -------------------------------
#         | forward_stdin_to_domoticz.py|
#         -------------------------------
#              | send binary code to PHP web (http://localhost/rfx433Mhz/homepage/serve-rfx?code=111111111101010101011111)
#             \/
#         -------------------------------
#         | My PHP program who register |
#         | devices and his codes       |
#         | and register in domoticz.   |
#         | Conjuction between code and |
#         | domoticz device.            |
#         -------------------------------
#              | send status to specifick domoticz device via domoticz web api
#             \/
#         ------------
#        | Domoticz  |
#        -------------
#

BASEDIR=$(dirname $0)


#for get_433Mhz.py
#$BASEDIR/get_433Mhz.py | $BASEDIR/forward_stdin_to_domoticz.py &

#for RFSniffer
$BASEDIR/RFSniffer | $BASEDIR/forward_stdin_to_domoticz.py b &

$BASEDIR/refresh.py

exit 0
