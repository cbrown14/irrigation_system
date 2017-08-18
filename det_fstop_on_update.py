#!/usr/bin/env python

from time import sleep
import RPi.GPIO as GPIO
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
import ConfigParser
import sys
import string
import re
import datetime
import os

def determine():

	sprinkler_times = []
	runtime = 0

	for i in sys.argv[1:]:
		if ':' in i:
			i = re.sub(':','',i)
			sprinkler_times.append(i)
		else:
			runtime += int(i)	

	dt = datetime.datetime.now()	
	dt = str(dt)
	list = dt.split(" ")
	timelist = list[1].split(":")
	cur_time = timelist[0] + timelist[1]
	
	for time in sprinkler_times:
		if ( int(cur_time) < int(time)  or (int(time)+runtime) < int(cur_time) ):
			continue
		else:
			return False
	return True

def now():
        return datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")


def load_config(filename='config'):
  config = ConfigParser.RawConfigParser()
  this_dir = os.path.abspath(os.path.dirname(__file__))
  config.read(config.read(this_dir + '/' + filename)
  if config.has_section('SprinklerConfig'):
      return {name:val for (name, val) in config.items('SprinklerConfig')}
  else:
      print 'Unable to read file %s with section SprinklerConfig' % filename
      print 'Make sure a file named config lies in the directory %s' % this_dir
      raise Exception('Unable to find config file')

def force_stop(config):
  zone_count = int(config['number_of_zones'])
  for x in range(0,zone_count):
        gpio_str = 'zone'+str(x)+'_gpio'
        runtime_str = 'zone'+str(x)+'_time'
        try:
                pin = int(config[gpio_str])
        except Exception as ex:
                print "Could not find value for "+gpio_str
        try:
                runtime = float(config[runtime_str])
        except Exception as ex:
                print "Could not find value for "+runtime_str

        with open(config['log_file'],'a') as log_file:
                try:
                        sleep(1)
                        GPIO.setup(pin, GPIO.OUT)
                        GPIO.output(pin, GPIO.LOW)
                        log_file.write('%s: Force stopped sprinkler for zone %s \n' % (now(),str(x)))
                        if(x == (zone_count-1)):
                                log_file.write('\n')
                except Exception as ex:
                        log_file.write('%s: An error has occurred: %s \n' % (now(), ex.message))
                        GPIO.output(pin, GPIO.LOW)


def main():
  turn_off = determine()
  print turn_off  
  if turn_off:
	os.system("pgrep -f 2zone | xargs sudo kill");
	config = load_config()
  	force_stop(config)



main()
