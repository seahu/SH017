#!/bin/bash

# script set defult seting for sehu component = reset to default setting

# set default Network interfce
echo -e "iface eth0 inet dhcp\n" > /etc/network/interfaces.d/eth0

#set default Wifi interface

echo -e "iface wlan0 inet dhcp\n" > /etc/network/interfaces.d/wlan0
echo -e "	wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf\n" >> /etc/network/interfaces.d/wlan0

echo -e "country=GB\n" > /etc/wpa_supplicant/wpa_supplicant.conf
echo -e "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n" >> /etc/wpa_supplicant/wpa_supplicant.conf
echo -e "update_config=1\n" >> /etc/wpa_supplicant/wpa_supplicant.conf
echo -e "network={\n" >> /etc/wpa_supplicant/wpa_supplicant.conf
echo -e "}\n" >> /etc/wpa_supplicant/wpa_supplicant.conf

# set default services
/opt/seahu/services/service_domoticz disable
/opt/seahu/services/service_rex disable

# run lcd menu after start
echo -e "1" > /etc/seahu/lcd_menu.cfg

#run owfs after start
echo -e "1" > /etc/seahu/owfs.cfg

# set default passwd
echo "pi:raspberry" | chpasswd


