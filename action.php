<?php
include_once('db.php');
include_once('utils.php');
include_once('xml_parser.php');


$query = trim($_POST["query"]);
$context = trim($_POST["context"]);

if((strlen($query) == null) || (strlen($context) == null)) 
{
	$msg["empty"] = 1;
    echo json_encode($msg);
} 

else 

{
	/* Поиск в wikimapia */
	$url = construct_url($query, $context);

	$wikimapia_parsed = array();
	$wikimapia_parsed["wm_url"] = "constructed url: " . $url . "<br><br>";

	$wikimapia = file_get_contents($url);

	/* Разбор и вывод ответа */
	$wikimapia_parsed["parsed"] = xml_parser($wikimapia);

	echo json_encode($wikimapia_parsed);
}


?> 
