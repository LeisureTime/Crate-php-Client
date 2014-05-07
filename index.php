<?php
include_once "crate.php";
$config = array();
$crate = new Crate($config);

$data["created_at"] = 394183938;
$data["id"] =  "21";
$data["retweeted"] =  "false";
$data["source"] =  "mobile";
$data["text"] =  "Hai KEvin";
$data["user_id"] =  "Kevin";

$response = $crate->insert_query("tweets",$data);

/*$response = $crate->post("",'{"stmt":
"update tweets set text = ? where id = ?",
"args": ["welcome To crate PHP clieent update!!!!!", "7"]
}');
*/
echo "<pre>";
print_r($response);

?>