#!/bin/bash

rex()
{
    case "$1" in
	enable)
	    #some code
	    update-rc.d ser2net enable
	    /etc/init.d/ser2net start
	    ;;
	disable)
	    #seme code
	    /etc/init.d/ser2net stop
	    update-rc.d ser2net disable
	    ;;
	status)
	    #env - if runn las line must be Started
	    /etc/init.d/ser2net status | grep "active (running)"
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

rex "$@"