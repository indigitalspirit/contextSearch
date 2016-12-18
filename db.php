<?php
include_once('utils.php');

function db_connect() {
	$mysqli = new mysqli("localhost", "root", "root", "csearch");

	if ($mysqli->connect_errno) {
    	logger("DB db_connect ", "Error: " . $mysqli->connect_error . "\n");
    	show_error("Error in DB connection" . $mysqli->connect_error);
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

	else if(!($res = mysqli_query($db_conn, $sql))) {
		logger("DB add_place ", "Error: " . $sql . "<br>" . printf("", mysqli_error($db_conn)) . "\n");
    
    	show_error("Error in DB query (add_place)" . $sql . htmlspecialchars("&nbsp") . printf("", mysqli_error($db_conn)));  
	}

	else 
	{
		$sql = "INSERT INTO places (title, description, url, lat, lon) VALUES ('" . $data["title"] . "', '" . $data["description"] . "', '" . $data["url"] . "', '" . $data["lat"] . "', '" . $data["lon"] . "')";
    
		if ($db_conn->query($sql) === TRUE) 
		{
	    	return $db_conn->insert_id;
		} 
		else if(!$db_conn->query($sql)) 		
		{
	    	logger("DB add_place ", "Error: " . $sql . "<br>" . printf("", $db_conn->error) . "\n");
    		show_error("Error in DB query (add_place)" . $sql . htmlspecialchars("&nbsp") . printf("", $db_conn->error));   
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
	    	logger("DB add_tag ", "Error: " . $sql . "<br>" . printf("", $db_conn->error) . "\n");
    		show_error("Error in DB query (add_tag)" . $sql . htmlspecialchars("&nbsp") . printf("", $db_conn->error));
		}
	  
	}	
}

/* запись "веса" объекта в БД */
function add_sum_to_place($db_conn, $place_id, $sum) {

	$sql = "UPDATE places SET weight='" . $sum . "'WHERE places.place_id='" . $place_id . "'";
	if ($db_conn->query($sql) === TRUE) 
	{
		logger("DB add_sum_to_place ", "Query done" . "\n");
		return true;
	} 
	else 
	{
	    logger("DB add_sum_to_place ", "Error: " . $sql . "<br>" . printf("", $db_conn->error) . "\n");
    	show_error("Error in DB query (add_sum_to_place)" . $sql . htmlspecialchars("&nbsp") . printf("", $db_conn->error));
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
	    	logger("DB add_context ", "Error: " . $sql . "<br>" . printf("", $db_conn->error) . "\n");
    		show_error("Error in DB query (add_context)" . $sql . htmlspecialchars("&nbsp") . printf("", $db_conn->error));
		}  
	}	
}

/* привязка тега к контексту */
function bind_tag_to_context($db_conn, $tag_id, $context_id, $url_count) {

	$sql = "INSERT INTO mapping_context_tags (tag_id, context_id, url_count) VALUES ($tag_id, $context_id, $url_count)";

	if ($db_conn->query($sql) === TRUE) 
	{
	    logger("DB bind_tag_to_context ", "Query done" . "\n");
	} 
	else 
	{
	    logger("DB bind_tag_to_context ", "Error: " . $sql . "<br>" . printf("", $db_conn->error) . "\n");
    	show_error("Error in DB query (bind_tag_to_context)" . $sql . htmlspecialchars("&nbsp") . printf("", $db_conn->error));
	}
}

/* проверка связи тег-контекст */
function check_tag_mapping_to_context($db_conn, $tag_id, $context_id) {

	$sql = "SELECT id FROM mapping_context_tags WHERE context_id='" . $context_id . "'" . "AND tag_id='" . $tag_id . "'";

	if(!($res = mysqli_query($db_conn, $sql))) {
		logger("DB bind_tag_to_context ", "Error: " . $sql . "<br>" . printf("", mysqli_error($db_conn)) . "\n");
    	show_error("Error in DB query (bind_tag_to_context)" . $sql . htmlspecialchars("&nbsp") . printf("", mysqli_error($db_conn)));
	}

	else if (mysqli_num_rows($res) > 0)
	{
		return TRUE;
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
	    logger("DB bind_tag_to_place ", "Query done" . "\n");
	    return TRUE;
	} 
	else 
	{
	    logger("DB bind_tag_to_place ", "Error: " . $sql . "<br>" . printf("", $db_conn->error) . "\n");
    	show_error("Error in DB query (bind_tag_to_place)" . $sql . htmlspecialchars("&nbsp") . printf("", $db_conn->error));
	}
}


/* поиск объектов */
function get_places($db_conn) {
	$result_array = array();

	$query = "SELECT * FROM places";
	$res = mysqli_query($db_conn, $query);

	if(!($res = mysqli_query($db_conn, $query))) {
		logger("DB get_places ", "Error: " . $sql . "<br>" . printf("", $db_conn->error) . "\n");
    	show_error("Error in DB query (get_places)" . $sql . htmlspecialchars("&nbsp") . $printf("", $db_conn->error));
	}

	if(mysqli_num_rows($res) <= 0) 
	{
		logger("DB get_places ", "Ничего не найдено" . "\n");
    	$result_array[] = "Ничего не найдено";

    	return $result_array;
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
function get_places_by_weight($db_conn, $context, $query_words) {
	$result_array = array();

	$query = "SELECT places.title, places.description, places.lat, places.lon, places.weight, tags.name, context.name FROM places, tags, mapping_context_tags, mapping, context WHERE mapping.place_id=places.place_id AND tags.id=mapping.tag_id AND mapping_context_tags.tag_id=tags.id AND context.id=mapping_context_tags.context_id AND context.name LIKE '%" . stem($context, STEM_RUSSIAN_UNICODE) . "%' AND (";

	//$query_second_part = null;

	for($i=0; $i<count($query_words); $i++) {

		if ($query_words[$i] != null) {

			$query .= "places.title LIKE '%" . $query_words[$i] . "%'";

			if ($i != (count($query_words) - 1)) {
			 	$query .= " OR "; 
			 } 
		}	
			
	}

	$query .= ")";
	//" GROUP BY places.weight;";//='" . $title . "'"; //

	logger("DB get_places_by_weight ", "QUERY: " . $query . "\n");

	if(!($res = mysqli_query($db_conn, $query))) {
		logger("DB get_places_by_weight ", "Error: " . $sql . "<br>" . printf("", mysqli_error($db_conn)) . "\n");
    	show_error("Error in DB query (get_places_by_weight)  " . $sql . printf("", mysqli_error($db_conn)));
	}

	if(mysqli_num_rows($res) <= 0) 
	{
		logger("DB get_places_by_weight ", "Ничего не найдено" . "\n");
    	$result_array[] = "Ничего не найдено";
    	return $result_array;
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