#!/usr/bin/python

# this utility is only for conver repelace tab for spaces in python source files
import os
import sys

spaces="        "

if __name__ == "__main__":
    print(f"Arguments count: {len(sys.argv)}")
    for i, arg in enumerate(sys.argv):
        if i==0 : continue
        print(f"file {i:>6}: {arg}")

        file_path=arg
        file = open(file_path, 'r')
        content = file.read()
        content = content.replace("\t", spaces)
        file.close()
        file = open(file_path, 'w')
        file.write(content)
