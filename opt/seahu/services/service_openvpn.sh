#!/bin/bash

openVPN()
{
    case "$1" in
        enable)
            #some code
	    #update-rc.d domoticz.sh default
            update-rc.d openvpn enable
            /etc/init.d/openvpn restart
	    ;;
        disable)
            #seme code
            /etc/init.d/openvpn stop
            update-rc.d openvpn disable
	    ;;
        status)
            #domoticze - if run las twoline contain "Started"
            /etc/init.d/openvpn status | tail -n 2 | grep "Started"
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
