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

    /*if(strlen($context) == null) {
      print "<script language='Javascript' type='text/javascript'>
      alert ('Пустой контекст!');
      </script>";
      return;
    }*/

}

function construct_url() {

  $url = 'http://api.wikimapia.org/?';
  $key = '030D67E2-DA816AC1-F0EBA91A-9AAF6AE2-CCE73D60-D49546B1-C08E9AF2-FA1FA000';
  $lat = '61.784974';
  $lon = '34.347038';
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
  $url .= '&language=ru&page=1&count=50&distance=7000';
  
 // echo "<p>" . $url . "</p><br><br>"; 

 // $url_example = 'http://api.wikimapia.org/?key=030D67E2-DA816AC1-F0EBA91A-9AAF6AE2-CCE73D60-D49546B1-C08E9AF2-FA1FA000&function=place.search&q=%D0%BB%D1%8F%D0%B3%D1%83%D1%88%D0%BA%D0%B0&lat=61.78491&lon=34.34691&format=xml&language=en&page=1&count=50';


  
 // echo $response;

  return $url;
}
?>
