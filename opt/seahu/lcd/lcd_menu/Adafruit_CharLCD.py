# Copyright (c) 2014 Adafruit Industries
# Author: Tony DiCola
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
import socket
import time
import smbus
import RPi.GPIO as GPIO
import imp

cfg=imp.load_source('config', '/etc/seahu/pin_config.py') #load pins goinfiguration


#from graph_lcd import *
#import graph_lcd


# Entry flags
LCD_ENTRYRIGHT          = 0x00
LCD_ENTRYLEFT           = 0x02
LCD_ENTRYSHIFTINCREMENT = 0x01
LCD_ENTRYSHIFTDECREMENT = 0x00

# Control flags
LCD_DISPLAYON           = 0x04
LCD_DISPLAYOFF          = 0x00
LCD_CURSORON            = 0x02
LCD_CURSOROFF           = 0x00
LCD_BLINKON             = 0x01
LCD_BLINKOFF            = 0x00

# Move flags
LCD_DISPLAYMOVE         = 0x08
LCD_CURSORMOVE          = 0x00
LCD_MOVERIGHT           = 0x04
LCD_MOVELEFT            = 0x00

# Char LCD plate button names.
SELECT                  = 0xEF
RIGHT                   = 0xFD
DOWN                    = 0xF7
UP                      = 0xFB
LEFT                    = 0xFE
ESC                     = 0xDF



