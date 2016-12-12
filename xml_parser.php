<?php
include_once('db.php');
include_once('utils.php');


/* Разбор ответа от Wikimapia, поиск в yandex */
function xml_parser($xml_str) {
 
  $results = new SimpleXMLElement($xml_str);

  /* count - кол-во результатов поиска в wikimapia */
  $count = $results->found;

  if(!$count) {
    $none = "<p>ничего не найдено</p>";
    return $none;
  }

  $titles = array();
  $urlhtmls = array();
  $results_array = array();
  $url_array = array();

  $db_conn = db_init();

  /* подготовка контекста, стемминг */
  $context = str_replace(" ", "&", trim($_POST["context"]));

  if ($context == null) {
    echo "<p>context empty: </p>";
  }
  else 
  {
    $context_info = prepair_context($db_conn, $context);
  }
  
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
      $tags = prepair_tags($raw_tags);
      
      /* запись объектов в бд */
      $point_id = add_place($db_conn, $point);

      if ($point_id == null) {
        echo "null point id in add_place";
        die();
      } 
      
      /* поиск в yandex - запрос + контекст */
      $search_result = search_in_yandex(trim($_POST["query"]), $context_info["info"]);

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

            if ($tag_id == null) {
              echo "null tag id in bind context to tag ";
              die();
            }
           
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
  
  /* поиск результатов в БД, вывод по-возрастанию */
  $global_result = array("places" => get_places_by_weight($db_conn, $context_info["info"]), "urls" => $url_array);

  return $global_result;
  
}

/************************************************************************************************************/


?>
