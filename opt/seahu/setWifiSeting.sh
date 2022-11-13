#!/bin/bash

help () {
	echo "HELP:"
	echo "-----"
	echo "-m mac (chnage do any effect)"
	echo "-i static ip"
	echo "-n static netmask"
	echo "-g static getway"
	echo "-d static dns"
	echo "-I dhcp (1=dhcp 0=static)"
	echo "-K AP country (CZ,GB,US,...)"
	echo "-S AP sid"
	echo "-P AP psk"
	echo "-k CLIENT country (CZ,GB,US,...)"
	echo "-s CLIENT sid"
	echo "-p CLIENT psk"
	echo "-A wifi_mode (AP/CLIENT)"
	echo "-c channel (used in AP mode)"
	echo "-F forward (0/1) (used in AP mode)"
	echo "-W enable_wlan (0/1)"
	echo "-D enable_dhcpd (0/1)"
	echo "-a first_poll_IP (used if enable dhcpd in AP mode)"
	echo "-b secound_poll_IP (used id enable dhcpd in AP mode)"
	echo "-N nat (0/1) (used in AP mode)"

	echo "-h help"
}

netmask2cidr () {
	local i= len=
	local IFS=.
	for i in $1; do
	while [ ${i} != "0" ]; do
		len=$((${len} + ${i} % 2))
		i=$((${i} >> 1))
	done
	done
	echo "${len}"
}

# first get actual values
#------------------------
wifi_seting=$(/opt/seahu/getWifiSeting.sh)
echo "-- Print actual values: --"
for var_name in enable_wlan wifi_mode mac actual_ip actual_netmask actual_gateway actual_dns static_ip static_netmask static_gateway static_dns dhcp ap_country ap_sid ap_psk ap_channel client_country client_sid client_psk client_connect client_signal forward enable_dhcpd dhcpd_pool_ip1 dhcpd_pool_ip2 nat
    do
	line=$(echo "$wifi_seting" | grep ^$var_name:)
	#echo "line:"$line
	eval "$var_name"=${line#*:} # separe all after :      etc. mac:dc:a6:32:e3:5c:c6 get dc:a6:32:e3:5c:c6
	echo $var_name ${!var_name}
    done
#mac=$(echo "$wifi_seting" | sed -n '1p')
#static_ip=$(echo "$wifi_seting" | sed -n '2p')
#static_netmask=$(echo "$wifi_seting" | sed -n '3p')
#static_gateway=$(echo "$wifi_seting" | sed -n '4p')
#static_dns=$(echo "$wifi_seting" | sed -n '5p')
#dhcp=$(echo "$wifi_seting" | sed -n '6p')
#sid=$(echo "$wifi_seting" | sed -n '7p')
#psk=$(echo "$wifi_seting" | sed -n '8p')
#wifi_mode=$(echo "$wifi_seting" | sed -n '9p')
#chanel=$(echo "$wifi_seting" | sed -n '10p')
#forward=$(echo "$wifi_seting" | sed -n '11p')
#enable_wlan=$(echo "$wifi_seting" | sed -n '12p')
#enable_dhcpd=$(echo "$wifi_seting" | sed -n '13p')
#dhcpd_pool_ip1=$(echo "$wifi_seting" | sed -n '14p')
#dhcpd_pool_ip2=$(echo "$wifi_seting" | sed -n '15p')
#nat=$(echo "$wifi_seting" | sed -n '16p')
#country="CZ"

#replace actual values by new values by command arguments
#--------------------------------------------------------
while getopts W:m:i:n:g:d:I:K:S:P:k:s:p:A:c:F:D:a:b:N:C:h flag
do
    case "${flag}" in
	W) enable_wlan=${OPTARG};;
	m) mac=${OPTARG};;
	i) static_ip=${OPTARG};;
	n) static_netmask=${OPTARG};;
	g) static_gateway=${OPTARG};;
	d) static_dns=${OPTARG};;
	I) dhcp=${OPTARG};;
	K) ap_country=${OPTARG};;
	S) ap_sid=${OPTARG};;
	P) ap_psk=${OPTARG};;
	k) client_country=${OPTARG};;
	s) client_sid=${OPTARG};;
	p) client_psk=${OPTARG};;
	A) wifi_mode=${OPTARG};;
	c) ap_channel=${OPTARG};;
	F) forward=${OPTARG};;
	D) enable_dhcpd=${OPTARG};;
	a) dhcpd_pool_ip1=${OPTARG};;
	b) dhcpd_pool_ip2=${OPTARG};;
	N) nat=${OPTARG};;
	h) help;;
	\?) echo "Invalid option: $flag" 1>&2
	    help
	    ;;
	:) help;;

    esac
