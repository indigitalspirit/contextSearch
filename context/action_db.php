
<?
include_once('db.php');
include_once('utils.php');
include_once('xml_parser.php');


/*****************************************************************************/

//$url = construct_url();

//echo "constructed url: " . $url;
//$wikimapia = file_get_contents($url);
//function send_result($result) {

//}


$db_conn = db_connect();

$context = str_replace(" ", "&", trim($_POST["context"]));

$wikimapia_parsed = get_places_by_weight($db_conn, $context);
//if (!$wikimapia_parsed) {
//	$result = "Ничего не найдено";
	# code...
//}

/*
$max = -1;

foreach ($wikimapia_parsed as $element ) {
		//echo "element sum: " . $element["yandex_sum"];
	 if($element["yandex_sum"] > $max) {
	 	$max = $element->yandex_sum;
	 	$final_sorted[] = $element;
	 }

}
*/

// здесь нужен вывод из БД
/*
echo "wikimapia_parsed and sorted: ";
	foreach($final_sorted as $sorted_point) {

		echo $sorted_point["title"] . "  " . $sorted_point["yandex_sum"] . "<br>";     //var_dump($wikimapia_parsed);
	}

echo "&nbsp";
*/



//$url = 'https://www.google.ru/?q=лягушка';

/*
$url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=".urlencode('лягушка');
$body = file_get_contents($url);
$json = json_decode($body);
*/

//foreach ($json->responseData->results as $resultjson) {
//$result_google['urls']= $resultjson->url;
//$result_google['contents'] = $resultjson->content;
//}

//_______________________

// The request also includes the userip parameter which provides the end
// user's IP address. Doing so will help distinguish this legitimate
// server-side traffic from traffic which doesn't come from an end-user.
 function curl_file_get_contents($fp_url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fp_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $fp = curl_exec($ch);
        curl_close($ch);
        return $fp;
    }

/*
$keyword = trim($_POST["query"]);

echo "keyword: " . $keyword;

//$keywords = explode(",",$keywords);
$res = Array();


  //  $params = array(
        $user = 'nastya-pavlova-93'; // логин
        $key = '03.180579375:33ebf55a7d6898980d6426e16a4eaf72'; // ключ Яндекс.XML
        //'filter' => 'none',
        //'groupby' => 'attr=d.mode=deep.groups-on-page=100', // attr=<служебный атрибут>.mode=<тип группировки>.groups-on-page=<количество групп на одной странице>.docs-in-group=<количество документов в каждой группе>
        //$query = trim($_POST["query"]);// => urlencode($keyword)
        $context = str_replace(" ", "&", trim($_POST["context"]));
        //echo $context;
        //'lr' => 6
    //);
    $result_ya_1 = array();
    $result_ya_2 = array();

    //$url_s = "http://82.196.66.12:12173";
    //$fp = curl_file_get_contents($url_s); // для CURL
    //var_dump($fp);
    
   // for($i=0; $i<50; $i++) {    

    	  if (!$keyword) {
	    	echo "null query";
	    	die();
	    }

	    $url_1 = "https://yandex.com/search/xml?l10n=en&user=nastya-pavlova-93&key=03.180579375:33ebf55a7d6898980d6426e16a4eaf72&page=1&query=" . $keyword; //. $query;

	    //echo "</n>" . "url: " . $url_1;
	   // $url = "https://yandex.ru/search/xml?l10n=en"  . urldecode(http_build_query($params));
	     $url_2 = "https://yandex.com/search/xml?l10n=en&user=nastya-pavlova-93&key=03.180579375:33ebf55a7d6898980d6426e16a4eaf72&page=" . $i . "&query=город"; //. $context;
	    $fp_1 = file_get_contents($url_1);

	    if (!$fp_1) {
	    	echo "null catched";
	    	die();
	    }

	   // echo "result: " . $fp_1;

	    //curl_file_get_contents($url_1); // для CURL
	   

	    $results_1 = new SimpleXMLElement($fp_1);
	    $human = "found-human";
	    $count = $results_1->response->$human;//->grouping->group

	    $count = str_replace("answers found", "", $count); 
	    $count = trim($count);
	    //echo "count: " . $count;

	    $count_res = array();
	    $count_res = explode(" ", $count);
	    if ($count_res[1] == "mln.") {
	    	
	    	$count = $count_res[0] . " 000 000";
	    } 
	    elseif($count_res[1] == "thsd.") {
	    	$count = $count_res[0] . " 000";
	    }

	    echo "<p>count: " . $count . "</p>";

	    foreach ($results_1->response->results->grouping->group as $result_group) {
			$result_ya_1[] .= $result_group->doc->url[0];
			//$result_google['contents'] = $resultjson->content;
		}
		*/

		//$fp_2 = curl_file_get_contents($url_2); // для CURL
	    //$results_2 = new SimpleXMLElement($fp_2);

	    //foreach ($results_2->response->results->grouping->group as $result_group) {
		//	$result_ya_2[] .= $result_group->doc->url[0];
			//$result_google['contents'] = $resultjson->content;
	//	}
	//}

	//echo  "Результат запроса:" .  var_dump($result_ya_1);//$fp_1;//. //. $fp_1;//$count;array_intersect($result_ya_1, $result_ya_2);
    //var_dump($results->response->results->grouping->group[1]->doc->url[0]);
/*
$result_google = array();
//for($i=0; $i<20; $i=$i+4) {

	//$url = "https://ajax.googleapis.com/ajax/services/search/web?v=1.0&"
	  //  . "q=hi&start=" . $i;
$key = "AIzaSyD1df3YjVA7qwCdDymXfl9HN2D-wjZj0gQ";
$search_query = trim($_POST["query"]);
$url = "https://www.googleapis.com/customsearch/v1?key=" . $key . "&cx=010132313326107130925:am1kkkaxuxu" . "&q=" . $search_query;
//$google_search = file_get_contents($amazon_url);
//var_dump($google_search);

	//$url = " https://www.googleapis.com/customsearch/v1?q=hi";
	// sendRequest
	// note how referer is set manually
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	*/
	//curl_setopt($ch, CURLOPT_REFERER, /* Enter the URL of your site here */);
	//$body = curl_exec($ch);
	//curl_close($ch);

	//var_dump($body);

	// now, process the JSON string
	//$json = json_decode($body);
	// now have some fun with the results...
	//var_dump($json->items);
	
	/*
	 foreach ($json->items as $result_json_item) {
		$result_google[] .= $result_json_item->link;
		//$result_google['contents'] = $resultjson->content;
	}
	*/



//}



//var_dump($result_google);

//$response = file_get_contents($url); 
//echo $response;

/*
$xml = new SimpleXMLElement($response);
$result = xml_parser($xml);


$title = trim($_POST["query"]);
$db_conn = db_connect();
$db_result = get_places_with_title($db_conn, $title);
 

    while( $row = mysqli_fetch_assoc($db_result) ) {
      //$result .= "<p>" . $row['place_id'] . "</p>";
      $result .= "<p>Название:&nbsp&nbsp&nbsp" . $row['title'] . "</p>";
      $result .= "<p>Описание:&nbsp&nbsp&nbsp" . $row['description'] . "</p>";
      $result .= "<p>Ссылка:&nbsp&nbsp&nbsp" . $row['url'] . "</p>";
      $result .= "<p>Координаты:&nbsp&nbsp&nbsp" . $row['lat'];
      $result .= "&nbsp" . $row['lon'] . "</p>";
      $result .= "<p><hr></p>";
    }
//echo $result;
show_result($result);

*/
?> 