<?php
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
  while($i < $count) {  
    $place = "places_". $i; 
    $result_html .= "<p>";
    //echo $xml_str->places->$place->title;
    $result_html .= $xml_str->places->$place->title;

    if($xml_str->places->$place->title != null) {
      $result_html .= "<br>"; 
      $result_html .= $xml_str->places->$place->urlhtml;
      $result_html .= "<br>"; 
      $description = $xml_str->places->$place->description;
      
      if($description === null) {
        $result_html .= "нет описания";
      }
      else {
        $result_html .= $description;
      }
      //$result_html .= $xml_str->places->$place->description;
      $result_html .= "<br>"; 
      $result_html .= $xml_str->places->$place->location->lon;
      $result_html .= "<br>"; 
      $result_html .= $xml_str->places->$place->location->lat;
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
      

    }
    else {
      $i = $count;
      //$result_html .= "Ничего не найдено</p>";
    }

    $i++;

  }
  $result_html .= "<vr><p>DB</p>";

  return $result_html;
}

?>
