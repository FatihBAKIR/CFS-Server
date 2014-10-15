<?php
require("apiMain.php"); 

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$file = $sql->filefromid($_REQUEST["id"]);

$retArray = array("result" => "fail");

if (API_SQL::FilePermission($user->id, $file->id)->CanGet() && $file->active == "1")
{
	$retArray["result"] = "success";
	$retArray["stats"] = API_SQL::FileStats($file->id);
}

echo json_encode($retArray);
?>