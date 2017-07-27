#!/bin/bash

# check atomticaly start modem
if /etc/local.rc | grep 'ifup ppp0' > /dev/nul ; then
    echo "Modem is allready configured automaticaly start after system ON."
else
    echo "Setiing start modem after system ON."
    echo "ifup ppp0 &"
fi

# chek if modem run
if /sbin/ifconfig | grep 'ppp0' > /dev/nul ; then
    echo "Modem allready run."
else
    echo "Start modem"
    nohup ifup ppp0 &
fi

