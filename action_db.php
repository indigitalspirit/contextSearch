<?php
include_once('db.php');
include_once('utils.php');
include_once('xml_parser.php');

ini_set('log_errors', 'On');
ini_set('error_log', '/var/www/html/log');


$query = trim($_POST["query"]);//'лягушка';
$context = trim($_POST["context"]);

if((strlen($query) == null) || (strlen($context) == null)) 
{
   logger("ACTION ", "empty context or query" . "\n");
   show_error("Empty context or query");
}
else
{
	/* Поиск в базе данных, вывод результатов */
	$db_conn = db_connect();

	$query_words = explode(" ", trim($_POST["query"]));

	/* Поиск в базе данных, вывод результатов */
	$db_places["places"] = get_places_by_weight($db_conn, $context, $query_words);

	echo json_encode($db_places);
}

?> 
