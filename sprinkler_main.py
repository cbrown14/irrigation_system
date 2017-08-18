import os 
import sys 
import requests 
import ConfigParser 
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
  config.read(this_dir + '/' + filename)
  if config.has_section('SprinklerConfig'):
      return {name:val for (name, val) in config.items('SprinklerConfig')}
  else:
      print 'Unable to read file %s with section SprinklerConfig' % filename
      print 'Make sure a file named config lies in the directory %s' % this_dir
      raise Exception('Unable to find config file')

def get_precip_in_window(config,time_win_hr=24): 
    yesterday = (datetime.datetime.today() - \
                 datetime.timedelta(days=1)).strftime('%Y%m%d')
    today = datetime.datetime.today().strftime('%Y%m%d')
    
    # Get observations for today and yesterday
    try:
        r_yesterday = get_wu_history(config, yesterday)
        t_yesterday, vals_yesterday = get_rainfall(r_yesterday)
    except Exception as ex:
        return None
    
    try:
        r_today = get_wu_history(config, today)
        t_today, vals_today = get_rainfall(r_today)
    except Exception as ex:
        return None
        
    try:
        t = t_yesterday + t_today
        t.append(0)
        vals = vals_yesterday + vals_today
        if len(vals)>0:
          vals.append(vals[-1])
        else:
          vals.append(0)
        t_win = [s for s in t if s >= -time_win_hr * 3600]
        val_win = [vals[i] for i in range(len(vals)) if t[i] >= -time_win_hr * 3600]
        total = integrate(t_win, val_win)
    except Exception as ex:
        raise ex
        total = 0.0
    return total

def get_wu_history(config, day):
    API_URL = 'http://api.wunderground.com/api/{key}/history_{day}/q/{state}/{town}.json'
    return requests.get(API_URL.format(key=config['api_key'],
                                       day=day,
                                       state=config['state'],
                                       town=config['town']))

def get_rainfall(r):
    obs = r.json()['history']['observations']
    dates = [obs[i]['date'] for i in range(len(obs))]
    vals = [float(obs[i]['precipi']) for i in range(len(obs))]
    vals = [val if val >= 0 else 0 for val in vals]
    vals = [val / 3600.0 for val in vals] # -> inches / second
    dts = [datetime.datetime(year=int(dates[i]['year']),
                             month=int(dates[i]['mon']),
                             day=int(dates[i]['mday']),
                             hour=int(dates[i]['hour']),
                             minute=int(dates[i]['min'])) for i in range(len(dates))]
    now = datetime.datetime.now()
    t = [(dts[i] - now).total_seconds() for i in range(len(dates))]
    return t, vals

def integrate(t, vals):
    total = 0.0
    for i in range(len(vals) - 1):
        r = 0.5 * (vals[i] + vals[i + 1]) * (t[i + 1] - t[i])
        if r > 0: # sanity check in case of bad vals
            total += r
    return total


def run_sprinkler(config):
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
			sleep(5)
      			GPIO.setup(pin, GPIO.OUT)
      			log_file.write('%s: Starting sprinkler for zone %s \n' % (now(),str(x)))
      			GPIO.output(pin, GPIO.HIGH)
      			sleep(runtime * 60)
      			log_file.write('%s: Stopping sprinkler for zone %s \n' % (now(),str(x)))
			if(x == (zone_count-1)):
				log_file.write('\n')
      			GPIO.output(pin, GPIO.LOW)
    		except Exception as ex:
      			log_file.write('%s: An error has occurred: %s \n' % (now(), ex.message))
      			GPIO.output(pin, GPIO.LOW)


def init(config):
	try:
		zone_count = int(config['number_of_zones'])
		for x in range(0,zone_count):
			gpio_str = 'zone'+str(x)+'_gpio'
			try:
				pin = int(config[gpio_str])
				GPIO.setup(pin,GPIO.OUT)
				GPIO.output(pin,GPIO.LOW)
			except Exception as ex:
				print "Trouble initializing pin for "+gpio_str

	except Exception as ex:
		print "Could not find value for zount_count"

def main():
  # Load configuration file
  config = load_config()
  init(config)  
  with open(config['log_file'],'a') as log_file:
    # Get past 24 hour precip
    rainfall = get_precip_in_window(config)
    if rainfall is None:
      log_file.write('%s: Error getting rainfall amount, setting to 0.0 in\n' % current_time)
      rainfall = 0.0
    else:
      log_file.write('%s: Rainfall: %f inches\n' % (now(), rainfall))
  log_file.close()
    
  # If this is less than RAIN_THRESHOLD_IN run sprinkler
  
  if rainfall <= float(config['rain_threshold_in']):
    run_sprinkler(config)

main()
