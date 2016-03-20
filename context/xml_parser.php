<?php
include_once('db.php');
include_once('utils.php');

function xml_parser($xml_str) {
  $count = $xml_str->found;

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
    $point["title"] = $xml_str->places->$place->title;
    $result_html .= $xml_str->places->$place->title;

    if($xml_str->places->$place->title != null) {
      $result_html .= "<br>"; 

      $result_html .= $xml_str->places->$place->urlhtml;
      $point["url"] = $xml_str->places->$place->urlhtml;

      $result_html .= "<br>"; 

      $description = $xml_str->places->$place->description;
      

      if($description === null) {
        $result_html .= "нет описания";
      }
      else {
        $result_html .= $description;
        $point["description"] = $description;
      }
      //$result_html .= $xml_str->places->$place->description;
      $result_html .= "<br>"; 
      $result_html .= $xml_str->places->$place->location->lon;
      $point["lon"] = $xml_str->places->$place->location->lon;

      $result_html .= "<br>"; 
      $result_html .= $xml_str->places->$place->location->lat;
      $point["lat"] = $xml_str->places->$place->location->lat;
      $result_html .= "<br>"; 
      //$j = 0;
      //$tag_j = "tags_" . $j;
      //$result_html .= $xml_str->places->$place->tags->tags_0->title;


      $tags = $xml_str->places->$place->tags->tags_0->title;
      $tags = trim($tags);
      $tags = str_replace(",", "", $tags);
      $tags = explode(" ", $tags);

      foreach ($tags as $tag) {
         $result_html .= $tag . "\n";
         $point_tags[] .= $tag . "";
      }

      //print_r($tags);

      $result_html .= "<br><hr></p>";
      //$tag_d = $xml_str->places->$place->location->tags;
      //var_dump($xml_str->places->$place->location->tags->children());
      //echo "<br>"; 
      //$node = $xml_str->children();

      //echo  $node[0]->place;
      //echo "<br>";
      //var_dump($xml_str->place[0]->title); 
      //print_r($xml_str->places->places_0->tags->tags_0->title);
      //echo $xml_str->places->places_0->tags->tags_0->title;
      add_place($db_conn, $point);
      $point_id = $db_conn->insert_id;
     // printf ("New Record has id %d.\n", $point_id);
      $tag_ids = array();

      foreach ($point_tags as $point_tag) {
      add_tag($db_conn, $point_tag);
      $tag_id = $db_conn->insert_id;
      if ($tag_id > 0) {
        bind_tag_to_place($db_conn, $tag_id, $point_id);  
      }
      

      //printf ("New Tag has id %d.\n", $db_conn->insert_id);
      
      }

    }
    else {
      $i = $count;
      //$result_html .= "Ничего не найдено</p>";
    }

    $i++;

  }

  $result_html .= "<vr>";

  $result = get_places($db_conn);
 

  //echo $point["title"];
  /*while ($row = $places->fetch_assoc()) {
      $result_html .= "<p>" . $row['id'] . "</p>";
      $result_html .= "<p>" . $row['title'] . "</p>";
      $result_html .= "<p>" . $row['description'] . "</p>";
      $result_html .= "<p>" . $row['url'] . "</p>";
      $result_html .= "<p>" . $row['lat'] . "</p>";  
      $result_html .= "<p>" . $row['lon'] . "</p>";  
      $result_html .= "<p><hr></p>";      
    }*/


    while( $row = mysqli_fetch_assoc($result) ) {
      //print $row['url'];
     // $result_html .= "<p>url" . $row['url'] . "></p>";
      $result_html .= "<p>" . $row['place_id'] . "</p>";
      $result_html .= "<p>" . $row['title'] . "</p>";
      $result_html .= "<p>" . $row['description'] . "</p>";
      $result_html .= "<p>" . $row['url'] . "</p>";
      $result_html .= "<p>" . $row['lat'] . "</p>";
      $result_html .= "<p>" . $row['lon'] . "</p>";

   // 
    $result_html .= "<p><hr></p>";
}
  

  return $result_html;
}

?>
