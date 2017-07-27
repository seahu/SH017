#!/usr/bin/python

#beeper on seahu is control from pin 12 on raspberryPI (board pin numbering)

#this example run beeper for 2s

import RPi.GPIO as GPIO
import time
import imp # for load config pins file (beep_pin)

cfg=imp.load_source('config', '/etc/seahu/pin_config.py') #import config pins file


pin=cfg.beep_pin	#12
GPIO.setmode(GPIO.BOARD)
GPIO.setup(pin, GPIO.OUT)

#beep for 2s
def main():
	beep(2)

def beep(duration):
	# start beep
	GPIO.output(pin, 1)
	time.sleep(duration)
	GPIO.output(pin, 0)

main()
