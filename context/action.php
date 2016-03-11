
<?

        //echo "Ваш запрос: " . $_POST["query"] . "<br/>";
      

$url = 'http://api.wikimapia.org/?';
$key = '030D67E2-DA816AC1-F0EBA91A-9AAF6AE2-CCE73D60-D49546B1-C08E9AF2-FA1FA000';
$lat = '61.78491';
$lon = '34.34691';
$query = trim($_POST["query"]);//'лягушка';
$format = 'xml';

$url .= 'key=' . $key;
$url .= '&function=place.search';
$url .= '&q=' . $query;
$url .= '&lat=' . $lat;
$url .= '&lon=' . $lon;
$url .= '&format=' . $format;
$url .= '&language=en&page=1&count=50';
//echo $url . "<br>"; 

$url_example = 'http://api.wikimapia.org/?key=030D67E2-DA816AC1-F0EBA91A-9AAF6AE2-CCE73D60-D49546B1-C08E9AF2-FA1FA000&function=place.search&q=%D0%BB%D1%8F%D0%B3%D1%83%D1%88%D0%BA%D0%B0&lat=61.78491&lon=34.34691&format=xml&language=en&page=1&count=50';
/*
$result = file_get_contents($url); 
$response = json_decode($result, true);
var_dump($response);
*/


/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_HEADER, 0); // читать заголовок
//curl_setopt($ch, CURLOPT_NOBODY, 1); // читать ТОЛЬКО заголовок без тела
$result = curl_exec($ch);  
curl_close($ch);
echo $result;
//var_dump(json_decode($result, true));
*/


$result = file_get_contents($url); 
/*
$xmlDoc = new DOMDocument();
$xmlDoc->load($result);

$x = $xmlDoc->getElementsByTagName("id")->item(0)->nodeValue;
echo $x;

*/

/*
$doc = new DOMDocument();
  $doc->load($result);
  
  $places = $doc->getElementsByTagName( "places" );
  //echo "places " . $places;

  foreach( $places as $place )
  {
  $id = $place->getElementsByTagName("id")->nodeValue;
  echo "id " . $id;
  }
*/



//echo $result;

$xml = new SimpleXMLElement($result);
$count = $xml->found;
$i=0;

while($i < $count) {	
$place = "places_". $i; 
echo $xml->places->$place->title;
echo "<br>"; 
echo $xml->places->$place->urlhtml;
echo "<br>";

if($xml->places->$place->title == null) {
	$i = $count;
} 

$i++;

}
//$movies->movie->{'great-lines'}->line;
/*
foreach ($xml as $el)
{
    //echo $el->language."<br />";
   
    //foreach ($el->places as $place)
    //{
       // echo "places: ".$el->places."<br />";
        
    //}
    
}
*/

//echo $items->places;

//http://api.wikimapia.org/?key=030D67E2-DA816AC1-F0EBA91A-9AAF6AE2-CCE73D60-D49546B1-C08E9AF2-FA1FA000&function=place.search&query=лягушка&lat=61.78491&lon=34.34691&format=xml&pack=&language=en&page=1&count=50&category=&categories_or=&categories_and=&distance=

//http://api.wikimapia.org/?key=example&function=place.search&q=%D0%BB%D1%8F%D0%B3%D1%83%D1%88%D0%BA%D0%B0&lat=61.78491&lon=34.34691&format=&pack=&language=en&page=1&count=50&category=&categories_or=&categories_and=&distance=
?> 