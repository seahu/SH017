#!/bin/bash

main()
{
    case "$1" in
	enable)
	    #some code
	    update-rc.d rflink.sh enable
	    /etc/init.d/rflink.sh restart
	    ;;
	disable)
	    #seme code
	    /etc/init.d/rflink.sh stop
	    update-rc.d rflink.sh disable
	    ;;
	status)
	    #env - if runn las line must be Started
	    /etc/init.d/rflink.sh status | grep "running"
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