#!/bin/bash
if /sbin/ifconfig | grep tun0 > /dev/null ; then
    echo $(/sbin/ifconfig tun0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')
    echo $(/sbin/ifconfig tun0 | grep 'inet addr:' | cut -d: -f3 | awk '{ print $1}')
    echo $(/sbin/ifconfig tun0 | grep 'inet addr:' | cut -d: -f4 | awk '{ print $1}')
else
    echo ""
    echo ""
    echo ""
fi


