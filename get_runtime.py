#!/usr/bin/env python
import ConfigParser

'''
This file will parse the config file
and get the run times for each zone, in order
'''

def load_config(filename='config'):
  config = ConfigParser.RawConfigParser()
  config.read('/home/pi/test_stuff/config')
  if config.has_section('SprinklerConfig'):
      return {name:val for (name, val) in config.items('SprinklerConfig')}
  else:
      print 'Unable to read file %s with section SprinklerConfig' % filename
      print 'Make sure a file named config lies in the directory %s' % this_dir
      raise Exception('Unable to find config file')

def main():
	config = load_config()	
	try:
		zone_count = int(config['number_of_zones'])
		for x in range(0,zone_count):
			time_str = 'zone'+str(x)+'_time'
			print config[time_str]
	except Exception as ex:
		print "Trouble getting run times"

main()
