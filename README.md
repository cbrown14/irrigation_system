# irrigation_system
# Cameron Brown
# 5/14/2016

This project is a web server controlled irrigation system implemented with a Raspberry Pi 2 microcontroller.

Installed on the Pi is an apache server. This hosts the Pi's website, allowing users to interface remotely with
the irrigation system. The website is written in HTML, with PHP used to pass data to various python scripts. 
These scripts interact with the Pi's crontab, and the main irrigation system code config file, allowing zone-specific
run times and time of day settings to be controlled via the website.

The file that runs the irrigation system reads a config file to pass information regarding which zone to run and how long.
This file also utilizes a Weather Underground's API to determine current ground moisture levels. Information regarding
the API, such as location and API key number are in the config file as well.

The file that runs the irrigation system is broken into four sections:
1) Read config file for current settings.
2) Import API information.
3) Determine if irrigation system needs to be ran.
4) Run system for designated time.

The irrigation piping system has a in-line 12V DC solenoid valve, one for each zone, allowing the Pi to start and stop
watering to each zone. The Pi interacts with a valve by utilizing a 5V relay to pass a 12V battery's current to the valves.
However, since the Pi's GPIO pins are 3.3V when high, a MOSFET is used to open and close the relay by permitting the
relay's access to ground.
