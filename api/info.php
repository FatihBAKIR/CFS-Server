<?php
require("apiMain.php"); 

$infoArray = array();

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$infoArray["result"] = "fail";

if ($user != 0)
{
	$infoArray["result"] = "success";
	$dosyalar = $sql->dosyalari($user->id);
	
	$boyut = 0;
	for ($i = 0; $i < sizeof($dosyalar); $i++)
	{
		if ($dosyalar[$i]->active == 1)
		{
			$boyut += $dosyalar[$i]->size;
		}
	}
	
	$infoArray["userName"] = $user->name;
	
	$infoArray["usedFile"] = (string)sizeof($dosyalar);
	$infoArray["fileLimit"] = $user->limit;
	
	$infoArray["usedSize"] = filesizeinfo($boyut);
	$infoArray["sizeLimit"] = filesizeinfo($sql->serversetting("boyut_limiti"));
}
echo json_encode($infoArray);
?>