<?php
require("apiMain.php"); 

$resArray = array();
$resArray["result"] = "fail";
$user = API_SQL::UserFromHash($_REQUEST["SH"]);

if ($_REQUEST["id"] == "last")
	$file = API_SQL::GetLastFile($user->id);
else 
	$file = $sql->filefromid($_REQUEST["id"]);

if (API_SQL::FilePermission($user->id, $file->id)->CanGet() && $file->active == "1")
{
	$resArray["result"] = "success";
	$file->path = API_SQL::GeneratePublicLink($file->id);
	$file->size = filesizeinfo($file->size);
	$file->utime = date("d/m/Y", $file->utime);
	$resArray["file"] = $file;
}

echo json_encode($resArray);
?>