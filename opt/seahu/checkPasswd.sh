#!/bin/bash

# prepare  install:
# sudo apt-get install whois

# lin in /etc/schadow hasnex format
# $id$prefix$encrypted
#
# where id defines the type of encryption and, reading further, can be one of 
#         ID  | Method
#          ---------------------------------------------------------
#          1   | MD5
#          2a  | Blowfish (not in mainline glibc; added in some
#              | Linux distributions)
#          5   | SHA-256 (since glibc 2.7)
#          6   | SHA-512 (since glibc 2.7)
#
# next command generete equvalent cecord to /etc/shadow
# mkpasswd -msha-512 passwd prefix


user=$1
passwd=$2

correct=$(</etc/shadow awk -v user="$user" -F : 'user == $1 {print $2}')
prefix=${correct%"${correct#\$*\$*\$}"}
prefix=${prefix%%\$}
prefix=${prefix##*\$}

compare=$(mkpasswd -msha-512 $passwd $prefix)

if [ "$correct" = "$compare" ] ; then
    echo -n "0"
    exit 0
else
    echo -n "1"
    exit 1
fi
