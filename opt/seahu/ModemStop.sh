#!/bin/bash

# check atomticaly start modem
if /etc/local.rc | grep 'ifup ppp0' > /dev/nul ; then
    echo "Deleteting automaticaly start modem after system ON."
    sed -i "/nohup ifup ppp0 &\n/d" file
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

