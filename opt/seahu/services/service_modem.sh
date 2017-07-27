#!/bin/bash

modem()
{
    case "$1" in
	enable)
	    #some code
	    # check atomticaly start modem
	    modem_conf=$(cat /etc/seahu/modem.cfg)
	    if [ "$modem_conf" = "1" ]; then
	        echo "Modem is allready configured automaticaly start after system ON."
	    else
	        echo "Setiing start modem after system ON."
	        echo "1" > /etc/seahu/modem.cfg
	    fi
	    # chek if modem run
	    if /sbin/ifconfig | grep 'ppp0' > /dev/nul ; then
	        echo "Modem allready run."
	    else
	        echo "Start modem"
	        nohup ifup ppp0 >/dev/null 2>/dev/null &
	    fi
	    ;;
	disable)
	    #some code
	    # check atomticaly start modem
	    modem_conf=$(cat /etc/seahu/modem.cfg)
	    if [ "$modem_conf" = "1" ]; then
	        echo "Delete automaticaly start modem after system ON."
	        echo "0" > /etc/seahu/modem.cfg
	    else
	        echo "Model allready do not automaticaly start."
	    fi
	    # chek if modem run
	    if /sbin/ifconfig | grep 'ppp0' > /dev/nul ; then
	        echo "Stopping modem"
	        ifdown ppp0
	    else
	        echo "Modem is allready stoped."
	    fi
	    ;;
	status)
	    /sbin/ifconfig | grep 'ppp0' > /dev/nul
	    #env - if runn las line must be Started
	    if [ $? -eq 0 ] ; then
		    echo "OK"
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

modem "$@"