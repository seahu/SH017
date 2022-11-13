#!/bin/bash

# Return netmask for a given network and CIDR.
cidr_to_netmask() {
    if [ "$1" == "" ]; then
	 return 0
     fi
    value=$(( 0xffffffff ^ ((1 << (32 - $1)) - 1) ))
    echo "$(( (value >> 24) & 0xff )).$(( (value >> 16) & 0xff )).$(( (value >> 8) & 0xff )).$(( value & 0xff ))"
}

# mac
mac=$(/sbin/ifconfig eth0 | grep 'ether ' | awk '{ print $2}')
# actual ip
actual_ip=$(/sbin/ifconfig eth0 | grep 'inet '  | awk '{ print $2}')
# actual net mask
actual_netmask=$(/sbin/ifconfig eth0 | grep 'netmask ' | awk '{ print $4}')
# actual gateway
actual_gateway=$(/sbin/ip route | grep 'eth0' | awk '/default/ { print $3 }')
# actual dns
# reslov find last updatep dnsmasq if run or get dns server from resolf.conf if no run dnsmasq service
if ps -A | grep dnsmasq > /dev/null ; then
    actual_dns=$(journalctl -u dnsmasq | grep nameserver | tail -n 1 | cut -d ' ' -f8 | cut -d '#' -f1)
else
    actual_dns=$(cat /etc/resolv.conf | grep -i nameserver | head -n1 | cut -d ' ' -f2)
fi
# static ip
static_ip=$(cat /etc/dhcpcd.eth0.conf | grep "static ip_address=" | cut -d '=' -f2)
static_ip=$(echo "$static_ip" | cut -d '/' -f1)
# static net mask
static_netmask=$(cat /etc/dhcpcd.eth0.conf | grep "static ip_address=" | cut -d '/' -f2) # select cidr from line with IP/cidr
static_netmask=$(cidr_to_netmask $static_netmask) # convert cidr format to netmask format etc. 16 to 255.255.0.0
# static gateway
static_gateway=$(cat /etc/dhcpcd.eth0.conf | grep "static routers=" | cut -d '=' -f2)
# static dns
static_dns=$(cat /etc/dhcpcd.eth0.conf | grep "static domain_name_servers=" | cut -d '=' -f2)
# dhcp 
if grep "^interface eth0" /etc/dhcpcd.conf > /dev/null ; then
    dhcp="0" # static
else
    dhcp="1" # dhcp
fi

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
