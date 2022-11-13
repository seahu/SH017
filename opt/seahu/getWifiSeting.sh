#!/bin/bash

# print actual Wi-Fi setting (each paramere in owwn line)
# enable/disable wlan0
# AP/CLIENT (Wi-Fi mode)
# mac
# actual_IP
# actual_netmask
# actual_gateway
# actual_DNS server
# static_IP
# static_netmask
# static_DNS
# dhcp 0-static 1-dhcp
# AP country (CZ.GB,UK,US,...)
# AP SID
# AP PSK (Wi-Fi password)
# AP chanel
# CLIENT  country (CZ.GB,UK,US,...)
# CLIENT SID
# CLIENT PSK
# CLIENT connect 1=connect 0=no connect
# CLIENT signal
# forwarding 0-disable 1-enable
# enable/disable DHPCd
# first IP of dhcpd range
# secound IP of dhcpd range
# enable/disable NAT (0-disable, 1-enable)

# Return netmask for a given network and CIDR.
cidr_to_netmask() {
    value=$(( 0xffffffff ^ ((1 << (32 - $1)) - 1) ))
    echo "$(( (value >> 24) & 0xff )).$(( (value >> 16) & 0xff )).$(( (value >> 8) & 0xff )).$(( value & 0xff ))"
}


# enable/disbale wlan0 (Wi-Fi)
if ip link show wlan0 | grep "state UP" > /dev/null ; then
    enable_wlan=1
else
    enable_wlan=0
fi

# mac
mac=$(/sbin/ifconfig wlan0 | grep 'ether ' | awk '{ print $2}')

# actual ip
actual_ip=$(/sbin/ifconfig wlan0 | grep 'inet '  | awk '{ print $2}')

# actual net mask
actual_netmask=$(/sbin/ifconfig wlan0 | grep 'netmask ' | awk '{ print $4}')

# actual gateway
actual_gateway=$(/sbin/ip route | grep 'wlan0' | awk '/default/ { print $3 }')

# actual dns
# reslov find last updatep dnsmasq if run or get dns server from resolf.conf if no run dnsmasq service
if ps -A | grep dnsmasq > /dev/null ; then
    actual_dns=$(journalctl -u dnsmasq | grep nameserver | tail -n 1 | cut -d ' ' -f8 | cut -d '#' -f1)
else
    actual_dns=$(cat /etc/resolv.conf | grep -i nameserver | head -n1 | cut -d ' ' -f2)
fi

# static ip
static_ip=$(cat /etc/dhcpcd.wlan0.conf | grep "static ip_address=" | cut -d '=' -f2)
static_ip=$(echo "$static_ip" | cut -d '/' -f1)

# static net mask
static_netmask=$(cat /etc/dhcpcd.wlan0.conf | grep "static ip_address=" | cut -d '/' -f2) # select cidr from line with IP/cidr
static_netmask=$(cidr_to_netmask $static_netmask) # convert cidr format to netmask format etc. 16 to 255.255.0.0

# static gateway
static_gateway=$(cat /etc/dhcpcd.wlan0.conf | grep "static routers=" | cut -d '=' -f2)

# static dns
static_dns=$(cat /etc/dhcpcd.wlan0.conf | grep "static domain_name_servers=" | cut -d '=' -f2)

# dhcp 
if grep "^interface wlan0" /etc/dhcpcd.conf > /dev/null ; then
    dhcp="0" # static
else
    dhcp="1" # dhcp
fi

# wifi_mode
# type of Wi-Fi managmet (client) or AP (acces point) : $sid, $psk, $wifi_mode
wifi_mode=$(/sbin/iw wlan0 info | grep type |  awk '{ print $2 }')
if [ $wifi_mode != "AP" ] ; then
    wifi_mode="CLIENT"
fi

# country for ap
ap_country=$(cat /etc/hostapd/hostapd.conf |grep -i ^country_code= | cut -d '=' -f2)

# sid for AP mode
ap_sid=$(cat /etc/hostapd/hostapd.conf | grep -i ^ssid=|cut -d '=' -f2)

# psk for AP mode (password)
ap_psk=$(cat /etc/hostapd/hostapd.conf | grep -i wpa_passphrase=|cut -d '=' -f2)

#channel nuber usefull only on AP type
ap_channel=$(cat /etc/hostapd/hostapd.conf | grep channel= | cut -d'=' -f2)

# country for CLIENT  mode
client_country=$(cat /etc/wpa_supplicant/wpa_supplicant.conf | grep -i country=|cut -d '=' -f2)

# sid for CLIENT mode
client_sid=$(cat /etc/wpa_supplicant/wpa_supplicant.conf | grep -i ssid=|cut -d '=' -f2)
client_sid=${client_sid%%\"}
client_sid=${client_sid##\"}


# Wi-Fi password for CLIENT  mode
client_psk=$(cat /etc/wpa_supplicant/wpa_supplicant.conf | grep -i psk=|cut -d '=' -f2)
client_psk=${client_psk%%\"}
client_psk=${client_psk##\"}

# Wi-Fi client connect status 1=connect 0= no connect
if iw wlan0 link | grep "Not connected." > /dev/null ; then
    client_connect=0 # not connected
else
    client_connect=1 # connected
fi

# Client Wi-Fi signal
client_signal=$(iw wlan0 link | grep "signal:" | cut -d ':' -f2)

# enable/disable forwarding (0-disable, 1-enable)
forward=$(cat /proc/sys/net/ipv4/ip_forward)

# enable/disable wifi DHCPd  (0-disable, 1-enable)
if grep no-dhcp-interface=wlan0 /etc/dnsmasq.d/dnsmasq-wlan0.conf > /dev/null ; then
    enable_dhcpd=0
else
    enable_dhcpd=1
fi

# frist IP of dhcpd range
dhcpd_pool_ip1=$(grep dhcp-range= /etc/dnsmasq.d/dnsmasq-wlan0.conf |  cut -d'=' -f2 |  cut -d',' -f1)

# secound IP of dhcpd range
dhcpd_pool_ip2=$(grep dhcp-range= /etc/dnsmasq.d/dnsmasq-wlan0.conf |  cut -d'=' -f2 |  cut -d',' -f2)

# enable/disable NAT (0-disable, 1-enable)
if /sbin/iptables -t nat -L -n | grep MASQUERADE > /dev/null ; then
    nat=1
else
    nat=0
fi

# ---- echo ------
echo "enable_wlan:$enable_wlan"
echo "wifi_mode:$wifi_mode"
echo "mac:$mac"
echo "actual_ip:$actual_ip"
echo "actual_netmask:$actual_netmask"
echo "actual_gateway:$actual_gateway"
echo "actual_dns:$actual_dns"
echo "static_ip:$static_ip"
echo "static_netmask:$static_netmask"
echo "static_gateway:$static_gateway"
echo "static_dns:$static_dns"
echo "dhcp:$dhcp"
echo "ap_country:$ap_country"
echo "ap_sid:$ap_sid"
echo "ap_psk:$ap_psk"
echo "ap_channel:$ap_channel"
echo "client_country:$client_country"
echo "client_sid:$client_sid"
echo "client_psk:$client_psk"
echo "client_connect:$client_connect"
echo "client_signal:$client_signal"
echo "forward:$forward"
echo "enable_dhcpd:$enable_dhcpd"
echo "dhcpd_pool_ip1:$dhcpd_pool_ip1"
echo "dhcpd_pool_ip2:$dhcpd_pool_ip2"
echo "nat:$nat"