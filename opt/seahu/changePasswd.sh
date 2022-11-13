#!/bin/bash

# prepare  install:
# sudo apt-get install whois

# lin in /etc/schadow hasnex format
# $id$prefix$encrypted
#
# where id defines the type of encryption and, reading further, can be one of.
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
passwd_old=$2
passwd_new=$3

correct=$(</etc/shadow awk -v user="$user" -F : 'user == $1 {print $2}')
prefix=${correct%"${correct#\$*\$*\$}"}
hash_type=${correct:1:1}
prefix=${prefix%%\$}
prefix=${prefix##*\$}

if [ "$hash_type" == "5" ]; then
    compare=$(mkpasswd -msha-256 $passwd $prefix)
fi
if [ "$hash_type" == "6" ]; then
    compare=$(mkpasswd -msha-512 $passwd $prefix)
fi

if [ "$correct" != "$compare" ] ; then
    echo "1"
    exit 1
fi
echo "$user:$passwd_new" | chpasswd
echo "0"
