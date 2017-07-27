#!/usr/bin/python

# Seahu SH017 use two IOexpanders PCF8574
# one on baseboard is used for control 4x relay + 2x optical isolation input + 2x otical isolation output
# secound on display board is used for control buttons and LCD backlight

# Pin 8-bit expanders (5V logic) to raspberry
# PCF8574           RASPBERRY PI GPIO (board pin number)
#-----------       ---------------------
# CLK                  5 (throught 5V/3V converter)
# SDA                  3 (throught 5V/3V converter)

# Pins of expander on baseboard with i2C address 0x38 or 0x20
#------------------------------------------------------------
# P0                   relay1
# P1                   relay2
# P2                   relay3
# P3                   relay4
# P4                   input1
# P5                   input2
# P6                   output1
# P7                   output2

# Pins of expander on display board with i2C address 0x3C or 0x24
#----------------------------------------------------------------
# P0                   left  button
# P1                   right button
# P2                   up    button
# P3                   down  button
# P4                   ok    button
# P5                   esc   button
# P6                   LCD backlight
# P7                   not use

# next example set on relay1 for 2s

import smbus
import time
import imp # for import file with config

cfg=imp.load_source('config', '/etc/seahu/pin_config.py')
base_board_addr=cfg.addr2_i2c	#0x38

def main():
	# on relay1
	set_bit_of_io_expander(base_board_addr, 0, 0)
	print (get_bit_of_io_expander(base_board_addr, 0))
	# wait 2s
	time.sleep(2.0)
	# off relay1
	set_bit_of_io_expander(base_board_addr, 0, 1)
	print (get_bit_of_io_expander(base_board_addr, 0))

def set_bit_of_io_expander(addr_i2c, bit_number, set):
	bus = smbus.SMBus(1) # 1 is number of i2c bus
	mask = 1<<bit_number
	inverse_mask=mask^0xFF
	if set==1 :
		bus.write_byte(addr_i2c, bus.read_byte(addr_i2c)|mask) # this more complication because save state other pins
	else :
		bus.write_byte(addr_i2c, bus.read_byte(addr_i2c)&inverse_mask)

def get_bit_of_io_expander(addr_i2c, bit_number):
	bus = smbus.SMBus(1)
	mask = 1<<bit_number
	if bus.read_byte(addr_i2c)&mask==0 :
		return 0
	else :
		return 1

main()
