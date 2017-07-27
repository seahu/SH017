#!/bin/bash

echo $(/sbin/ifconfig eth0 | grep 'HWaddr ' | awk '{ print $5}')
echo $(/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')
echo $(/sbin/ifconfig eth0 | grep 'Mask:' | cut -d: -f4 | awk '{ print $1}')
echo $(/sbin/ip route | grep 'eth0' | awk '/default/ { print $3 }')
echo $(cat /etc/resolv.conf |grep -i nameserver|head -n1|cut -d ' ' -f2)
if grep "dhcp" /etc/network/interfaces.d/eth0 > /dev/null ; then
    echo "dhcp"
else
    echo "static"
fi



