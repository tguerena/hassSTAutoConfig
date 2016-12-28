<?php
//Configurables

$directory = "testconfig";

// Changes will be written to the testconfig folder.

// Uncomment the following line and comment the above - testconfig line so changes are written directly to your config files

//    $directory = "..";

//End of Configurables
$contents = file_get_contents("devices.txt");
$json = json_decode($contents);
$binarySensors = array(
    "beacon"=>array(
        "payload_on"=>"present",
        "payload_off"=>"not present"),
    "contact"=>array(
        "payload_on"=>"open",
        "payload_off"=>"closed"),
    "door"=>array(
        "payload_on"=>"open",
        "payload_off"=>"closed"),
    "motion"=>array(
        "payload_on"=>"active",
        "payload_off"=>"inactive"),
    "mute"=>array(
        "payload_on"=>"muted",
        "payload_off"=>"unmuted"),
    "presence"=>array(
        "payload_on"=>"present",
        "payload_off"=>"not present"),
    "shock"=>array(
        "payload_on"=>"detected",
        "payload_off"=>"clear"),
    "sleeping"=>array(
        "payload_on"=>"sleeping",
        "payload_off"=>"not sleeping"),
    "smoke"=>array(
        "payload_on"=>"detected",
        "payload_off"=>"clear"),
    "sound"=>array(
        "payload_on"=>"detected",
        "payload_off"=>"not detected"),
    "tamper"=>array(
        "payload_on"=>"detected",
        "payload_off"=>"clear"),
    "water"=>array(
        "payload_on"=>"wet",
        "payload_off"=>"dry"),
    "windowShade"=>array(
        "payload_on"=>"open",
        "payload_off"=>"closed")
);
$basicSensors = array(
    "acceleration"=>"",
    "currentActivity"=>"",
    "alarm"=>"",
    "battery"=>array("unit_of_measurement"=>"%"),
    "button"=>"",
    "carbonDioxide"=>"",
    "carbonMonoxide"=>"",
    "coolingSetpoint"=>"",
    "consumableStatus"=>"",
    "energy"=>array("unit_of_measurement"=>"kWh"),
    "eta"=>"",
    "heatingSetpoint"=>"",
    "humidity"=>array("unit_of_measurement"=>"%"),
    "illuminance"=>"",
    "lqi"=>"",
    "pH"=>"",
    "power"=>array("unit_of_measurement"=>"W"),
    "powerSource"=>"",
    "rssi"=>"",
    "sessionStatus"=>"",
    "soundPressureLevel"=>"",
    "status"=>"",
    "steps"=>"",
    "temperature"=>array("unit_of_measurement"=>"°F"),
    "thermostatFanMode"=>"",
    "thermostatMode"=>"",
    "thermostatOperatingState"=>"",
    "thermostatSetpoint"=>"",
    "touch"=>"",
    "trackData"=>"",
    "trackDescription"=>"",
    "ultravioletIndex"=>"",
    "voltage"=>""
);
foreach ($json as $each => $properties){
    $item = array();
    $item['platform'] = "mqtt";
    $item['name'] = $each;
    $item['retain'] = true;
    //MQTT Fan
    if(strpos($each,"Fan") !== false){
        $item['name'] = $item['name'];
        $item['state_topic'] = "smartthings/$each/switch";
        $item['command_topic'] = "smartthings/$each/level";
        $item['speed_state_topic'] = "smartthings/$each/level";
        $item['speed_command_topic'] = "smartthings/$each/level";
        $item['payload_on'] = "on";
        $item['payload_off'] = "off";
        $item['payload_low_speed'] = 33;
        $item['payload_medium_speed'] = 67;
        $item['payload_high_speed'] = 99;
    }
    //MQTT Light
    if (isset($properties->switch)){
        $item['name'] = "Switch - ".$item['name'];
        //is a dimmable light switch
        if (isset($properties->level)) {
            $item['name'] = "Dimmer - ".$item['name'];
            $item['brightness_state_topic'] = "smartthings/$each/level";
            $item['brightness_command_topic'] = "smartthings/$each/level";
            $item['brightness_scale'] = "100";
            $item['payload_on'] = "on";
            $item['payload_off'] = "off";
        }
        if (isset($properties->color)) {
            $item['name'] = "Color Light - ".$item['name'];
            $item['rgb_state_topic'] = "smartthings/$each/color";
            $item['rgb_command_topic'] = "smartthings/$each/color";
        }
        if (isset($properties->hue)) {
            $item['color_temp_state_topic'] = "smartthings/$each/hue";
            $item['color_temp_command_topic'] = "smartthings/$each/hue";
        }
        $item['state_topic'] = "smartthings/$each/switch";
        $item['command_topic'] = "smartthings/$each/switch";
        $item['payload_on'] = "on";
        $item['payload_off'] = "off";
        if (strpos($each,"Fan") !== false){
            $all['fan']['fans.yaml'][] = $item;
        } else if (!isset($properties->level) && !isset($properties->color) && !isset($properties->hue)){
            $all['switch']['switches.yaml'][] = $item;
        } else {
            $all['light']['lights.yaml'][] = $item;
        }
    }
    //MQTT Lock
    if (isset($properties->lock)){
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/lock";
        $sensor['command_topic'] = "smartthings/$each/lock";
        $sensor['payload_lock'] = "locked";
        $sensor['payload_unlock'] = "unlocked";
        $all['lock']["locks.yaml"][] = $sensor;
    }

    //MQTT Garage Door
    if (isset($properties->door) && isset($properties->contact)){
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = "Cover - ".$each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/contact";
        $sensor['command_topic'] = "smartthings/$each/door";
        $sensor['optimistic'] = "true";
        $sensor['state_open'] = "open";
        $sensor['state_closed'] = "closed";
        $sensor['service_open'] = "open";
        $sensor['service_close'] = "close";
        $all['cover']["garageDoor.yaml"][] = $sensor;
    }
    //MQTT Sensor
    foreach ($basicSensors as $sensorName => $sensorProperties){
        if (isset($properties->$sensorName)){
            $sensor = array();
            $sensor['platform'] = "mqtt";
            $sensor['name'] = ucfirst($sensorName)." - ".$each;
            $sensor['retain'] = true;
            $sensor['state_topic'] = "smartthings/$each/$sensorName";
            if (is_array($sensorProperties)) {
                foreach ($sensorProperties as $sensorProperty => $sensorValue) {
                    $sensor[$sensorProperty] = $sensorValue;
                }
            }
            $all['sensor']["$sensorName.yaml"][] = $sensor;
        }
    }
    //MQTT Binary Sensor
    foreach ($binarySensors as $sensorName => $sensorProperties){
        if (isset($properties->$sensorName)){
            $sensor = array();
            $sensor['platform'] = "mqtt";
            $sensor['name'] = ucfirst($sensorName)." - ".$each;
            $sensor['retain'] = true;
            $sensor['state_topic'] = "smartthings/$each/$sensorName";
            foreach ($sensorProperties as $sensorProperty => $sensorValue){
                $sensor[$sensorProperty] = $sensorValue;
            }
            $all['binary_sensor']["$sensorName.yaml"][] = $sensor;
        }
    }
}
foreach ($all as $type => $files){
    $i = 0;
    foreach ($files as $filename => $filecontents){
        $configurationTitle = $i == 0 ? $type : "$type $i";
        $configuration[$configurationTitle] = "!include devices/$filename";
        if (!file_exists(__DIR__."/$directory/devices")) {
            mkdir(__DIR__."/$directory");
            mkdir(__DIR__."/$directory/devices");
        }
        if (file_exists(__DIR__."/$directory/devices/".$filename)) {
          $file = file_get_contents(__DIR__."/$directory/devices/".$filename);
          $file = preg_replace("/######hassSTAutoConfig Start.*######hassSTAutoConfig End/s","",$file);
        } else {
          $file = '';
        }
        $file .= "######hassSTAutoConfig Start\n".str_replace("---\n","##You can add your own custom stuff above hassSTAutoConfig Start\n",str_replace("...\n","",yaml_emit($filecontents)))."######hassSTAutoConfig End";
        file_put_contents(__DIR__."/$directory/devices/".$filename,$file);
        $i++;
    }
}
if (file_exists(__DIR__."/$directory/configuration.yaml")) {
  $file = file_get_contents(__DIR__."/$directory/configuration.yaml");
  $file = preg_replace("/######hassSTAutoConfig Start.*######hassSTAutoConfig End/s","",$file);
} else {
  $file = '';
}
$file .= "######hassSTAutoConfig Start\n".preg_replace("/\'\!include (.*?)\.yaml\'/",'!include ${1}.yaml',str_replace("---\n","##You can add your own custom stuff above hassSTAutoConfig Start\n",str_replace("...\n","",yaml_emit($configuration))))."######hassSTAutoConfig End";
file_put_contents(__DIR__."/$directory/configuration.yaml",$file);
