<?php

$shortopts  = "";
$shortopts .= "i:";  // IP Address Required value
$shortopts .= "p::"; // Port Number Optional value
$shortopts .= "h::"; // Help

$longopts  = array(
    "help::",
    "ip::",
    "port::",        // No value
);

$options = getopt($shortopts, $longopts);
if ((isset($options['help']) || isset($options['h'])) || ((!empty($options['i']) && filter_var($options['i'], FILTER_VALIDATE_IP) === false) && (!empty($options['ip']) && filter_var($options['ip'], FILTER_VALIDATE_IP) === false))){
    echo "\nThis tool is used to check the retains stored in MQTT Server. Use clearRetains to clear all retains to start fresh.
    -i IP address of MQTT Server (can also use --ip) [!REQUIRED]
    -p Port number of MQTT Server (can also use --port) [No entry will default to port 8883]
    ";
} else {
    $ip = !empty($options['i']) && !filter_var($options['i'], FILTER_VALIDATE_IP) === false ? $options['i'] : $options['ip'];

    $cmd = "mosquitto_sub -h $ip -t 'smartthings/#' -v";
    $cmd .= !empty($options['p']) ? "-p ".$options['p'] : !empty($options['port']) ? "-p ".$options['port'] : "";

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
            $sep = explode("\n", $s);
            preg_match("/^.*?\/(.*?)\/(.*?) (.*?)$/", $sep[0], $matches);
            `mosquitto_pub -h $ip -t "smartthings/{$matches[1]}/{$matches[2]}" -r -n`;
            echo "Topic: {$matches[1]}\nValue: {$matches[2]} Cleared\n";
            flush();
        }
    }
}