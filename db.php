<?php

function db_connect() {
	$mysqli = new mysqli("localhost", "root", "root", "csearch");

	if ($mysqli->connect_errno) {
    	printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    	die();
	}

	return $mysqli;
}

/* запись объекта в БД */
function add_place($db_conn, $data) {

	$sql = 'SELECT place_id FROM places WHERE lat=' . $data["lat"] . 'AND lon=' . $data["lon"];
	
	$res = mysqli_query($db_conn, $sql);

	if (mysqli_num_rows($res) > 0) 
	{

		$row = $res->fetch_assoc(); 
	    $id = $row['place_id'];
   		
		return $id;		
	} 
	else 
	{
		$sql = "INSERT INTO places (title, description, url, lat, lon) VALUES ('" . $data["title"] . "', '" . $data["description"] . "', '" . $data["url"] . "', '" . $data["lat"] . "', '" . $data["lon"] . "')";
    
		if ($db_conn->query($sql) === TRUE) 
		{

	    	return $db_conn->insert_id;

		} 
		else 
		{
	    	echo "Error: " . $sql . "<br>" . $db_conn->error;
	    	die();
		}
	   
	}
}

/* запись тега в БД */
function add_tag($db_conn, $tag_name) {

	$sql = "SELECT id FROM tags WHERE name='" . $tag_name . "'";

	$res = mysqli_query($db_conn, $sql);

	if (mysqli_num_rows($res) > 0)
	{
		$row = $res->fetch_assoc(); 
		$id = $row['id'];
   		
		return $id;
	} 
	else 
	{

		$sql = "INSERT INTO tags (name) VALUES ('" . $tag_name . "')";

		if ($db_conn->query($sql) === TRUE) 
		{
			return $db_conn->insert_id;
		} 
		else 
		{
	    	echo "Error: " . $sql . "<br>" . $db_conn->error;
	    	die();
		}
	  
	}	
}

/* запись "веса" объекта в БД */
function add_sum_to_place($db_conn, $place_id, $sum) {

	$sql = "UPDATE places SET weight='" . $sum . "'WHERE places.place_id='" . $place_id . "'";
	if ($db_conn->query($sql) === TRUE) 
	{
		//
	} 
	else 
	{
	    echo "Error: (add_sum) " . $sql . "<br>" . $db_conn->error;
	    return 0;
	}

}

/* запись контекста объекта в БД */
function add_context($db_conn, $context_name) {
	$sql = "SELECT id FROM context WHERE name='" . $context_name . "'";

	$res = mysqli_query($db_conn, $sql);

	if (mysqli_num_rows($res) > 0)
	{
		$row = $res->fetch_assoc(); 
		$id = $row['id'];
		return $id;
	} 
	else 
	{
		$sql = "INSERT INTO context (name) VALUES ('" . $context_name . "')";

		if ($db_conn->query($sql) === TRUE) 
		{

		 	return $db_conn->insert_id;
 
		} 
		else 
		{
	    	echo "Error: (add_context) " . $sql . "<br>" . $db_conn->error;
	    	die();
		}  
	}	
}

/* привязка тега к контексту */
function bind_tag_to_context($db_conn, $tag_id, $context_id, $url_count) {

	$sql = "INSERT INTO mapping_context_tags (tag_id, context_id, url_count) VALUES ($tag_id, $context_id, $url_count)";

	if ($db_conn->query($sql) === TRUE) 
	{
	    //echo "New bind created successfully";
	} 
	else 
	{
	    echo "Error (bind tag to context): " . $sql . "<br>" . $db_conn->error;
	}
}

/* проверка связи тег-контекст */
function check_tag_mapping_to_context($db_conn, $tag_id, $context_id) {

	$sql = "SELECT id FROM mapping_context_tags WHERE context_id='" . $context_id . "'" . "AND tag_id='" . $tag_id . "'";

	$res = mysqli_query($db_conn, $sql);

	if (mysqli_num_rows($res) > 0)
	{
		return true;
	} 
	else 
	{
	    return false;
	}
}

/* привязка тега к объекту */
function bind_tag_to_place($db_conn, $tag_id, $place_id) {

	$sql = "INSERT INTO mapping (place_id, tag_id) VALUES ('" . $place_id . "', '" . $tag_id . "')";

	if ($db_conn->query($sql) === TRUE) 
	{
	    //echo "New bind created successfully";
	} 
	else 
	{
	    echo "Error (bind tag to place): " . $sql . "<br>" . $db_conn->error;
	}
}


/* поиск объектов */
function get_places($db_conn) {
	$result_array = array();

	$query = "SELECT * FROM places";
	$res = mysqli_query($db_conn, $query);

	if(mysqli_num_rows($res) <= 0) 
	{
		$result_array[] = "get_places_by_weight: "."Ничего не найдено.";
		echo "get_places_by_weight: "."Ничего не найдено.  " . $query;
		die();
	}
	else 
	{
		for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) 
		{
			$res->data_seek($row_no);
        	$row = $res->fetch_assoc();

        	$result_array[] = $row['title'] . "&nbsp &nbsp" . $row['weight']."<br><hr>";
		}
		return $result_array;
	}
}

/* поиск объектов */
function get_places_with_title($db_conn, $title) {

	$query = "SELECT * FROM places WHERE places.title LIKE '%" . $title . "%'";

	$result = $db_conn->query($query);
	
	return $result;
}

/* поиск id объекта */
function get_place_id($db_conn, $title) {
	$query = "SELECT place_id FROM places WHERE places.title='" . $title . "';";

	$result = $db_conn->query($query);

	return $result;
}

/* поиск объектов - вывод по-возрастанию веса */
function get_places_by_weight($db_conn, $context) {
	$result_array = array();

	$query = "SELECT places.title, places.description, places.lat, places.lon, places.weight, tags.name, context.name FROM places, tags, mapping_context_tags, mapping, context WHERE mapping.place_id=places.place_id AND tags.id=mapping.tag_id AND mapping_context_tags.tag_id=tags.id AND context.id=mapping_context_tags.context_id AND context.name LIKE '%" . $context . "%' GROUP BY places.weight;";//='" . $title . "'"; //


	$res = mysqli_query($db_conn, $query);

	if(mysqli_num_rows($res) <= 0) 
	{
		$result_array[] = "get_places_by_weight: "."Ничего не найдено.";
		echo "get_places_by_weight: "."Ничего не найдено.  " . $query;
		die();
	}
	else 
	{
		for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) 
		{
			$res->data_seek($row_no);
        	$row = $res->fetch_assoc();

        	$result_array[] = $row['title']."&nbsp &nbsp" . $row['weight']."<br><hr>";
		}

		return $result_array;
	}
}

?>