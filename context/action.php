
<?
include_once('db.php');
include_once('utils.php');
include_once('xml_parser.php');


/*****************************************************************************/

$url = construct_url();


echo "constructed url: " . $url . "<br><br>";
$wikimapia = file_get_contents($url);
$wikimapia_parsed = xml_parser($wikimapia);


?> 