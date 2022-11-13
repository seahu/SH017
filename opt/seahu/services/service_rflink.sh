#!/bin/bash

main()
{
    case "$1" in
	enable)
	    #some code
	    #update-rc.d rflink enable
	    #/etc/init.d/rflink restart
	    systemctl enable rflink
	    #systemctl daemon-reload
	    systemctl start rflink
	    ;;
	disable)
	    #seme code
	    #/etc/init.d/rflink stop
	    #update-rc.d rflink disable
	    systemctl stop rflink
	    systemctl disable rflink
	    #systemcrl daemon-reload
	    ;;
	status)
	    #env - if runn las line must be Started
	    #/etc/init.d/rflink.sh status | grep "running"
	    systemctl status rflink | grep "running" > /dev/null
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