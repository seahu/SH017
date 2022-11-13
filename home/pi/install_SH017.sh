#!/bin/bash

# go to PI home directory (place for downloaded files)
cd /home/pi

#installation scritp for seahu SH017 software on raspbeery PI computer
#instal all used programs and copy data

#---- Wi-Fi ---------------

# instal support for Wi-Fi Acces Point 
sudo apt install hostapd
# by defaut deny Wi-Fi Access Point
sudo systemctl stop hostapd
sudo systemctl disable hostapd
sudo systemctl unmask hostapd

# install support for routing services (DNS, DHCPD)
sudo apt install dnsmasq
sudo systemctl stop dnsmasq
sudo systemctl disable dnsmasq
sudo systemctl unmask dnsmasq

# for control firewall by netfilter-presistent
sudo DEBIAN_FRONTEND=noninteractive apt install -y netfilter-persistent iptables-persistent

#------ Web config ----------

# install http server
sudo apt-get install apache2
sudo a2enmod rewrite

# install php
sudo apt-get install php
sudo apt-get install composer
sudo apt-get install php-yaml

#for check pi password (mainly in php)
sudo apt-get install whois

#------- Open VPN ------
sudo apt-get install openvpn

#------- web console shellinabox ----
sudo apt-get install shellinabox

#------- ser2ner --------
sudo apt-get install ser2net

# change riht to configure file to enable web configure
sudo chmod o=rw,g=rw,o=r /etc/ser2net.conf
sudo chgrp www-data /etc/ser2net.conf

#---- HW Seahu SH017 ----
echo "!!! must enable i2c device by raspi-config !!!"
sudo apt-get install i2c-tools
sudo apt-get install python3-smbus

#---- LCD ----
sudo apt-get install python3-pillow
#sudo cp sSH017/etc/init.d/beeepr /etc/init.d/
sudo systemctl enable beeper
sudo systemctl start beeper
#sudo cp sSH017/etc/init.d/graph-lcd /etc/init.d/
sudo systemctl enable graph_lcd
sudo systemctl start graph_lcd
#sudo cp sSH017/etc/init.d/lcd_menu /etc/init.d/
sudo systemctl enable lcd_menu
sudo systemctl start lcd_menu

#----- my_owfs ------
sudo apt-get install libtool m4 automake
sudo apt-get install libfuse-dev
sudo apt-get install libudev-dev
sudo apt-get install libusb-dev
#sudo apt-get install libusb-1.0
sudo apt-get install libusb-1.0-0-dev
sudo apt-get install pkg-config
pwd
git clone https://github.com/owfs/owfs.git
cd owfs
 ./bootstrap
 ./configure
./make -j4
sudo ./make install
#sudo cp sSH017/etc/owfs.conf /etc/

#--- w1_seahu_cd (kernle driver) ---
# instalace hlavickovych souboru kernelu (ty se totis instalji jinym balickem nez je bezne)
sudo apt-get install raspberrypi-kernel-headers
# download s gitu
cd ~
git clone https://github.com/seahu/seahu_CD.git
# instalce meho linuxoveho ovladace pro moje 1-Wire zarizeni
cd seahu_CD/linux-kernel-module
sudo ./doit
# ps: chce to upravit script aby pridal jeste zaznam do modules.dep - jenze modules.dep del manualove stranky by se nemel editovat protoze do budoucna neni vylocena zmena formatu tak na to kaslu


#--- MQTT mosquitto ---
sudo apt-get install mosquitto
sudo apt-get install mosquitto-clients
sudo apt-get install libmosquitto-dev
sudo cp ./SH017/etc/mosquitto/mosquitto.conf /etc/mosquitto

#--- MQTT web client ---
cd /var/www
sudo git clone https://github.com/hivemq/hivemq-mqtt-web-client.git
sudo cp ./SH017/etc/apache/conf-available/hivemq-mqtt-web-client.conf /etc/apache/conf-available/
sudo a2enconf hivemq-mqtt-web-client
sudo systemctl reload apache2

#---- 433MHz ------
# wiringPi
pwd
git clone https://github.com/WiringPi/WiringPi
cd WiringPi/
sudo ./build

#--- RFlink ----
wget https://project-downloads.drogon.net/wiringpi-latest.deb
sudo dpkg -i wiringpi-latest.deb
git clone https://github.com/sovserg/rflink.git
cd rflink/RPi_rflink/
./make
sudo ./make install
sudo cp ./SH017/etc/rflink.conf /etc/

#--- Domoticz ---
sudo bash -c "$(curl -sSfL https://install.domoticz.com)"