class Adafruit_CharLCD(object):
    """Class to represent and interact with an HD44780 character LCD display."""

    def __init__(self, cols=16, lines=2, backlight=True):

        self.bus = smbus.SMBus(1)
        self.addr_i2c = cfg.addr_i2c  #0x3c
        self.addr2_i2c = cfg.addr2_i2c # 0x38 # registr pro obsluhu relatek a vstupu
        #io_init()
        #lcd_init()
        # Save column and line state.
        self._cols = cols
        self._lines = lines
        self.curX = 0
        self.curY = 0
        # Save backlight state.
        self.backlight = backlight
        # Setup all pins as outputs.
        # Setup backlight.
        self.backlight=False
        if backlight is not None:
            self.LCDon()
        # Initialize the display.
        self.clear()
        # Char LCD plate button names.
        self.SELECT                  = 0xEF
        self.RIGHT                   = 0xFD
        self.DOWN                    = 0xF7
        self.UP                      = 0xFB
        self.LEFT                    = 0xFE
        self.ESC                     = 0xDF

        # variables for control speed keys (buttons)
        self.buttonCount=0
        self.lastButton=0xFF
        self.buttonTime=0
        self.buttonSleep=0.25
        self.beepDuration=0.1

        #beep
        self.pin=12
        GPIO.setmode(GPIO.BOARD)
        GPIO.setup(self.pin, GPIO.OUT)

    def LCDon(self):
        if self.backlight==True: return
        mask=0x40
        Imask=mask^0xFF
        key_mask=0x3f
        self.bus.write_byte(self.addr_i2c, self.bus.read_byte(self.addr_i2c)|mask|key_mask)
        self.backlight=True



    def LCDoff(self):
        if self.backlight==False: return
        mask=0x40
        Imask=mask^0xFF
        key_mask=0x3f
        self.bus.write_byte(self.addr_i2c, (self.bus.read_byte(self.addr_i2c)|key_mask)&Imask)
        self.backlight=False

    def home(self):
        """Move the cursor back to its home (first line and first column)."""
        self.curX = 0
        self.curY = 0

    def clear_direct(self):
        """Clear the LCD."""
        lcd_clear()

    def clear(self):
        """Clear the LCD."""
        # use tcp socken on localhost with port 10000 to send display service
        # display service use before more proces can by use display on some time
        client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        client_socket.connect(('localhost', 10000))
        client_socket.send("c".encode('ascii'))
        client_socket.close()

    def  image(self,image):
        #image is name of image stored into /opt/sehu/lcd_images andmust be balck and white with resolution 128x64px
        # use tcp socken on localhost with port 10000 to send display service
        # display service use before more proces can by use display on some time
        client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        client_socket.connect(('localhost', 10000))
        client_socket.send( ("m,%s" %image).encode('ascii') )
        client_socket.close()

    def set_cursor(self, col, row):
        """Move the cursor to an explicit column and row position."""
        # Clamp row to the last row of the display.
        if row > self._lines:
            row = self._lines - 1
        # Set location.
        self.curX = col
        self.curY = row

    def enable_display(self, enable):
        """Enable or disable the display.  Set enable to True to enable."""
        if enable:
            self.displaycontrol |= LCD_DISPLAYON
        else:
            self.displaycontrol &= ~LCD_DISPLAYON

    def show_cursor(self, show):
        """Show or hide the cursor.  Cursor is shown if show is True."""
        if show:
            self.displaycontrol |= LCD_CURSORON
        else:
            self.displaycontrol &= ~LCD_CURSORON

    def blink(self, blink=True):
        """Turn on or off cursor blinking.  Set blink to True to enable blinking."""
        return
        #if blink:
        #    self.displaycontrol |= LCD_BLINKON
        #else:
        #    self.displaycontrol &= ~LCD_BLINKON

    def move_left(self):
        """Move display left one position."""

    def move_right(self):
        """Move display right one position."""

    def set_left_to_right(self):
        """Set text direction left to right."""
        self.displaymode |= LCD_ENTRYLEFT

    def set_right_to_left(self):
        """Set text direction right to left."""
        self.displaymode &= ~LCD_ENTRYLEFT

    def autoscroll(self, autoscroll):
        """Autoscroll will 'right justify' text from the cursor if set True,
        otherwise it will 'left justify' the text.
        """
        if autoscroll:
            self.displaymode |= LCD_ENTRYSHIFTINCREMENT
        else:
            self.displaymode &= ~LCD_ENTRYSHIFTINCREMENT

    def LCDprint(self,str, inversion=False):
        lcd_ascii168_string(self.curX*8,self.curY*2,str,inversion)
        self.curX += len(str)

    def message(self, text, inversion=False):
        """Write text to display.  Note that text can include newlines."""
        # use tcp socken on localhost with port 10000 to send display service
        # display service use before more proces can by use display on some time
        print("-------")
        print(self.curX, self.curY)
        print(text)
        client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        client_socket.connect(('localhost', 10000))
        x="%d" %self.curX
        if len(x)==1: x="0"+x
        y="%d" %self.curY
        if len(y)==1: y="0"+y
        if inversion: i="i"
        else : i="p"
        client_socket.send( ("%s,%s,%s,%s" %(i,x,y,text)).encode('ascii') )
        client_socket.close()
        time.sleep(0.1)

    def message_direct(self, text, inversion=False):
        """Write text to display.  Note that text can include newlines."""
        print("-------")
        print(self.curX, self.curY)
        print(text)
        line = 0
        # Iterate through each character.
        for char in text:
            # Advance to next line if character is a new line.
            if char == '\n':
                line += 1
                col = 0 
                self.set_cursor(col, line)
            # Write the character to the display.
            else:
                self.LCDprint(char, inversion)

    def set_backlight(self, backlight):
        """Enable or disable the backlight.  If PWM is not enabled (default), a
        non-zero backlight value will turn on the backlight and a zero value will
        turn it off.  If PWM is enabled, backlight can be any value from 0.0 to
        1.0, with 1.0 being full intensity backlight.
        """
        self.backlight = backlight
        if self.backlight : self.LCDon()
        else : self.LCDoff()


    def _delay_microseconds(self, microseconds):
        # Busy wait in loop because delays are generally very short (few microseconds).
        end = time.time() + (microseconds/1000000.0)
        while time.time() < end:
            pass

    def beep(self):
        mask=0x80
        Imask=mask^0xFF
        key_mask=0x3f
        self.bus.write_byte(self.addr_i2c, self.bus.read_byte(self.addr_i2c)&Imask)
        GPIO.output(self.pin, 1)
        time.sleep(self.beepDuration)
        GPIO.output(self.pin, 0)
        self.bus.write_byte(self.addr_i2c, self.bus.read_byte(self.addr_i2c)|mask|key_mask)


    def buttonPressed(self, button):
        #global variable buttonTime set speed of keys
        global buttonCount
        global lastButton
        global buttonTime
        """Return True if the provided button is pressed, False otherwise."""
        if button not in set((SELECT, RIGHT, DOWN, UP, LEFT, ESC)):
            raise ValueError('Unknown button, must be SELECT, RIGHT, DOWN, UP, LEFT, or ESC .')
        buttonCode = self.bus.read_byte(self.addr_i2c)|0xC0
        # check when button was pressed for change  sped ok key
        if self.lastButton!=buttonCode:
            self.buttonTime=time.time()
            print("last button code  :", self.lastButton)
            print("actual button code:", buttonCode)
        #if self.bus.read_byte(self.addr_i2c)|0xC0 == button:
        if buttonCode == button:
            #check spee of repeate pressed key
            if time.time()-self.buttonTime>1.25:
                print("fast")
                self.buttonSleep=0.01
                self.beepDuration=0.001
            else:
                print("slow")
                self.buttonSleep=0.5
                self.beepDuration=0.1
            self.beep()
            self.lastButton=buttonCode
            return True
        self.lastButton=buttonCode
        return False


    def readRegister(self):
        """Precte obsah registru obsluhujici vstupy arelatka."""
        return( self.bus.read_byte(self.addr2_i2c) )

    def writeRegister(self, byte):
        """Precte obsah registru obsluhujici vstupy arelatka."""
        return( self.bus.write_byte(self.addr2_i2c, byte) )
