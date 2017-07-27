#!/bin/bash

rex()
{
    case "$1" in
	enable)
	    #some code
	    update-rc.d rexevn enable
	    update-rc.d rexcore enable
	    /etc/init.d/rexenv start
	    /etc/init.d/rexcore start
	    ;;
	disable)
	    #seme code
	    /etc/init.d/rexcore stop
	    /etc/init.d/rexevn stop
	    update-rc.d rexcore disable
	    update-rc.d rexevn disable
	    ;;
	status)
	    #env - if runn las line must be Started
	    /etc/init.d/rexenv status | tail -n 1 | grep Started
	    if [ $? -eq 0 ] ; then
		#core - if run las twoline contain "RexCore is running"
		/etc/init.d/rexcore status | tail -n 2 | grep "RexCore is running"
		if [ $? -eq 0 ] ; then
		    echo "OK"
		else
		    echo  "NO"
		fi
	    else
		echo "NO"
	    fi
	    ;;
	*)
	    echo "Usage: $0 {enable|disable}"
	    exit 3
	    ;;
    esac
}

rex "$@"