<?php
ini_set('display_errors', 'On');
ini_set('html_errors', 0);

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
    $time_start = microtime(true);
    while ($s = fgets($pipes[1])) {
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        if ($time >= 1) {
            exit;
        }

        echo $s;
    }
}


print_r($options);