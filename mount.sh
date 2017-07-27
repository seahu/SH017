#!/bin/bash

#mouting all directory will be copy to github

Mount() {
    #run as sudo
    #/var/www
    sudo mount --bind /var/www/ /home/pi/SH017/var/www/
    sudo mount --bind /var/www/html /home/pi/SH017/var/www/html
    sudo mount --bind /var/www/rflink /home/pi/SH017/var/www/rflink
    sudo mount --bind /var/www/rfx433MHz /home/pi/SH017/var/www/rfx433MHz
    sudo mount --bind /var/www/seahu /home/pi/SH017/var/www/seahu
    sudo mount --bind /var/www/ser2net  /home/pi/SH017/var/www/ser2net
    #/opt
    sudo mount --bind /opt/rflink/ /home/pi/SH017/opt/rflink/
    sudo mount --bind /opt/seahu/ /home/pi/SH017/opt/seahu/
    #/etc
}

Umount() {
    #run as sudo
    #/var/www
    sudo umount /home/pi/SH017/var/www/
    sudo umount /home/pi/SH017/var/www/html
    sudo umount /home/pi/SH017/var/www/rflink
    sudo umount /home/pi/SH017/var/www/rfx433MHz
    sudo umount /home/pi/SH017/var/www/seahu
    sudo umount /home/pi/SH017/var/www/ser2net
    #/opt
    sudo umount /home/pi/SH017/opt/rflink/
    sudo umount /home/pi/SH017/opt/seahu/
    #/etc

}

Update() {
    #update files and directores who not mount
    #/etc
    #/etc/init.d
    sudo cp /etc/init.d/domoticz.sh /home/pi/SH017/etc/init.d/
    sudo cp /etc/init.d/rflink.sh /home/pi/SH017/etc/init.d/
    sudo cp /etc/init.d/rfx433MHz.sh /home/pi/SH017/etc/init.d/
    #etc/network
    sudo cp /etc/network/interfaces /home/pi/SH017/etc/network/
    sudo cp /etc/network/interfaces.d/* /home/pi/SH017/etc/network/interfaces.d/
    #wifi
    sudo cp /etc/wpa_supplicant/wpa_supplicant.conf /home/pi/SH017/etc/wpa_supplicant/
    sudo cp /etc/hostapd/hostapd.conf /home/pi/SH017/etc/hostapd/
    sudo cp /etc/hostapd/hostapd.conf.with_comments /home/pi/SH017/etc/hostapd/
    sudo cp /etc/dnsmasq.d/dnsmasq-wlan0.conf /home/pi/SH017/etc/dnsmasq.d/
    #ppp
    sudo cp /etc/wvdial.conf /home/pi/SH017/etc/
    sudo cp /etc/wvdial.conf.orig /home/pi/SH017/etc/
    sudo cp /etc/ppp/peers/Provider /home/pi/SH017/etc/ppp/peers/
    sudo cp /etc/chatscripts/Provider /home/pi/SH017/etc/chatscripts/
    #ser2net
    sudo cp /etc/ser2net.conf /home/pi/SH017/etc/
    #owfs
    sudo cp /etc/owfs.conf /home/pi/SH017/etc/
    #rc.local
    sudo cp /etc/rc.local /home/pi/SH017/etc/
    #rflink
    sudo cp /etc/rflink.conf /home/pi/SH017/etc/
    #sudoers
    sudo cp /etc/sudoers /home/pi/SH017/etc/
    #apache
    sudo cp -R /etc/apache2/* /home/pi/SH017/etc/apache2/
    #seahu
    sudo cp /etc/seahu/* /home/pi/SH017/etc/seahu/
    #/home/pi/examples
    sudo cp -R /home/pi/examples/* /home/pi/SH017/home/pi/examples/

    #right
    #chmod -R /home/pi/SH017/etc/* o=r
    sudo find /home/pi/SH017/etc -type d -exec chmod o=rx {} +
    sudo find /home/pi/SH017/etc -type f -exec chmod o=r {} +

}

GitRemove() {
    #remove all git stops
    find . -type f | grep -i "\.git" | xargs rm
}

ListPkg() {
    #list of instaled pakages
    apt list --installed > /home/pi/SH017/list_intalled_packages.txt
}

case "$1" in
    mount)
	Mount
	;;
    umount)
	Umount
	;;
    update)
	Update
	;;
    gitRemove)
	GitRemove
	;;
    list)
	ListPkg
	;;
    *)
	SCRIPTNAME="${0##*/}"
	echo "Usage: $SCRIPTNAME {mount|umount|update|gitRemove|list}"
	;;
esac

exit 0