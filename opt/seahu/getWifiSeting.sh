#!/bin/bash

echo $(/sbin/ifconfig wlan0 | grep 'HWaddr ' | awk '{ print $5}')
echo $(/sbin/ifconfig wlan0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')
echo $(/sbin/ifconfig wlan0 | grep 'Mask:' | cut -d: -f4 | awk '{ print $1}')
echo $(/sbin/ip route | grep 'wlan0' | awk '/default/ { print $3 }')
echo $(cat /etc/resolv.conf |grep -i nameserver|head -n1|cut -d ' ' -f2)
if grep "dhcp" /etc/network/interfaces.d/wlan0 > /dev/null ; then
    echo "dhcp"
else
    echo "static"
fi

# type of Wi-Fi maneget (client) or AP (acces point)
type=$(/sbin/iw wlan0 info | grep type |  awk '{ print $2 }')
if [ $type = "AP" ] ; then
    sid=$(cat /etc/hostapd/hostapd.conf |grep -i ^ssid=|cut -d '=' -f2)
    echo $sid
    psk=$(cat /etc/hostapd/hostapd.conf |grep -i wpa_passphrase=|cut -d '=' -f2)
    echo $psk
else
    sid=$(cat /etc/wpa_supplicant/wpa_supplicant.conf |grep -i ssid=|cut -d '=' -f2)
    sid=${sid%%\"}
    sid=${sid##\"}
    echo $sid
    psk=$(cat /etc/wpa_supplicant/wpa_supplicant.conf |grep -i psk=|cut -d '=' -f2)
    psk=${psk%%\"}
    psk=${psk##\"}
    echo $psk
    type="CLIENT"
fi

# echo type of Wi-Fi maneget (client) or AP (acces point)
echo $type

#channel nuber usefull only on AP type
echo $(cat /etc/hostapd/hostapd.conf | grep channel= | cut -d'=' -f2)

# enable/disable forwarding (0-disable, 1-enable)
echo $(cat /proc/sys/net/ipv4/ip_forward)

# enable/disbale wlan0 (Wi-Fi)
echo $(cat /etc/network/interfaces.d/wlan0 | grep "#wlan0_is_")

# enable/disable wifi DHCPd  (0-disable, 1-enable)
if grep no-dhcp-interface=wlan0 /etc/dnsmasq.d/dnsmasq-wlan0.conf > /dev/null ; then
    echo 0
else
    echo 1
fi

# frist IP of dhcpd range
echo $(grep dhcp-range= /etc/dnsmasq.d/dnsmasq-wlan0.conf |  cut -d'=' -f2 |  cut -d',' -f1)

# secound IP of dhcpd range
echo $(grep dhcp-range= /etc/dnsmasq.d/dnsmasq-wlan0.conf |  cut -d'=' -f2 |  cut -d',' -f2)

# enable/disable NAT (0-disable, 1-enable)
if /sbin/iptables -t nat -L -n | grep MASQUERADE > /dev/null ; then
    echo 1
else
    echo 0
fi

# get Wi-Fi state (UP/DOWN)
if ip link show wlan0 | grep "state UP" > /dev/null ; then
    echo "UP"
else
    if ip link show wlan0 | grep "state DORMANT" > /dev/null ; then
	echo "UP"
    else
	echo "DOWN"
    fi
fi
