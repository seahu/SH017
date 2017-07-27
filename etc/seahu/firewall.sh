#!/bin/bash

# set SEAHU firewall
/sbin/iptables --flush
/sbin/iptables --delete-chain
/sbin/iptables --table nat --flush
/sbin/iptables --table nat --delete-chain
/sbin/iptables --table nat --append POSTROUTING ! --out-interface wlan0 -j MASQUERADE
/sbin/iptables --append FORWARD --in-interface wlan0 -j ACCEPT
