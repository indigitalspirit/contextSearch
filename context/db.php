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
$mysqli = new mysqli("localhost", "root", "", "context_search");
/* проверка соединения */
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}
}

function add_place($db_conn, $data) {
	$query = mysqli_query($db_conn, 'SELECT * FROM places WHERE lat=' . $data[3] . 'AND lon=' . $data[4]);

	if (mysqli_num_rows($query) > 0){

	    echo "Place already exists";
	} else {

	$sql = "INSERT INTO places (title, description, url, lat, lon) VALUES ($data[0], $data[1], $data[2], $data[3], $data[4])";

	if ($mysqli->query($sql) === TRUE) {
	    echo "New place created successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	    // do something
	    	//if (!mysqli_query($con,$query)) {
	        //	die('Error: ' . mysqli_error($con));
	    	//}
	}
}


function add_tag($tag_name) {
	$query = mysqli_query($db_conn, 'SELECT * FROM tags WHERE name=' . $tag_name);

	if (mysqli_num_rows($query) > 0){

	    echo "Tag already exists";
	} else {

	$sql = "INSERT INTO tags (name) VALUES ($tag_name)";

	if ($mysqli->query($sql) === TRUE) {
	    echo "New tag created successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	    // do something
	    	//if (!mysqli_query($con,$query)) {
	        //	die('Error: ' . mysqli_error($con));
	    	//}
	}	
}

function bind_tag_to_place($tag_id, $place_id) {
	$query = mysqli_query($db_conn, 'SELECT * FROM mapping WHERE tag_id=' . $tag_id . 'AND place_id=' . $place_id);

	if (mysqli_num_rows($query) > 0){

	    echo "Bind already exists";
	} else {

	$sql = "INSERT INTO mapping (place_id, tag_id) VALUES ($place_id, $tag_id)";

	if ($mysqli->query($sql) === TRUE) {
	    echo "New bind created successfully";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}
	    // do something
	    	//if (!mysqli_query($con,$query)) {
	        //	die('Error: ' . mysqli_error($con));
	    	//}
	}	
}

/* $sql = "INSERT INTO places (title, description, url, lat, lon, tag)
VALUES ('test1', 'test test test', 'test.com', 54.567889, 64.678940, 'test_tag')"; */

/* $sql = "INSERT INTO tags (name, link) VALUES ('tag_test1', 'test link')"; */

/*$sql = "INSERT INTO tags (place_id, tag_id) VALUES (1, 1)";

if ($mysqli->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}
*/
//$mysqli->close();

//$result = $mysqli->query("SELECT 'Hello, dear MySQL user!' AS _message FROM DUAL");
//$row = $result->fetch_assoc();
//echo htmlentities($row['_message']);
?>