done
cidr=$(netmask2cidr "$static_netmask") # conver netmask do number etc. 255.255.255.0 to 24


# print new values
#------------------
echo "-- Print new values: --"
echo "enable_wlan: $enable_wlan"
echo "wifi_mode: $wifi_mode"
echo "mac: $mac"
echo "static ip: $static_ip"
echo "static netmask: $static_netmask"
echo "cidr: $cidr"
echo "static gateway: $static_gateway"
echo "static dns: $static_dns"
echo "dhcp: $dhcp"
echo "ap_country: $ap_country"
echo "ap_sid: $ap_sid"
echo "ap_psk: $ap_psk"
echo "ap_channel: $ap_channel"
echo "ap_country: $client_country"
echo "ap_sid: $client_sid"
echo "ap_psk: $client_psk"
echo "forward: $forward"
echo "enable_dhcpd: $enable_dhcpd"
echo "dhcpd_pool_ip1: $dhcpd_pool_ip1"
echo "dhcpd_pool_ip: $dhcpd_pool_ip2"
echo "nat: $nat"

# DO IT (not completion yet)
#---------------------------
#/etc/dhcpc.conf # if need set static IP address or delete wlan0 entry
echo "" > /etc/dhcpcd.wlan0.conf
echo "#--- Seahu Wi-Fi config ---" >> /etc/dhcpcd.wlan0.conf
if [ "$dhcp" = "1" ] ; then	# dhcp
    echo "" > /etc/dhcpcd.wlan0.conf
    echo="# interface wlan0 use auto dhcp cofigure" >> /etc/dhcpcd.wlan0.conf
    echo "# interface wlan0" >> /etc/dhcpcd.wlan0.conf
    echo "# static ip_address=$static_ip/$cidr" >> /etc/dhcpcd.wlan0.conf
    echo "# static routers=$static_gateway" >> /etc/dhcpcd.wlan0.conf
    echo "# static domain_name_servers=$static_dns" >> /etc/dhcpcd.wlan0.conf
    echo "" >> /etc/dhcpcd.wlan0.conf
else 				# static
    echo "" > /etc/dhcpcd.wlan0.conf
    echo "interface wlan0" >> /etc/dhcpcd.wlan0.conf
    echo "static ip_address=$static_ip/$cidr" >> /etc/dhcpcd.wlan0.conf
    
    if [ $wifi_mode == "CLIENT" ]; then
	echo "static routers=$static_gateway" >> /etc/dhcpcd.wlan0.conf
	echo "static domain_name_servers=$static_dns" >> /etc/dhcpcd.wlan0.conf
    else 
	echo "nohook wpa_supplicant" >> /etc/dhcpcd.wlan0.conf
	echo "#static routers=$static_gateway" >> /etc/dhcpcd.wlan0.conf # comment this because AP mode use default gateway from eth0
	echo "#static domain_name_servers=$static_dns" >> /etc/dhcpcd.wlan0.conf # comment this because AP mode use defalt dns from eth0 or localhost from dnsmsq
    fi
    echo "" >> /etc/dhcpcd.wlan0.conf
fi

cat /etc/dhcpcd.conf.orig > /etc/dhcpcd.conf
cat /etc/dhcpcd.eth0.conf >> /etc/dhcpcd.conf
cat /etc/dhcpcd.wlan0.conf >> /etc/dhcpcd.conf



