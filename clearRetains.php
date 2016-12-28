<?php

$ip = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $argv);

$cmd = "mosquitto_sub -h $ip -t 'smartthings/#' -v";

$descriptorspec = array(
    0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
    1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
    2 => array("pipe", "w")    // stderr is a pipe that the child will write to
);
flush();
$process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());

//echo "<pre>";
if (is_resource($process)) {
    while ($s = fgets($pipes[1])) {
        $sep = explode("\n",$s);
        preg_match("/^.*?\/(.*?)\/(.*?) (.*?)$/",$sep[0],$matches);
        `mosquitto_pub -h $ip -t "smartthings/{$matches[1]}/{$matches[2]}" -r -n`;
        echo "Topic: {$matches[1]}\nValue: {$matches[2]} Cleared\n";
        flush();
    }
}