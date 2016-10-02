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
	$sql = 'SELECT place_id FROM places WHERE lat=' . $data["lat"] . 'AND lon=' . $data["lon"];
		//echo "in addd place";
	$res = mysqli_query($db_conn, $sql);
	if (mysqli_num_rows($res) > 0){


	//if ($db_conn->query($sql) === TRUE) {
	    //echo "Place already exists";
		$row = $res->fetch_assoc(); 
		//$row = mysqli_fetch_array($res);
		$id = $row['place_id'];
   		//echo $id;
		return $id;

		//return $row['id'];
	} else {

	$sql = "INSERT INTO places (title, description, url, lat, lon) VALUES ('" . $data["title"] . "', '" . $data["description"] . "', '" . $data["url"] . "', '" . $data["lat"] . "', '" . $data["lon"] . "')";
    

	if ($db_conn->query($sql) === TRUE) {
	    //echo "New place created successfully";
	    return $db_conn->insert_id;
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
	$sql = "SELECT id FROM tags WHERE name='" . $tag_name . "'";

	$res = mysqli_query($db_conn, $sql);
	if (mysqli_num_rows($res) > 0){


	//if ($db_conn->query($sql) === TRUE) {
	    //echo "Place already exists";
		$row = $res->fetch_assoc(); 
		//$row = mysqli_fetch_array($res);
		$id = $row['id'];
   		//echo $id;
		return $id;
	} else {

	$sql = "INSERT INTO tags (name) VALUES ('" . $tag_name . "')";

	if ($db_conn->query($sql) === TRUE) {
	   // echo "New tag created successfully";
		return $db_conn->insert_id;
	} else {
	    echo "Error: " . $sql . "<br>" . $db_conn->error;
	}
	  
	}	
}
function add_sum_to_place($db_conn, $place_id, $sum) {

	$sql = "UPDATE places SET weight='" . $sum . "'WHERE places.place_id='" . $place_id . "'";
	if ($db_conn->query($sql) === TRUE) {

	} else {
	    echo "Error: (add_sum) " . $sql . "<br>" . $db_conn->error;
	    return 0;
	}

}

function add_context($db_conn, $context_name) {
	$sql = "SELECT id FROM context WHERE name='" . $context_name . "'";

	$res = mysqli_query($db_conn, $sql);
	if (mysqli_num_rows($res) > 0){


	//if ($db_conn->query($sql) === TRUE) {
	    //echo "Place already exists";
		$row = $res->fetch_assoc(); 
		//$row = mysqli_fetch_array($res);
		$id = $row['id'];
   		//echo $id;
		return $id;
	} else {

	$sql = "INSERT INTO context (name) VALUES ('" . $context_name . "')";

	if ($db_conn->query($sql) === TRUE) {

		//$context_id = mysqli_query($db_conn, "SELECT SCOPE_IDENTITY();");

	   // return $context_id;
		 return $db_conn->insert_id;

	   // echo "New tag created successfully";
	} else {
	    echo "Error: (add_context) " . $sql . "<br>" . $db_conn->error;
	    return 0;
	}
	   
	}	
}


function bind_tag_to_context($db_conn, $tag_id, $context_id, $url_count) {

	$sql = "INSERT INTO mapping_context_tags (tag_id, context_id, url_count) VALUES ($tag_id, $context_id, $url_count)";

	if ($db_conn->query($sql) === TRUE) {
	    //echo "New bind created successfully";
	} else {
	    echo "Error (bind tag to context): " . $sql . "<br>" . $db_conn->error;
	}
	

}

function bind_tag_to_place($db_conn, $tag_id, $place_id) {

	$sql = "INSERT INTO mapping (place_id, tag_id) VALUES ('" . $place_id . "', '" . $tag_id . "')";

	if ($db_conn->query($sql) === TRUE) {
	    //echo "New bind created successfully";
	} else {
	    echo "Error (bind tag to place): " . $sql . "<br>" . $db_conn->error;
	}
}


function get_places($db_conn) {
	$query = "SELECT * FROM places";

	$result = $db_conn->query($query);
	//$row = $result->fetch_array(MYSQLI_ASSOC);
	//$result->close();
	return $result;
}

function get_places_with_title($db_conn, $title) {
	$query = "SELECT * FROM places WHERE places.title LIKE '%" . $title . "%'";//='" . $title . "'"; //

	$result = $db_conn->query($query);
	//$row = $result->fetch_array(MYSQLI_ASSOC);
	//$result->close();
	return $result;
}

function get_place_id($db_conn, $title) {
	$query = "SELECT place_id FROM places WHERE places.title='" . $title . "';";//='" . $title . "'"; //

	$result = $db_conn->query($query);
	//$row = $result->fetch_array(MYSQLI_ASSOC);
	//$result->close();
	return $result;
}


function get_places_by_weight($db_conn, $context) {
	$query = "SELECT places.title, places.description, places.lat, places.lon, places.weight, tags.name, context.name FROM places, tags, mapping_context_tags, mapping, context WHERE mapping.place_id=places.place_id AND tags.id=mapping.tag_id AND mapping_context_tags.tag_id=tags.id AND context.id=mapping_context_tags.context_id AND context.name='" . $context . "' GROUP BY places.weight;";//='" . $title . "'"; //


	$res = mysqli_query($db_conn, $query);
	//var_dump($res);

	if(mysqli_num_rows($res) <= 0) {
		echo "Ничего не найдено.";
	}
	else {

		for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
	//while($row = mysqli_fetch_array($res))
	//{
		$res->data_seek($row_no);
        $row = $res->fetch_assoc();
		echo "".$row['title']."&nbsp &nbsp";
		//echo "".$row['lat']."&nbsp &nbsp";
		echo "".$row['weight']."<br><hr>";

	}

	}
	
}

?>