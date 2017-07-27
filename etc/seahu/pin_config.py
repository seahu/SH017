#pin configuration for SEAHU SH017 hardware
# primary for python programs, also can be used for bash programs

# LCD display (GPIO.BOARD numbering)
#------------
LCD_CS=24
LCD_RST=22
LCD_A0=18
LCD_CLK=23
#LCD_SI=21 #v.1
LCD_SI=19 #v.2

# BEEP  (GPIO.BOARD numbering)
#-----
beep_pin=12

# RS485 (GPIO.BOARD numbering)
#-------
RS485_RE_DE=11

# RFX 433MHz
#----------
#(GPIO.BOARD numbering)
RX_data=40 
TX_data=38

#(wiringpi numbering)
PIN_TX=28
PIN_RX=29


# I2C 8-bit IO expander on LCD BARD (lcdlight and buttons + "software serve beep"))
#----------------------------------
#addr_i2c=0x24 # v.1
addr_i2c=0x3c #v.2

# I2C 8-bit IO expander on BASE BOARD ( 4xrelays + 2xinput + 2xoutput )
#------------------------------------
#addr2_i2c=0x20 # v.1
addr2_i2c=0x38 # v.2

