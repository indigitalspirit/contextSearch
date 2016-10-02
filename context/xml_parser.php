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

  $titles = array();
  $urlhtmls = array();
  $results_array = array();

  $db_conn = db_connect();
  
  $context = str_replace(" ", "&", trim($_POST["context"]));
  $context_id = add_context($db_conn, $context);

  $i=0;
  while($i < $count) {  
    $place = "places_". $i; 

    $point = array();
    $point_tags = array();
 
    $point["title"] = $results->places->$place->title;
    $point["title"] = str_replace("'", "", $point["title"]);

    if($point["title"] != null) {
      $point["url"] = $results->places->$place->urlhtml;
      $point["url"] = str_replace("'", "", $point["url"]);

      $point["description"] = $description;

      if(!$point["description"])
        $point["description"] = "empty";

      $point["lon"] = $results->places->$place->location->lon;
      $point["lat"] = $results->places->$place->location->lat;
     
      $tags = $results->places->$place->tags->tags_0->title;
      $tags = trim($tags);
      $tags = explode(",", $tags);
      $tags = str_replace(",", "", $tags);
      $tags = str_replace(" ", "&", $tags);
      
      $tag_ids = array();
      $yandex_search_url_count = 0;
      $yandex_search_url_sum = 0;

      $point_id = add_place($db_conn, $point);
      //$point_id = $db_conn->insert_id;

      if ($point_id == null) {
      //$point_id = get_place_id($db_conn, $point["title"]);
        echo "null point id in bind point to tag 1";
        die();
      }

      foreach ($tags as $tag) {
        $yandex_search_url_sum += search_in_yandex($tag, $context);
        $tag_id = add_tag($db_conn, $tag);
  
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

  //$result_html .= "<vr>";

  get_places_by_weight($db_conn, $context);

  return $results_array;
}







function search_in_yandex($keyword, $context) {

  $res = Array();
  $result_ya_1 = array();

  $user = 'nastya-pavlova-93'; // логин
  $key = '03.180579375:33ebf55a7d6898980d6426e16a4eaf72'; // ключ Яндекс.XML
  $url = "https://yandex.com/search/xml?l10n=en&user=nastya-pavlova-93&key=03.180579375:33ebf55a7d6898980d6426e16a4eaf72&page=1&query=" . $keyword . "&" . $context; 


  $searched_content = curl_get_file_contents($url); //file_get_contents($url);

  if(!$searched_content) {
     // $count = 0;
      echo "no searched content: ";
    }
  else {
    //echo $searched_content . "<br><br>";

    $results = simplexml_load_string($searched_content); 
    
     // $results = new SimpleXMLElement($searched_content, null, true);

      $human = "found-human";
      $count = $results->response->$human;

      $count = str_replace("answers found", "", $count); 
      $count = trim($count);
      //echo "count: " . $count;

      $count_res = array();
      $count_res = explode(" ", $count);

      if ($count_res[1] == "mln.") { 
        $count = $count_res[0] * 1000000; 
      } 
      elseif($count_res[1] == "thsd.") {
        $count = $count_res[0] * 1000;
      }
      
      //print_r($searched_content);



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
