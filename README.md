# hassSTAutoConfig
Home-Assistant &lt;--> Smartthings Auto Config

These scripts are used to help you create your configuration files for use with Home Assistant.  The subscribe script is to be used to run in the background to listen for any smartthings events, and determines what the capabilities are, then creates the correct files based on what it sees.  So, the longer it runs, the more it sees and the better the capabilites will be in home-assistant.

Prerequisites:
Must have PHP and PHP-yaml extension installed
> #: apt-get install php5 php5-yaml 
or
> #: yum install php5 php5-yaml 

Must have mosqitto-clients installed
> #: apt-get install mosquitto-clients
or
> #: yum install mosquitto-clients

*These scripts assume you have already set up Smarter Smartthings as instructed here:
https://www.home-assistant.io/blog/2016/02/09/Smarter-Smart-Things-with-MQTT-and-Home-Assistant/

Instructions:
1.  Clone this repository
2.  Run subscribe.php as a deamon with the following options:

    --host={hostname or IP of your MQTT broker} (required or it defaults to my install which is 192.168.0.169)
    
    --mosquitto_sub={location of moquitto_sub binary} (optional, will default to assume mosquitto_sub is in $PATH)
    
    --port={MQTT Port} (optional, uses 1883 as default)
    
    --help (Will output this in case you forget)
    
    Example:
>php subscribe.php --host=192.168.0.169 --mosquitto_sub=/volume1/@appstore/mosquitto/bin/mosquitto_sub

To run it as a deamon add nohup to the beginning and & to the end:
    
>nohup php subscribe.php --host=192.168.0.169 --mosquitto_sub=/volume1/@appstore/mosquitto/bin/mosquitto_sub &
3.  While it is running, try to control as many as your devices as possible in SmartThings.  For instance, turn on your lights (the subscriber will know it is a light now), set the dimmer on the light (the subscriber will now know it is a dimmable light), change the color of the light (the subscriber will now know it has color capabilities) etc.
* At any time, you can run the next part to generate the config files.  I would recommend doing this in a test directory for the first time, making sure it all looks good, then point it at your home-assistant config directory.  ALWAYS MAKE A BACKUP OF YOUR CONFIG BEFORE THE FIRST RUN
4.  Run analyzeDevices.php to generate configs based on what subscribe saw.
>php analyzeDevices.php --directory='~/.homeassistant/testConfigDir```
    or the command I actually run since I run home-assistant in a Docker:
>php analyzeDevices.php --directory='/volume1/docker/homeassistant/config
5.  Restart Home-assistant

Your devices should now be available in Home Assistant
You can modify any of the files it created and as long as you modify above or below where it tells you, your changes will remain.
