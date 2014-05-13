<?php
include_once "crate.php";
$config = array();
$crate = new Crate($config);

/*
$data["created_at"] = 394183938;
$data["id"] =  "21";
$data["retweeted"] =  "false";
$data["source"] =  "mobile";
$data["text"] =  "Hai KEvin";
$data["user_id"] =  "Kevin";
*/

$file_path = $_FILES["profilePicture"]["tmp_name"];

$response = $crate->uploadBlob($file_path, "myblobs");

echo "<pre>-->";
print_r($response);


/*$response = $crate->insert_query("tweets",$data);


$response = $crate->post('{"stmt":
"insert into obj_table (title,objects) values (?,?)",
"args": ["test", [{"age":23,"name":"ramesh"},{"age":23,"name":"ramesh"},{"age":23,"name":"ramesh"}]]
}');
*/

//{"age":23,"name":"ramesh"}	--> object

//[{"age":23,"name":"ramesh"},{"age":23,"name":"ramesh"},{"age":23,"name":"ramesh"}]		--> arrays of objects

?>