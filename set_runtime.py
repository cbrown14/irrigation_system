#!/usr/bin/env python
import sys

if(len(sys.argv)<2):
	print "Need a run time argument"
	exit()


f = open("/home/pi/irrigation_system/config","r")

lines = f.readlines()
f.close()

f = open("/home/pi/irrigation_system/config","w")

for line in lines:
	if "_time" not in line and "_zones" not in line:
		f.write(line)

for x in range(0,(len(sys.argv)-1)):
	f.write("zone"+str(x)+"_time = "+sys.argv[x+1]+"\n")

f.write("number_of_zones = "+str((len(sys.argv)-1)))

f.close()
