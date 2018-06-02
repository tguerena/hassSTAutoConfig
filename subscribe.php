<?php
$host = '192.168.0.169';
$port = 1883;
$mosquitto = '/volume1/@appstore/mosquitto/bin/mosquitto_sub';

foreach ($argv as $arg){
    if (preg_match('/--host=(.*)/',$arg,$match)){
        $host = trim($match[1]);
    }
    if (preg_match('/--mosquitto_sub=(.*)/',$arg,$match)){
        $mosquitto = trim($match[1]);
    }
    if (preg_match('/--port=(.*)/',$arg,$match)){
        $port = trim($match[1]);
    }
    if (preg_match('/--help/',$arg,$match)){
        echo "Usage: php subscribe.php --host=192.168.0.169 --mosquitto_sub=/volume1/@appstore/mosquitto/bin/mosquitto_sub --port=1883\n";
        exit;
    }
}

//Test if Mosquitto is working
$mosqittoWorking = `$mosquitto &>/dev/null; echo $?`;
if ((int)$mosqittoWorking !== 1){
    echo "cannot find mosqitto_sub at $mosquitto\n";
}

//Test if I can connect to MQTT
$connection = @fsockopen($host, $port);
if (!is_resource($connection)) {
    echo "I can NOT connect to MQTT port $host:$port";
}

$cmd = "$mosquitto -h $host -t 'smartthings/#' -v";
//print_r($cmd);
$descriptorspec = array(
    0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
    1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
    2 => array("pipe", "w")    // stderr is a pipe that the child will write to
);
flush();
$process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());

if (is_resource($process)) {
    while ($s = fgets($pipes[1])) {
        if (file_exists(__DIR__."/devices.json")) {
            $contents = file_get_contents(__DIR__ . "/devices.json");
            $json = json_decode($contents);
        }
        $sep = explode("\n",$s);
        preg_match("/^.*?\/(.*?)\/(.*?) (.*?)$/",$sep[0],$matches);
        $json->$matches[1]->$matches[2] = date("Y-m-d H:i:s");
        file_put_contents(__DIR__."/devices.json",json_encode($json, JSON_PRETTY_PRINT));
        echo "Topic: {$matches[1]}\nValue: {$matches[2]}\n\n";
        flush();
    }

}
