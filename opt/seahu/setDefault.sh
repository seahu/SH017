#!/bin/bash

# script set defult seting for sehu component = reset to default setting

# set default Network interfce (use dhcp)
/opt/seahu/setNetSeting.sh -I 1

#set default Wifi interface
/opt/seahu/setWifiSeting.sh -W 1 -A AP -I 0 -K GB -S seahu -P 12345678 -C 7 -i 10.17.200.1 -n 255.255.255.0 -a 10.17.200.10 -b 10.17.200.254

# set default services
/opt/seahu/services/service_domoticz disable

# run lcd menu after start
echo -e "1" > /etc/seahu/lcd_menu.cfg

#run owfs after start
echo -e "1" > /etc/seahu/owfs.cfg

# set default passwd
echo "pi:raspberry" | chpasswd


