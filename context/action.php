
<?
include_once('db.php');
include_once('utils.php');
include_once('xml_parser.php');


/*****************************************************************************/

$url = construct_url();
$response = file_get_contents($url); 
//echo $response;
$xml = new SimpleXMLElement($response);
$result = xml_parser($xml);
//echo $result;
show_result($result);

//$array = json_decode($response, true);
//$places = $array["places"][0];
//print($places["title"]);
?> 