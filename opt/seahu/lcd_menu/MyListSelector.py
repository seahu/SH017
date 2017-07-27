# ListSelector.py
#
# Created by Alan Aufderheide, February 2013
# Modify by Ing. Ondrej Lzcka, october 2016
#
# Given a list of items in the passed list,
# allow quick access by picking letters progressively.
# Uses up/down to go up and down where cursor is.
# Move left/right to further filter to quickly get to item.
# Still need to do case insensitive, and sort.
from time import sleep
from Adafruit_CharLCD import Adafruit_CharLCD

class ListSelector:
    def __init__(self, theList, theLcd):
        self.list = []
        for item in theList:
            if isinstance(item, basestring):
                self.list.append(item)
            else:
                self.list.append(item[0])
        self.lcd = theLcd
	print self.list

    def Pick(self):
	print "start PICK"
	if len(self.list)==0:
    	    self.lcd.clear()
	    self.lcd.set_cursor(0,1)
	    self.lcd.message("Emptny list")
	    sleep(3)
	    return False
	ROWS=4
	COLUMS=16
	cur=0
	startPage=0
	change=True
	sleep(0.25) #some time to end previously SELECT
        while 1:
            if self.lcd.buttonPressed(self.lcd.SELECT):
                break
            if self.lcd.buttonPressed(self.lcd.ESC):
                return False
            if self.lcd.buttonPressed(self.lcd.UP):
		if cur>0 :
		    cur-=1
		    if cur<startPage : startPage=cur;
		    change=True
            if self.lcd.buttonPressed(self.lcd.DOWN):
		if cur<(len(self.list)-1):
		    cur+=1
		    if cur>(startPage+ROWS-1) : startPage=cur-ROWS+1
		    change=True
	    if change==True:
		print "cur:", cur
		print "startPage:", startPage
    		self.lcd.clear()
		self.lcd.home()
		for i in range(ROWS):
		    if (startPage+i)>(len(self.list)-1) : break
		    if (startPage+i)==cur :
			item=">"+self.list[cur][:COLUMS-2]+"<"
			inversion=True
		    else :
			item=" "+self.list[startPage+i][:COLUMS-2]
			inversion=False
		    self.lcd.set_cursor(0,i)
		    self.lcd.message(item, inversion)
		    sleep(0.02) # wait to end previousy message (I use primitive socket seerver for print on lcd who serve only one connection at some time, therfore fast sending packet my by doing big delay)
		change=False
	    sleep(0.15)
        return [cur] #return list with one item context cursor, becouse when cur=0 my be evaluate as return False

