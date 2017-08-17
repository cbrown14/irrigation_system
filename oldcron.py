#!/usr/bin/env python

'''
This file will read the current root crontab file
and will parse the 'current settings' from the file
'''

from crontab import CronTab
import re

def main():
	cron = CronTab(user="root")
	cron.write("outcron.tab")

	file = open("outcron.tab","r")
	for line in file:
		if(line[0] == "*" or isNum(line[0])):
			list = re.findall(r"[\w']+",line)			
			x = 0
			while(isNum(list[x])):
				if(isNum(x)):
					if(list[x]==0 or list[x] == '0' and x%2==0):
						new = str(list[x]) + '0'
						print new
					elif((int(list[x]) < 10) and x%2 == 0):
						new = '0'+str(list[x])
						print new
					else:
						print int(list[x])
				x+=1

def isNum(s):
	try:
		int(s)
		return True
	except ValueError:
		return False

main()
