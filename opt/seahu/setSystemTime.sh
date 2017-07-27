#!/bin/bash

min=$1
hour=$2
day=$3
month=$4
year=$5

size_min=${#min}
size_hour=${#hour}
size_day=${#day}
size_month=${#month}
size_year=${#year}

if [ size_min eq 1 ]   ; then min="0$min"     ; fi;
if [ size_hour eq 1 ]  ; then hour="0$hour"   ; fi;
if [ size_day eq 1 ]   ; then day="0$day"     ; fi;
if [ size_month eq 1 ] ; then month="0$month" ; fi;
if [ size_year eq 2 ]  ; then year="20$year"  ; fi;

date +%Y%m%d -s "$year$mounth$day"
date +%T -s "$hour:$min:00"

#in rasbian is bug on PCF8563 kernel module therefore I can't use hwclock, bay puthon script
#hwclock --systohc
/opt/seahu/PCF8563.py --systohc
