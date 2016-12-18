<?php
include_once('db.php');
include_once('utils.php');

ini_set('log_errors', 'On');
ini_set('error_log', '/var/www/html/log');

/* Разбор ответа от Wikimapia, поиск в yandex */
function xml_parser($xml_str) {
 
  $titles = array();
  $urlhtmls = array();
  $results_array = array();
  $url_array = array();

  $results = new SimpleXMLElement($xml_str);

  if(!$results) {
    logger("XML PARSER ERROR ", "empty WM results" . "\n");
    show_error("Empty Wikimapia results");
  }


  /* разбор ответа 
   * формат ответа wm
   *
   * <wm>
   * <debug>
   * <code></code>
   * <message></message>
   * </debug>
   * </wm>
   *
   */

  $error_code = trim($results->debug->code);
  $error_message = trim($results->debug->message);

  if($error_code && $error_message) {
    logger("XML PARSER ERROR ", "\terror_code - " . $error_code . "\terror_message - " . $error_message . "\n");
    show_error("Error in Wikimapia query: " . "\terror_code - " . $error_code . "\terror_message - " . $error_message);
  }

  /* count - кол-во результатов поиска в wikimapia */
  $count = $results->found;
  logger("XML_PARSER count ", $count . "\n"); 

  $db_conn = db_init();
  logger("DB ", "connection init" . "\n");

  global $foundcount;
  $foundcount = $count;

  $db_conn = db_init();
  logger("DB ", "connection init" . "\n");

  /* подготовка контекста, стемминг */
  $context = str_replace(" ", "&", trim($_POST["context"]));
  logger("XML_PARSE context", $context . "\n");

  $context_info = prepare_context($db_conn, $context);
  
  /* разбор результатов с wikimapia, поиск в yandex */

  $i=0;
  while($i < $count) {  
    $point = array();
    $point_tags = array();
    $tag_ids = array();

    $yandex_search_url_count = 0;
    $yandex_search_url_sum = 0;

    $place = "places_". $i; 
    $point["title"] = $results->places->$place->title;
    $point["title"] = str_replace("'", "", $point["title"]);

    if($point["title"] != null) {
      $point["url"] = $results->places->$place->urlhtml;
      $point["url"] = str_replace("'", "", $point["url"]);
      $point["description"] = $results->places->$place->description;

      if(!$point["description"])
        $point["description"] = "empty";

      $point["lon"] = $results->places->$place->location->lon;
      $point["lat"] = $results->places->$place->location->lat;
     
      $raw_tags = $results->places->$place->tags->tags_0->title;

      /* подготовка тегов - парсинг, стемминг */
      $tags = prepare_tags($raw_tags);
      
      /* запись объектов в бд */
      $point_id = add_place($db_conn, $point);

      /* поиск в yandex - запрос + контекст */
       $search_result = search_in_yandex(str_replace(" ", "&", trim($_POST["query"])), $context_info["info"]);

      $yandex_search_url_sum += $search_result["count"];
      $url_array[] = $search_result["urls"];


       /* поиск в yandex по тэгам и контексту */
      foreach ($tags as $tag) {

        /* Запись тега в БД */
        $tag_id = add_tag($db_conn, trim($tag));

        /* поиск по тегам, не связанным с существующим контестом ( в БД) */
        if(!check_tag_mapping_to_context($db_conn, $tag_id, $context_info["id"])) 

        {
            $search_result = search_in_yandex(trim($tag), $context_info["info"]);
            $yandex_search_url_sum += $search_result["count"];
            $url_array[] = $search_result["urls"];

            /* связь тег-место */
            bind_tag_to_place($db_conn, $tag_id, $point_id);  
           
            if ($url_count == null) {
              $url_count = 1;
            }
            /* связь тег- контекст */
            bind_tag_to_context($db_conn, $tag_id, $context_info["id"], $url_count);
        }
      }
      /* запись в бд "веса" объекта */
      add_sum_to_place($db_conn, $point_id, $yandex_search_url_sum);
      
    }
    else 
    {
      $i = $count;
    }
    
    $i++;

  }

  $query_words = explode(" ", trim($_POST["query"]));
  
  /* поиск результатов в БД, вывод по-возрастанию */
  $global_result = array("places" => get_places_by_weight($db_conn, $context_info["info"], $query_words), "urls" => $url_array);

  return $global_result;
  
}

/************************************************************************************************************/


?>
