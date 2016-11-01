<?php
$contents = file_get_contents("devices.txt");
$json = json_decode($contents);
print_r($json);

foreach ($json as $each => $properties){
    $item = array();
    $item['platform'] = "mqtt";
    $item['name'] = $each;
    $item['retain'] = true;
    //is a fan
    if(strpos($each,"Fan") !== false){
        $item['state_topic'] = "smartthings/$each/switch";
        $item['command_topic'] = "smartthings/$each/level";
        $item['payload_on'] = "on";
        $item['payload_off'] = "off";
        $item['payload_low_speed'] = 33;
        $item['payload_medium_speed'] = 67;
        $item['payload_high_speed'] = 99;
    }
    //is a light switch
    if (isset($properties->switch)){
        //is a dimmable light switch
        if (isset($properties->level)) {
            $item['brightness_state_topic'] = "smartthings/$each/level";
            $item['brightness_command_topic'] = "smartthings/$each/level";
            $item['brightness_scale'] = "100";
            $item['payload_on'] = "on";
            $item['payload_off'] = "off";
        }
        if (isset($properties->color)) {
            $item['rgb_state_topic'] = "smartthings/$each/color";
            $item['rgb_command_topic'] = "smartthings/$each/color";
        }
        if (isset($properties->hue)) {
            $item['color_temp_state_topic'] = "smartthings/$each/hue";
            $item['color_temp_command_topic'] = "smartthings/$each/hue";
        }

        $item['state_topic'] = "smartthings/$each/switch";
        $item['command_topic'] = "smartthings/$each/switch";
        $all['lights']['lights.yaml'][] = $item;
    }
    if (isset($properties->temperature)) {
        $temp = array();
        $temp['platform'] = "mqtt";
        $temp['name'] = $each;
        $temp['retain'] = true;
        $temp['state_topic'] = "smartthings/$each/temperature";
        $temp['unit_of_measurement'] = '°F';
        $all['sensor']['tempAndHumidity.yaml'][] = $temp;
    }
    if (isset($properties->humidity)) {
        $temp = array();
        $temp['platform'] = "mqtt";
        $temp['name'] = $each;
        $temp['retain'] = true;
        $temp['state_topic'] = "smartthings/$each/humidity";
        $temp['unit_of_measurement'] = '%';
        $all['sensor']['tempAndHumidity.yaml'][] = $temp;
    }
    if (isset($properties->battery)) {
        $batt = array();
        $batt['platform'] = "mqtt";
        $batt['name'] = $each;
        $batt['retain'] = true;
        $batt['state_topic'] = "smartthings/$each/battery";
        $batt['unit_of_measurement'] = "%";
        $all['sensor']['batteries.yaml'][] = $batt;
    }
    if (isset($properties->power)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/power";
        $sensor['unit_of_measurement'] = "W";
        $all['sensor']['power.yaml'][] = $sensor;
    }
    if (isset($properties->energy)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/energy";
        $sensor['unit_of_measurement'] = "kWh";
        $all['sensor']['energy.yaml'][] = $sensor;
    }
    if (isset($properties->presence)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/presence";
        $sensor['payload_on'] = "present";
        $sensor['payload_off'] = "not present";
        $all['sensor']['presence.yaml'][] = $sensor;
    }
    if (isset($properties->sessionStatus)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/sessionStatus";
        $all['sensor']['appliances.yaml'][] = $sensor;
    }
    if (isset($properties->timeRemaining)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/timeRemaining";
        $all['sensor']['appliances.yaml'][] = $sensor;
    }
    if (isset($properties->acceleration)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/acceleration";
        $all['sensor']['acceleration.yaml'][] = $sensor;
    }
    if (isset($properties->button)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/button";
        $all['sensor']['button.yaml'][] = $sensor;
    }
    if (isset($properties->consumable)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/consumable";
        $all['sensor']['consumable.yaml'][] = $sensor;
    }
    if (isset($properties->carbonDioxide)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/carbonDioxide";
        $all['sensor']['carbonDioxide.yaml'][] = $sensor;
    }
    if (isset($properties->contact)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/contact";
        $all['sensor']['contact.yaml'][] = $sensor;
    }
    if (isset($properties->door)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/door";
        $all['sensor']['door.yaml'][] = $sensor;
    }
    if (isset($properties->illuminance)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/illuminance";
        $all['sensor']['illuminance.yaml'][] = $sensor;
    }
    if (isset($properties->lock)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/lock";
        $sensor['command_topic'] = "smartthings/$each/lock";
        $sensor['payload_lock'] = "locked";
        $sensor['payload_unlock'] = "unlocked";
        $all['lock']['locks.yaml'][] = $sensor;
    }
    if (isset($properties->motion)) {
        $sensor = array();
        $sensor['platform'] = "mqtt";
        $sensor['name'] = $each;
        $sensor['retain'] = true;
        $sensor['state_topic'] = "smartthings/$each/motion";
        $sensor['payload_on'] = "active";
        $sensor['payload_off'] = "inactive";
        $all['sensor']['illuminance.yaml'][] = $sensor;
    }

}

$directory = "test_config";

foreach ($all as $type => $files){
    $i = 0;
    foreach ($files as $filename => $filecontents){
        $configurationTitle = $i == 0 ? $type : "$type $i";
        $configuration[$configurationTitle] = "!include $filename";
        if (!file_exists(__DIR__."/$directory")) {
            mkdir(__DIR__."/$directory");
        }
        if (file_exists(__DIR__."/$directory/".$filename)) {
          $file = file_get_contents(__DIR__."/$directory/".$filename);
          $file = preg_replace("/\/\/AutoConfig Start.*\/\/AutoConfig End/s","",$file);
        } else {
          $file = '';
        }
        $file .= "//AutoConfig Start\n".yaml_emit($filecontents)."//AutoConfig End";
        file_put_contents(__DIR__."/$directory/".$filename,$file);
        $i++;
    }
}
if (file_exists(__DIR__."/$directory/configuration.yaml")) {
  $file = file_get_contents(__DIR__."/$directory/configuration.yaml");
  $file = preg_replace("/\/\/AutoConfig Start.*\/\/AutoConfig End/s","",$file);
} else {
  $file = '';
}
$file .= "//AutoConfig Start\n".preg_replace("/\'\!include (.*?)\.yaml\'/",'!include ${1}.yaml',yaml_emit($configuration))."//AutoConfig End";
file_put_contents(__DIR__."/$directory/configuration.yaml",$file);