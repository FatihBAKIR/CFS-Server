<?php
require("apiMain.php"); 

$infoArray = array();

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$infoArray["result"] = "fail";

if ($user != 0)
{
	$dir = $sql->klasor($_REQUEST["id"]);
	
	if ($dir->id == $_REQUEST["id"] && $dir->aktif == "1")
	{	
		$infoArray["result"] = "success";
		$infoArray["dir"] = $dir;
		API_SQL::RemoveDir($user->id ,$dir->id);
	}
}

echo json_encode($infoArray);
?>