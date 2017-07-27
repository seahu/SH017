#!/bin/bash

main()
{
    case "$1" in
	enable)
	    #some code
	    update-rc.d rfx433MHz.sh enable
	    /etc/init.d/rfx433MHz.sh start
	    ;;
	disable)
	    #seme code
	    /etc/init.d/rfx433MHz.sh stop
	    update-rc.d rfx433MHz.sh disable
	    ;;
	status)
	    #env - if runn las line must be Started
	    /etc/init.d/rfx433MHz.sh status | grep "Running"
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