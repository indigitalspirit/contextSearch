<?php

function show_result($result) {
  echo $result;
}

function get_context() {
    $context = trim($_POST["context"]);
    $context = strip_tags($context); // вырезаем теги
          //конвертируем специальные символы в мнемоники HTML
    $context = htmlspecialchars($context, ENT_QUOTES);
          /* на некоторых серверах
           * автоматически добавляются
           * обратные слеши к кавычкам, вырезаем их */
    $context = stripslashes($context);
    $context = urlencode($context);
    echo "<br>" . $context . "<br>";
    
    $user_tags = array();

}

function construct_url() {

  $url = 'http://api.wikimapia.org/?';
  $key = '030D67E2-DA816AC1-F0EBA91A-9AAF6AE2-CCE73D60-D49546B1-C08E9AF2-FA1FA000';
  $lat = '59.938630';//$lat = '61.784974';
  $lon = '30.314130';//'34.347038';
  $query = trim($_POST["query"]);//'лягушка';


   if(strlen($query) == null) {
      print "<script language='Javascript' type='text/javascript'>
      alert ('Пустой запрос!');
      </script>";
      return;
    }

  $query=strip_tags($query); // вырезаем теги
          //конвертируем специальные символы в мнемоники HTML
  $query=htmlspecialchars($query,ENT_QUOTES);
          /* на некоторых серверах
           * автоматически добавляются
           * обратные слеши к кавычкам, вырезаем их */
  $query=stripslashes($query);
  $query = urlencode($query);


  /* JSON or XML */
  $format = 'xml';

  $url .= 'key=' . $key;
  $url .= '&function=place.search';
  $url .= '&q=' . $query;
  $url .= '&lat=' . $lat;
  $url .= '&lon=' . $lon;
  $url .= '&format=' . $format;
  $url .= '&language=en&page=1&count=100&distance=10000';
  
 // echo "url:  " . $url;
  return $url;
}
?>
