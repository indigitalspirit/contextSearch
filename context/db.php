<?php
/*
$link = mysql_connect('localhost', 'context_search', 'root');
if (!$link) {
      die('Ошибка соединения!' . mysql_error());
      }
echo 'Успешное соедининение';
mysql_close($link);
*/
function db_connect() {
$mysqli = new mysqli("localhost", "root", "", "csearch");
/* проверка соединения */
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}
return $mysqli;
}

function add_place($db_conn, $data) {
	$query = mysqli_query($db_conn, 'SELECT * FROM places WHERE lat=' . $data["lat"] . 'AND lon=' . $data["lon"]);

	if (mysqli_num_rows($query) > 0){

	   // echo "Place already exists";
	} else {

	$sql = "INSERT INTO places (title, description, url, lat, lon) VALUES ('" . $data["title"] . "', '" . $data["description"] . "', '" . $data["url"] . "', '" . $data["lat"] . "', '" . $data["lon"] . "')";
    

	if ($db_conn->query($sql) === TRUE) {
	    //echo "New place created successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $db_conn->error;
	}
	    // do something
	    	//if (!mysqli_query($con,$query)) {
	        //	die('Error: ' . mysqli_error($con));
	    	//}
	}
}


function add_tag($db_conn, $tag_name) {
	$query = mysqli_query($db_conn, "SELECT * FROM tags WHERE name='" . $tag_name . "'");

	if (mysqli_num_rows($query) > 0){

	    //echo "Tag already exists";
	} else {

	$sql = "INSERT INTO tags (name) VALUES ('" . $tag_name . "')";

	if ($db_conn->query($sql) === TRUE) {
	   // echo "New tag created successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $db_conn->error;
	}
	    // do something
	    	//if (!mysqli_query($con,$query)) {
	        //	die('Error: ' . mysqli_error($con));
	    	//}
	}	
}

function bind_tag_to_place($db_conn, $tag_id, $place_id) {

	$sql = "INSERT INTO mapping (place_id, tag_id) VALUES ($place_id, $tag_id)";

	if ($db_conn->query($sql) === TRUE) {
	   // echo "New bind created successfully";
	} else {
	    //echo "Error: " . $sql . "<br>" . $db_conn->error;
	}
	    // do something
	    	//if (!mysqli_query($con,$query)) {
	        //	die('Error: ' . mysqli_error($con));
	    	//}
	//}	
}


function get_places($db_conn) {
	$query = "SELECT * FROM places";

	$result = $db_conn->query($query);
	//$row = $result->fetch_array(MYSQLI_ASSOC);
	//$result->close();
	return $result;

	//SELECT name FROM tags WHERE tags.id IN ( SELECT tag_id FROM mapping WHERE mapping.place_id = '6')
	
	    // do something
	    	//if (!mysqli_query($con,$query)) {
	        //	die('Error: ' . mysqli_error($con));
	    	//}
	
}

function get_places_with_title($db_conn, $title) {
	$query = "SELECT * FROM places WHERE places.title LIKE '%" . $title . "%'";//='" . $title . "'"; //

	$result = $db_conn->query($query);
	//$row = $result->fetch_array(MYSQLI_ASSOC);
	//$result->close();
	return $result;

	//SELECT name FROM tags WHERE tags.id IN ( SELECT tag_id FROM mapping WHERE mapping.place_id = '6')
	
	    // do something
	    	//if (!mysqli_query($con,$query)) {
	        //	die('Error: ' . mysqli_error($con));
	    	//}
	
}
?>