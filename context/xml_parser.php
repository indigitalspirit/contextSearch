<?php
include_once('db.php');
include_once('utils.php');

function xml_parser($xml_str) {

 // echo "xml_parser $xml_str: " . $xml_str . "&nbsp";
  $results = new SimpleXMLElement($xml_str);

  $count = $results->found;
  //echo "<p>count (wikimapia): " . $count . "</p>";

  if(!$count) {
    $none = "<p>ничего не найдено</p>";
    return $none;
  }

  $i=0;
  $titles = array();
  $urlhtmls = array();
  $result_html = "";

  $db_conn = db_connect();

  $results_array = array();

  $context = str_replace(" ", "&", trim($_POST["context"]));
  $context_id = add_context($db_conn, $context);
  //$context_id = $db_conn->insert_id;
  //$tag_id = $db_conn->insert_id;

  while($i < $count) {  
    $place = "places_". $i; 

    
    $each_result_array = array();


    $result_html .= "<p>";
    $point = array();
    $point_tags = array();
    //echo $xml_str->places->$place->title;
    $point["title"] = $results->places->$place->title;
    $result_html .= "Название:&nbsp&nbsp&nbsp" . $results->places->$place->title;
    $each_result_array["title"] = $results->places->$place->title;

    if($results->places->$place->title != null) {
      $result_html .= "<br>"; 

      $result_html .= "Ссылка:&nbsp&nbsp&nbsp&nbsp" . $results->places->$place->urlhtml;
      $each_result_array["url"] = $results->places->$place->urlhtml;

      $point["url"] = $results->places->$place->urlhtml;

      $result_html .= "<br>"; 

      $description = "Описание:&nbsp&nbsp&nbsp" . $results->places->$place->description;
      

      if($description === null) {
        $result_html .= "нет описания";
         //$each_result_array["description"] = "";
      }
      else {
        $result_html .= $description;
        $point["description"] = $description;
        //$each_result_array["description"] = $description;
      }

      //$result_html .= $xml_str->places->$place->description;
      $result_html .= "<br>"; 
      $result_html .= "Координаты:&nbsp&nbsp&nbsp" . $results->places->$place->location->lon;
      //$each_result_array["lon"] = $results->places->$place->location->lon;

      $point["lon"] = $results->places->$place->location->lon;
      //$result_html .= "<br>"; 
      $result_html .= "&nbsp" . $results->places->$place->location->lat;
     // $each_result_array["lat"] = $results->places->$place->location->lat;
      $point["lat"] = $results->places->$place->location->lat;
      $result_html .= "<br>Тэги:&nbsp&nbsp&nbsp"; 
      

      
      $tags = $results->places->$place->tags->tags_0->title;
      $tags = trim($tags);
      $tags = explode(",", $tags);
      $tags = str_replace(",", "", $tags);
      $tags = str_replace(" ", "&", $tags);
      

      $yandex_search_url_count = 0;
      $yandex_search_url_sum = 0;


      $point_id = add_place($db_conn, $point);
      //$point_id = $db_conn->insert_id;

      if ($point_id == null) {
        //$point_id = get_place_id($db_conn, $point["title"]);
         echo "null point id in bind point to tag 1";
            die();

      }

      $tag_ids = array();


      foreach ($tags as $tag) {
        //$result_html .= $tag . "\n";
        
        $yandex_search_url_sum += search_in_yandex($tag, $context);
        $tag_id = add_tag($db_conn, $tag);
        //$tag_id = $db_conn->insert_id;
        
    
         /// $point_id = get_place_id($db_conn, $point["title"]);

          if ($point_id == null) {
            echo "null point id in bind point to tag 2";
            die();
          }

          bind_tag_to_place($db_conn, $tag_id, $point_id);  

          $url_count = search_in_yandex($tag, $context);

          if ($tag_id == null) {
            echo "null tag id in bind context to tag ";
            die();
          }

          //echo "url count (in yandex): " . $url_count . "<br>";
           if ($url_count == null) {
            $url_count = 1;
          }

          bind_tag_to_context($db_conn, $tag_id, $context_id, $url_count);
        
      }

      add_sum_to_place($db_conn, $point_id, $yandex_search_url_sum);

   

    }
    else {
      $i = $count;
    }

    $i++;

  }

  $result_html .= "<vr>";
  //$final_result .= "<vr>";

  //return $result_html;//$final_result; //
  get_places_by_weight($db_conn, $context);

  return $results_array;
}

function search_in_yandex($keyword, $context) {

  //$keyword = trim($_POST["query"]);

  //echo "keyword: " . $keyword;//$keywords = explode(",",$keywords);
  $res = Array();
  $user = 'nastya-pavlova-93'; // логин
  $key = '03.180579375:33ebf55a7d6898980d6426e16a4eaf72'; // ключ Яндекс.XML
  //$context = str_replace(" ", "&", trim($_POST["context"]));
  
  $result_ya_1 = array();
  //$result_ya_2 = array();
 

  //if (!$keyword) {
    //  echo "no keyword";
     // die();
  //}

  $url = "https://yandex.com/search/xml?l10n=en&user=nastya-pavlova-93&key=03.180579375:33ebf55a7d6898980d6426e16a4eaf72&page=1&query=" . $keyword . "&" . $context; 

  //echo "yandex url: " . $url . "<br>";

  $searched_content = file_get_contents($url);

  if(!$searched_content) {

      $count = 0;
      echo "no searched content: ";
    }
    else {

 
    $results = new SimpleXMLElement($searched_content);
      $human = "found-human";
      $count = $results->response->$human;

      $count = str_replace("answers found", "", $count); 
      $count = trim($count);
      //echo "count: " . $count;

      $count_res = array();
      $count_res = explode(" ", $count);
      if ($count_res[1] == "mln.") {
        
      $count = $count_res[0] * 1000000;
        //$count = $count_res[0] . " 000 000";
      } 
      elseif($count_res[1] == "thsd.") {
        $count = $count_res[0] * 1000;
        //$count = $count_res[0] . " 000";
      }

    }

    //  echo "<p>count: " . $count . "</p>";

      /*
      foreach ($results->response->results->grouping->group as $result_group) {
      $result_ya_1[] .= $result_group->doc->url[0];
      //$result_google['contents'] = $resultjson->content;
    }
    */

    return $count;

}
?>
