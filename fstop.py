#!/usr/bin/env python

import ConfigParser
import os
import sys
import datetime
from time import sleep
import RPi.GPIO as GPIO
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)

def now():
        return datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")


def load_config(filename='config'):
  config = ConfigParser.RawConfigParser()
  this_dir = os.path.abspath(os.path.dirname(__file__))
  config.read('/home/pi/test_stuff/' + filename)
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
  # Load configuration file
  config = load_config()
  force_stop(config)

main()
	
