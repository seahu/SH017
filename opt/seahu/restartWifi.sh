#!/bin/bash
/sbin/ifdown wlan0
#sleep 5
/sbin/ifup wlan0
/sbin/iwconfig wlan0 power off
/etc/init.d/dnsmasq restart
/etc/seahu/firewall.sh