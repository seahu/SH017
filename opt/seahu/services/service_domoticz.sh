#!/bin/bash

domoticz()
{
    case "$1" in
        enable)
            #some code
	    #update-rc.d domoticz.sh default
            update-rc.d domoticz.sh enable
            /etc/init.d/domoticz.sh start
	    ;;
        disable)
            #seme code
            /etc/init.d/domoticz.sh stop
            update-rc.d domoticz.sh disable
	    ;;
        status)
            #domoticze - if run las twoline contain "Started"
            /etc/init.d/domoticz.sh status | tail -n 2 | grep "Started"
            if [ $? -eq 0 ] ; then
                echo "OK"
            else
                echo  "NO"
            fi
	    ;;
	*)
	    echo "Usage: $0 {enable|disable}"
	    exit 3
	    ;;
    esac
}

#/etc/init.d/domoticz.sh status | tail -n 2 | grep "Started"
#exit 1
domoticz "$@"
