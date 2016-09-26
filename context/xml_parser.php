<?php
include_once('db.php');
include_once('utils.php');

function xml_parser($xml_str) {

 // echo "xml_parser $xml_str: " . $xml_str . "&nbsp";
  $results = new SimpleXMLElement($xml_str);

  $count = $results->found;
  echo "<p>count (wikimapia): " . $count . "</p>";

  if(!$count) {
    $none = "<p>ничего не найдено</p>";
    return $none;
  }

  $i=0;
  $titles = array();
  $urlhtmls = array();
  $result_html = "";

  $db_conn = db_connect();


  while($i < $count) {  
    $place = "places_". $i; 
    $result_html .= "<p>";
    $point = array();
    $point_tags = array();
    //echo $xml_str->places->$place->title;
    $point["title"] = $results->places->$place->title;
    $result_html .= "Название:&nbsp&nbsp&nbsp" . $results->places->$place->title;

    if($results->places->$place->title != null) {
      $result_html .= "<br>"; 

      $result_html .= "Ссылка:&nbsp&nbsp&nbsp&nbsp" . $results->places->$place->urlhtml;
      $point["url"] = $results->places->$place->urlhtml;

      $result_html .= "<br>"; 

      $description = "Описание:&nbsp&nbsp&nbsp" . $results->places->$place->description;
      

      if($description === null) {
        $result_html .= "нет описания";
      }
      else {
        $result_html .= $description;
        $point["description"] = $description;
      }
      //$result_html .= $xml_str->places->$place->description;
      $result_html .= "<br>"; 
      $result_html .= "Координаты:&nbsp&nbsp&nbsp" . $results->places->$place->location->lon;
      $point["lon"] = $results->places->$place->location->lon;
      //$result_html .= "<br>"; 
      $result_html .= "&nbsp" . $results->places->$place->location->lat;
      $point["lat"] = $results->places->$place->location->lat;
      $result_html .= "<br>Тэги:&nbsp&nbsp&nbsp"; 
      


      $tags = $results->places->$place->tags->tags_0->title;
      $tags = trim($tags);
      $tags = str_replace(",", "", $tags);
      $tags = explode(" ", $tags);

      foreach ($tags as $tag) {
         $result_html .= $tag . "\n";
         $point_tags[] .= $tag . "";
      }

      $result_html .= "<br><hr></p>";
      add_place($db_conn, $point);
      $point_id = $db_conn->insert_id;
      $tag_ids = array();

      foreach ($point_tags as $point_tag) {
        add_tag($db_conn, $point_tag);
        $tag_id = $db_conn->insert_id;
        
        if ($tag_id > 0) {
          bind_tag_to_place($db_conn, $tag_id, $point_id);  
        }
      }

    }
    else {
      $i = $count;
    }

    $i++;

  }

  $result_html .= "<vr>";

  return $result_html;
}

?>
