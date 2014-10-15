<?php
require("apiMain.php");

$resArray["result"] = "fail";
$user = API_SQL::UserFromHash($_REQUEST["SH"]);

if ($_REQUEST["id"] == "all")
{	
	API_SQL::SetPubAll($user->id, $_REQUEST["pub"]);
	$resArray["result"] = "success";
}
else
{
	if ($_REQUEST["id"] == "last")
		$file = API_SQL::GetLastFile($user->id);
	else 
		$file = $sql->filefromid($_REQUEST["id"]);
	
	if (API_SQL::FilePermission($user->id, $file->id)->CanMove() && $file->active == "1")
	{		
 		$file = $sql->filefromid($sql->IDFromVer($file->id, "latest"));
		$resArray["result"] = "success";
		API_SQL::SetPub($file->id, $_REQUEST["pub"]);
		$file = $sql->filefromid($_REQUEST["id"]);
		$file->size = filesizeinfo($file->size);
		$resArray["file"] = $file;
	}
}

echo json_encode($resArray);
?>