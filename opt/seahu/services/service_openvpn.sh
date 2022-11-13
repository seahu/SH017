#!/bin/bash

openVPN()
{
    case "$1" in
        enable)
            #some code
	    #update-rc.d domoticz.sh default
	    systemctl enable openvpn
	    systemctl restart openvpn
	    ;;
        disable)
            #seme code
	    systemctl stop openvpn
	    systemctl disable openvpn
	    ;;
        status)
	    systemctl status openvpn | grep "Active: active " > /dev/null
            if [ $? -eq 0 ] ; then
                echo "OK"
            else
                echo  "NO"
            fi
	    ;;
	*)
	    echo "Usage: $0 {enable|disable|status}"
	    exit 3
	    ;;
    esac
}

#/etc/init.d/domoticz.sh status | tail -n 2 | grep "Started"
#exit 1
openVPN "$@"
