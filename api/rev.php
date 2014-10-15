<?php
require("apiMain.php"); 

$resArray = array();
$resArray["result"] = "fail";
$user = API_SQL::UserFromHash($_REQUEST["SH"]);

if ($_REQUEST["id"] == "last")
	$file = $sql->filefromid($sql->BaseOfRev(API_SQL::GetLastFile($user->id)->id));
else 
	$file = $sql->filefromid($sql->BaseOfRev($_REQUEST["id"]));

if (API_SQL::FilePermission($user->id, $file->id)->CanGet() && $file->active == "1")
{
	$resArray["result"] = "success";
	$resArray["revs"] = array();
	$file = $file->id;
	$f = $sql->filefromid($file);
	$f->utime = date('d/m/Y', $f->utime);
	$resArray["revs"][] = $f;
	while (1)
	{
		$older = $sql->IDFromVer($file, 1);
		if ($file == $older) break;
		$file = $older;
		$f = $sql->filefromid($file);
		$f->utime = date('d/m/Y', $f->utime);
		$resArray["revs"][] = $f;
	}
}

echo json_encode($resArray);
?>