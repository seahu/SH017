#!/bin/bash

domoticz()
{
    case "$1" in
        enable)
            #some code
            systemctl enagble domoticz.sh
            #systemctl daemon-reload
            systemctl start domoticz
            ;;
        disable)
            #some code
            systemctl stop domoticz
            systemctl disable domoticz
            ;;
        status)
            #domoticze - if run las twoline contain "Started"
            systemctl status domoticz | grep "Active:" | grep "running"
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

domoticz "$@"
