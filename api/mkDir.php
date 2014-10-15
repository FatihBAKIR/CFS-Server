<?php
require("apiMain.php"); 

$infoArray = array();

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$infoArray["result"] = "fail";

if ($user != 0)
{
	API_SQL::MakeDir($user->id, $_REQUEST["title"], $_REQUEST["dir"]);
	$infoArray["result"] = "success";
	$infoArray["dir"] = $dir;
}

echo json_encode($infoArray);
?>