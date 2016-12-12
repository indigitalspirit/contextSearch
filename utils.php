<?php

function show_result($result) {

  echo $result;

}

function get_context() {

    $context = trim($_POST["context"]);
    $context = strip_tags($context); 
    $context = htmlspecialchars($context, ENT_QUOTES);
    $context = stripslashes($context);
    $context = urlencode($context);

}

/* задаем параметры поиска в wikimapia */
function construct_url($query, $context) {
  

  $url = 'http://api.wikimapia.org/?';
  $key = '030D67E2-67AE3D75-1F4A5A13-ECBA5547-927E8E30-7F219FEC-576EAEF1-531252A1';
  $lat = '61.784974';
  $lon = '34.347038';

  /* JSON or XML */
  $format = 'xml';

  $url .= 'key=' . $key;
  $url .= '&function=place.search';
  $url .= '&q=' . $query;
  $url .= '&lat=' . $lat;
  $url .= '&lon=' . $lon;
  $url .= '&format=' . $format;
  $url .= '&language=' . 'ru';
  $url .= '&page=' . '1';
  $url .= '&count=' . '50';
  $url .= '&distance=' . '10000';
  
  return $url;
}



function db_init() {
  return db_connect();
}

/* стемминг контекста, запись в БД */
function prepair_context($db_conn, $context) {

  $context = stem($context, STEM_RUSSIAN_UNICODE);
  $context_id = add_context($db_conn, $context);

  return $context_info = array('id' => $context_id, 'info' => $context);
}

 /* парсинг, стемминг тегов */
function prepair_tags($tags) {
 

  $tags = trim($tags);
  $tags = str_replace("/", ",", $tags);
  $tags = str_replace("(", ",", $tags);
  $tags = str_replace(")", ",", $tags);
  $tags = str_replace(" ", ",", $tags);
  $tags = explode(",", $tags);

  $prepaired_tags = array();

  foreach ($tags as $tag) 
  {
    $prepaired_tags[] = stem(trim($tag), STEM_RUSSIAN_UNICODE);
  }

  return $prepaired_tags;
}

 //поиск в yandex
function search_in_yandex($keyword, $context_info) {

 

  $res = array();
  $result_ya_1 = array();
  $url_array = array();
  $finish_array = array("count" => "", "urls" => "");

  $user = 'nastya-pavlova-93'; // логин
  $key = '03.180579375:33ebf55a7d6898980d6426e16a4eaf72'; // ключ Яндекс.XML
  $url = "https://yandex.com/search/xml?l10n=en&user=nastya-pavlova-93&key=03.180579375:33ebf55a7d6898980d6426e16a4eaf72&page=1&query=" . $keyword . "&" . $context_info; 
  
  $finish_array["urls"] = $url;

  $searched_content = file_get_contents($url);

  $results = simplexml_load_string($searched_content); 
    
  /* разбор ответа */  
  $count = prepair_url_count($results);

  $finish_array["count"] = $count;

  return $finish_array;
}


/* вычисление "веса" объекта */
function prepair_url_count($results) {
  
    $count_res = array();
    $human = "found-human";

    $count = $results->response->$human;
    $count = str_replace("answers found", "", $count); 
    $count = trim($count);
      
    $count_res = explode(" ", $count);

    if ($count_res[1] == "mln.") { 
        $count = $count_res[0] * 1000000; 
    } 
    elseif($count_res[1] == "thsd.") {
        $count = $count_res[0] * 1000;
    }
    return $count;
}

function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
            else return FALSE;
    }




?>
