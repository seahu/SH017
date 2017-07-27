#!/bin/bash

modem_conf=$(cat /etc/seahu/modem.cfg)
if [ "$modem_conf" = "1" ]; then
    enable="yes"
else
    enable="no"
fi
echo $enable

/sbin/ifconfig | grep 'ppp0' > /dev/nul
if [ $? -eq 0 ] ; then
    con="Connect"
    echo $con
else
    con="Disconnect"
    echo $con
fi
dev=$(cat /etc/ppp/peers/Provider | grep "/dev/tty")
echo ${dev##*/}
echo $(head /etc/ppp/peers/Provider -n 2 | tail -n 1)
dial=$(cat /etc/chatscripts/Provider | grep "ATD")
dial=${dial%\'}
dial=${dial#*ATD}
echo $dial
APN=$(cat /etc/chatscripts/Provider | grep ",\"IP\",")
APN=${APN%\"\'}
APN=${APN#*,\"IP\",\"}
echo $APN

if /sbin/ifconfig | grep 'ppp0' > /dev/nul ; then
    echo $(/sbin/ifconfig ppp0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')
    echo $(/sbin/ifconfig ppp0 | grep 'Mask:' | cut -d: -f4 | awk '{ print $1}')
    echo $(/sbin/ip route | grep 'ppp0' | awk '/default/ { print $3 }')
    echo $(cat /etc/resolv.conf |grep -i nameserver|head -n1|cut -d ' ' -f2)
else 
    echo ""
    echo ""
    echo ""
    echo ""
fi

if [ $con == "Disconnect" ] ; then
    /opt/seahu/getModemStreghtOfSignal.py $dev
else
    echo "For get signal strenght must be disconnect"
fi
