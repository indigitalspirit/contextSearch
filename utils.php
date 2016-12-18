<?php

function show_result($result) {

  echo $result;

}

function show_error($error_msg) {

  $msg["error"] = 1;
  $msg["msg"] = $error_msg;

  echo json_encode($msg);
    
  die();
}

function logger($module, $error) {
    $logfile = fopen('log', 'a');
    fwrite($logfile, date("Y.m.j H:i:s")." Module ".$module.": ".$error."\n");
    fclose($logfile);
}

function get_context() {

    $context = trim($_POST["context"]);
    $context = strip_tags($context); 
    $context = htmlspecialchars($context, ENT_QUOTES);
    $context = stripslashes($context);
    $context = urlencode($context);

}

/* задаем параметры поиска в wikimapia */
function construct_url($query, $context, $lat, $lon) {
  

  $url = 'http://api.wikimapia.org/?';
  $key = '030D67E2-67AE3D75-1F4A5A13-ECBA5547-927E8E30-7F219FEC-576EAEF1-531252A1';
//  $lat = '61.784974';
//$lat = '59.938630';
//  $lon = '34.347038';
//$lon = '30.314130';

  /* JSON or XML */
  $format = 'xml';

  $url .= 'key=' . $key;
  $url .= '&function=place.search';
  $url .= '&q="' . str_replace(" ", "&", $query);
  $url .= '"&lat=' . $lat;
  $url .= '&lon=' . $lon;
  $url .= '&format=' . $format;
  $url .= '&language=' . 'ru';
  $url .= '&page=' . '1';
  $url .= '&count=' . '50';
  $url .= '&distance=' . '10000';

  logger("UTILS CONSTRUCTED URL ", $url . "\n");
  logger("UTILS CONSTRUCTED URL QUERY", str_replace(" ", "&", $query) . "\n");
  
  return $url;
}



function db_init() {
  return db_connect();
}

/* стемминг контекста, запись в БД */
function prepare_context($db_conn, $context) {

  if(!($context = stem($context, STEM_RUSSIAN_UNICODE))) {
    logger("Prepare_context ", "Stemming error" . "\n");
    show_error("Error: Empty context stemming result");
  }
  else 
  {
  
    logger("PREPARE_CONTEXT stemming_result ", $context . "\n");

    $context_id = add_context($db_conn, $context);

    $context_info = array('id' => $context_id, 'info' => $context);

    logger("PREPARE_CONTEXT context_info", "id -> " . $context_info['id'] . " info -> " . $context_info['info'] . "\n");
  
    return $context_info;
  }
}

 /* парсинг, стемминг тегов */
function prepare_tags($tags) {
 
  $prepared_tags = array();

  $tags = trim($tags);
  $tags = str_replace("/", ",", $tags);
  $tags = str_replace("(", ",", $tags);
  $tags = str_replace(")", ",", $tags);
  $tags = str_replace(" ", ",", $tags);
  $tags = explode(",", $tags);


  foreach ($tags as $tag) 
  {
    if($tag != null) {
      $stemmed_tag = stem(trim($tag), STEM_RUSSIAN_UNICODE);
      $prepared_tags[] = $stemmed_tag;
      logger("PREPARE_TAGS stemming_result ", $stemmed_tag . "\n");
      logger("PREPARE_TAGS tag ", $tag . "\n");  
    }
  }

  return $prepared_tags;
}

 //поиск в yandex
function search_in_yandex($keyword, $context_info) {

 
  $res = array();
  $result_ya_1 = array();
  $url_array = array();
  $final_array = array("count" => "", "url" => "");

  $user = 'nastya-pavlova-93'; // логин
  //$user = 'seekerk';
  $key = '03.180579375:33ebf55a7d6898980d6426e16a4eaf72'; // ключ Яндекс.XML
  //$key = '03.311288933:33a36a310dcd99622e9e7703b347a59d';
  $url = "https://yandex.com/search/xml?l10n=en&user=" . $user . "&key=" . $key . "&page=1&query=" . $keyword . "&" . $context_info; 

  logger("SEARCH IN YANDEX URL", $url . "\n");
  
  $searched_content = file_get_contents($url);

  if(!$searched_content) {
    logger("SEARCH IN YANDEX ", "error in file_get_contents" . "\n");
    show_error("SEARCH_IN_YANDEX: empty result - error in file_get_contents");
  }

  $final_result["count"] = prepare_url_count($searched_content);
  $final_result["url"] = $url;

  return $final_result;
}


/* вычисление "веса" объекта */
function prepare_url_count($content) {
  
     $results = simplexml_load_string($content); 

  if(!$results) {
    logger("PREPARE CONTEXT ERROR ", "empty results" . "\n");
    show_error("PREPARE CONTEXT ERROR: empty results");
  }

  /* разбор ответа */  
  $error = trim($results->response->error);

  if($error) {
      $code = trim($results->response->error->attributes()->code);

      logger("PREPARE CONTEXT ERROR code", $code . "\n");
      logger("PREPARE CONTEXT ERROR", $error . "\n");

      show_error("Error in YANDEX SEARCH: code " . $code . "  error: " . $error);
  }

  $human = "found-human";

  $count_string = trim(str_replace("answers found", "", $results->response->$human)); 
      
  $count_string_words = explode(" ", $count_string);

  if ($count_string_words[1] == "mln.") { 
      $count = $count_string_words[0] * 1000000; 
  } 
  elseif($count_string_words[1] == "thsd.") {
      $count = $count_string_words[0] * 1000;
  }
    
  //$count_info['status'] = 1;
 // $count_info['count'] = $count;

    return $count;//$count_info;
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
