#!/bin/bash

main()
{
    case "$1" in
	enable)
	    #some code
	    #update-rc.d rfx433MHz.sh enable
	    #/etc/init.d/rfx433MHz.sh start
	    systemctl enable rfx433MHz
	    #systemctl daemon-reload
	    systemctl start rfx433MHz
	    ;;
	disable)
	    #seme code
	    #/etc/init.d/rfx433MHz.sh stop
	    #update-rc.d rfx433MHz.sh disable
	    systemctl stop rfx433MHz
	    systemctl disable rfx433MHz
	    #systemctl daemon-reload
	    ;;
	status)
	    #env - if runn las line must be Started
	    #/etc/init.d/rfx433MHz.sh status | grep "Running"
	    systemctl status rfx433MHz | grep "running" > /dev/null
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

main "$@"