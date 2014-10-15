<?php
require("apiMain.php"); 

$infoArray = array();

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$infoArray["result"] = "fail";

if ($user != 0)
{
	$file = $sql->filefromid($_REQUEST["id"]);
	
	if ($file->id == $_REQUEST["id"] && $file->active == "1")
	{	
		$infoArray["result"] = "success";
		$infoArray["file"] = $file;
		API_SQL::RemoveFile($file->id);
	}
}

echo json_encode($infoArray);
?>