#CLIENT MODE
if [ $wifi_mode == "CLIENT" ]; then
    # off hostapd
    systemctl stop hostapd
    systemctl disable hostapd
    systemctl mask hostpad

    # off dnsmasq
    systemctl stop dnsmasq
    systemctl disable dnsmasq
    systemctl mask dnsmasq

    # set /etc/wpa_supplicant/wpa_supplicant.conf
    file="/etc/wpa_supplicant/wpa_supplicant.conf"
    echo "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev" > "$file"
    echo "update_config=1" >> "$file"
    echo "country=$client_country" >> "$file"
    echo "" >> "$file"
    echo "network={" >> "$file"
    echo "	ssid=\"$client_sid\"" >> "$file"
    if [ "$client_psk" == "" ]; then
        echo "	key_mgmt=NONE" >> "$file"
    else
        echo "	psk=\"$client_psk\"" >> "$file"
    fi
    echo "}" >> "$file"

    # disable forward

    # unset NAT
    /sbin/iptables --flush
    /sbin/iptables --delete-chain
    /sbin/iptables --table nat --flush
    /sbin/iptables --table nat --delete-chain
    netfilter-persistent save

    # restart wifi
    rfkill block wifi
    systemctl enable wpa_supplicant.service
    systemctl daemon-reload
    rfkill unblock wifi
    ip addr flush dev wlan0
    systemctl restart dhcpcd
    systemctl restart wpa_supplicant.service
    #/opt/seahu/restartWifi.sh
fi

#AP MODE
if [ $wifi_mode == "AP" ]; then
    # off wpa_supplicant
    systemctl stop wpa_supplicant.service
    systemctl disable wpa_supplicant.service
    systemctl mask wpa_supplicant.service

    # configure hostapd
    systemctl stop hostapd
    file=/etc/hostapd/hostapd.conf
    echo "#---- Configured by Seahu GUI ---" > "$file"
    echo "country_code=$ap_country" >> "$file"
    echo "interface=wlan0" >> "$file"
    echo "driver=nl80211" >> "$file"
    echo "ssid=$ap_sid" >> "$file"
    echo "hw_mode=g"  >> "$file"
    echo "channel=$ap_channel"  >> "$file"
    echo "macaddr_acl=0"  >> "$file"
    echo "ignore_broadcast_ssid=0"  >> "$file"
    echo "auth_algs=1" >> "$file"
    echo "wpa=3"  >> "$file"
    echo "wpa_passphrase=$ap_psk"  >> "$file"
    echo "wpa_key_mgmt=WPA-PSK"  >> "$file"
    echo "wpa_pairwise=TKIP"  >> "$file"
    echo "rsn_pairwise=CCMP"  >> "$file"

    # configure dnsmasq
    systemctl stop dnsmasq
    file=/etc/dnsmasq.d/dnsmasq-wlan0.conf
    echo "#---- Do not edit configured by Seahu GU - Wi-Fi Ap is enabled. ---" > "$file"
    echo "interface=wlan0" >> "$file"
    echo "dhcp-range=$dhcpd_pool_ip1,$dhcpd_pool_ip2,$static_netmask,24h" >> "$file"
    if [ "$enable_dhcpd" == "0" ]; then
	echo "no-dhcp-interface=wlan0" >> "$file"
    fi

    # enable forward
    /sbin/sysctl -w net.ipv4.ip_forward=1
    file="/etc/sysctl.d/routed-ap.conf"
    echo "# Enable IPv4 routing" > "$file"
    echo "net.ipv4.ip_forward=1" >> "$file"

    # enable firewall
    /sbin/iptables --flush
    /sbin/iptables --delete-chain
    /sbin/iptables --table nat --flush
    /sbin/iptables --table nat --delete-chain
    /sbin/iptables --table nat --append POSTROUTING ! --out-interface wlan0 -j MASQUERADE
    /sbin/iptables --append FORWARD --in-interface wlan0 -j ACCEPT
    netfilter-persistent save

    # restart wifi
    rfkill block wifi
    rfkill unblock wifi
    systemctl unmask dnsmasq
    systemctl unmask hostapd
    systemctl enable dnsmasq
    systemctl enable hostapd
    systemctl daemon-reload
    systemctl restart dhcpcd
    systemctl start hostapd
    systemctl start dnsmasq
    ip addr flush dev wlan0
    systemctl restart dhcpcd
    #/opt/seahu/restartWifi.sh
fi

# enable/disable wlan interface
if [ "$enable_wlan" == "1" ]; then
    ip link set wlan0 up
else
    # wpa_supplicant
    systemctl stop wpa_supplicant
    systemctl disable wpa_supplicant
    systemctl mask wpa_supplicant

    # dnsmasq
    systemctl stop dnsmasq
    systemctl disable dnsmasq
    systemctl mask dnsmasq

    # hostapd
    systemctl stop hostapd
    systemctl disable hostapd
    systemctl mask hostapd

    systemctl daemon-reload
    rfkill block wifi
    ip link set wlan0 down
fi

