#!/bin/bash

## print header lines
#echo ""
#echo " mac                 essid         frq   chn qual   lvl  enc"

## test if wlan is up (for scan, cannot be set IP, netmask, .. ,but  must be up)
if ip link show wlan0 | grep "state UP" > /dev/null ; then
    wlanstatus="up"
else
    wlanstatus="down"
    rfkill unblock wifi
fi


#while IFS= read -r line; do
iwlist wlan0 scan | while read line; do

    ## test line contenst and parse as required
    [[ "$line" =~ Address ]] && mac=${line##*ss: }
    [[ "$line" =~ \(Channel ]] && { chn=${line##*nel }; chn=${chn:0:$((${#chn}-1))}; }
    [[ "$line" =~ Frequen ]] && { frq=${line##*ncy:}; frq=${frq%% *}; }
    [[ "$line" =~ Quality ]] && { 
        qual=${line##*ity=}
        qual=${qual%% *}
        lvl=${line##*evel=}
        lvl=${lvl%% *}
    }
    [[ "$line" =~ Encrypt ]] && enc=${line##*key:}
    [[ "$line" =~ ESSID ]] && {
        essid=${line##*ID:}
        essid=${essid%%\"}
        essid=${essid##\"}
        echo "$mac;$essid;$frq;$chn;$qual;$lvl;$enc"  # output after ESSID
    }

done

if [ $wlanstatus = "down" ] ; then
    rfkill block wifi
fi
