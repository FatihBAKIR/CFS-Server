<?php
require("apiMain.php"); 

$infoArray = array();

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$infoArray["result"] = "fail";

if ($user != 0)
{
	$file = $sql->filefromid($_REQUEST["id"]);
	
	if ($file->id == $_REQUEST["id"] && $file->active == "1" && API_SQL::FilePermission($user->id, $file->id))
	{	
		$infoArray["result"] = "success";
		$infoArray["file"] = $file;
		
		$newName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $file->title;
		$newDir= isset($_REQUEST["dir"]) ? $_REQUEST["dir"] : $file->dir;
		
		API_SQL::EditFile($file->id, $newName, $newDir);
	}
}

echo json_encode($infoArray);
?>