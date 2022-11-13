#!/bin/bash

# tun_ip
tun_ip1=$(/sbin/ifconfig tun0 | grep 'inet ' | awk '{ print $2}')
tun_ip2=$(/sbin/ifconfig tun0 | grep 'inet ' | awk '{ print $6}')
tun_netmask=$(/sbin/ifconfig tun0 | grep 'netmask ' | awk '{ print $4}')

echo "tun_ip1:$tun_ip1"
echo "tun_ip2:$tun_ip2"
echo "tun_netmask:$tun_netmask"
