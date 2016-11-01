<?php
$cmd = "mosquitto_sub -h 192.168.0.111 -t 'smartthings/#' -v";

$descriptorspec = array(
    0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
    1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
    2 => array("pipe", "w")    // stderr is a pipe that the child will write to
);
flush();
$process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());

if (is_resource($process)) {
    while ($s = fgets($pipes[1])) {
        if (file_exists(__DIR__."/devices.txt")) {
            $contents = file_get_contents(__DIR__ . "/devices.txt");
            $json = json_decode($contents);
        }
        $sep = explode("\n",$s);
        preg_match("/^.*?\/(.*?)\/(.*?) (.*?)$/",$sep[0],$matches);
        $json->$matches[1]->$matches[2]->$matches[3] = date("Y-m-d H:i:s");
        file_put_contents(__DIR__."/devices.txt",json_encode($json, JSON_PRETTY_PRINT));
//        echo "Topic: {$matches[1]}\nValue: {$matches[2]}\n\n";
        flush();
    }
}
