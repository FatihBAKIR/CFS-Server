<?php
require("apiMain.php"); 

$infoArray = array();

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$infoArray["result"] = "fail";

if ($user != 0 && $_REQUEST["id"] != $_REQUEST["dir"])
{
	$dir = $sql->klasor($_REQUEST["id"]);
	
	if ($dir->id == $_REQUEST["id"] && $dir->aktif == "1")
	{	
		$infoArray["result"] = "success";
		$infoArray["dir"] = $dir;
		
		$newName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $dir->isim;
		$newDir= isset($_REQUEST["dir"]) ? $_REQUEST["dir"] : $dir->ustdizin;
		
		API_SQL::EditDir($dir->id, $newName, $newDir);
	}
}

echo json_encode($infoArray);
?>