<?php
include_once('db.php');
include_once('utils.php');
include_once('xml_parser.php');

ini_set('log_errors', 'On');
ini_set('error_log', '/srv/www/vhosts/web/contextSearch/log');


$query = str_replace(" ", "&", trim($_POST["query"]));
$context = trim($_POST["context"]);
$lat = trim($_POST["latitude"]);
$lon = trim($_POST["longitude"]);

if((strlen($query) == null) || (strlen($context) == null)) 
{
	logger("ACTION ", "empty context or query" . "\n");
    show_error("Empty context or query");
} 

else 

{
$starttime = microtime(true);
	// Поиск в wikimapia 
	$url = construct_url($query, $context, $lat, $lon);
	$wikimapiatime = microtime(true);

	$wikimapia_content = array();
	
	$wikimapia_content = file_get_contents($url);

	if(!$wikimapia_content) 
	{
		logger("ACTION ", "Empty Wikimapia content" . "\n");
   
    	show_error("Empty Wikimapia content");
	} 

	// Разбор и вывод ответа 
	$wikimapia_parsed["parsed"] = xml_parser($wikimapia_content);
	$endtime = microtime(true);

	global $foundcount;

	$wikimapia_parsed["timers"] = "Start time: ". $starttime . "<br> Wikimapia time: " . $wikimapiatime . " (found " . $foundcount. " objects)<br> End time: ". $endtime;

	echo json_encode($wikimapia_parsed);
}




?> 
