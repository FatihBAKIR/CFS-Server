<?php
require("apiMain.php"); 

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$klasor;

if (isset($_REQUEST["id"]) && $_REQUEST["id"] != "")
{
	if ($_REQUEST["id"] != "0")	$klasor = $sql->klasor($_REQUEST["id"]);
	else
	{
		$klasor = new klasor();
		$klasor->id = "0";
		$klasor->aktif = "1";
		$klasor->kimin = $user->id;
		$klasor->isim = "Ana Dizin";
	}
}

$dirArray = array();
$dirArray["result"] = "fail";
if (API_SQL::DirPermission($user->id, $klasor->id) && $klasor->aktif == "1")
{	
	$dirArray["result"] = "success";
	$dirArray["dir"] = $klasor;
	$dirArray["files"] = $sql->klasordekidosyalari($user->id,$klasor->id);
	foreach ($dirArray["files"] as $file)
	{
		$file->size = filesizeinfo($file->size);
	}
	$dirArray["dirs"] = API_SQL::SubDirectories($klasor->id, $klasor->kimin);
	if ($_REQUEST["id"] != "0")
	{
		array_unshift($dirArray["dirs"], $sql->klasor($klasor->ustdizin));
	}
}

echo json_encode($dirArray);
?>