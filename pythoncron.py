#!/usr/bin/env python

from crontab import CronTab
import sys

def main():
	
	cron = CronTab(user='root')
	cron.remove_all(comment='old')
	for item in sys.argv[1:]:
		items = item.split(":")
		hour = int(items[0])
		minute = int(items[1])
		job = cron.new(command = '/usr/bin/python2.7 /home/pi/test_stuff/2zone.py',comment = 'old')
		job.minute.on(minute)
		job.hour.on(hour)		
		cron.write()		
main()
