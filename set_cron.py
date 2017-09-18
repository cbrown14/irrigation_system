#!/usr/bin/env python

from crontab import CronTab
import sys

compiler_directory = '/usr/bin/python2.7'
directory = '/home/pi/irrigation_system/sprinkler_main.py'
tag = 'old'

def main():
	
	cron = CronTab(user='root')
	cron.remove_all(comment='old')
	for item in sys.argv[1:]:
		items = item.split(":")
		hour = int(items[0])
		minute = int(items[1])
		job = cron.new(command = compiler_directory + ' ' + directory,comment = tag)
		job.minute.on(minute)
		job.hour.on(hour)
		cron.write()	
main()
