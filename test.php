
<?php 
ini_set('log_errors', 'On');
ini_set('error_log', '/srv/www/vhosts/web/contextSearch/log');


function logger($module, $error) {
    $logfile = fopen('log', 'a');
    fwrite($logfile, date("Y.m.j H:i:s")." Module ".$module.": ".$error."\n");
    fclose($logfile);
}

logger("ACTION ", "empty context or query" . "\n");


?>

