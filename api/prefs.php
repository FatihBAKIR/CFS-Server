<?php
require("apiMain.php");

$resArray = array();
$resArray["result"] = "fail";

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

if ($user->id != 0)
{	
	$resArray["result"] = "success";
	if ($_REQUEST["q"] == "set") 
	{
		API_SQL::SetUserPref($user->id, $_REQUEST["key"], $_REQUEST["val"]);
	}
	else 
	{
		$resArray["val"] = API_SQL::GetUserPref($user->id, $_REQUEST["key"], "0");
	}
}

echo json_encode($resArray);
?>