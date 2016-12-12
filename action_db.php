<?php
include_once('db.php');
include_once('utils.php');
include_once('xml_parser.php');


$db_conn = db_connect();

$query = trim($_POST["query"]);//'лягушка';
$context = trim($_POST["context"]);

if((strlen($query) == null) || (strlen($context) == null)) 
{
    $msg["empty"] = 1;
    echo json_encode($msg);
}
else
{
	/* Поиск в базе данных, вывод результатов */
	$wikimapia_parsed = get_places_by_weight($db_conn, $context);

	echo json_encode($wikimapia_parsed);
}

?> 
