<?php
require("apiMain.php"); 

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

if ($_REQUEST["id"] == "last")
	$file = API_SQL::GetLastFile($user->id);
else 
	$file = $sql->filefromid($_REQUEST["id"]);

if ($file->active == 1 && API_SQL::FilePermission($user->id, $file->id)->CanGet())
{
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.basename($file->name));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . $file->size);
	ob_clean();
	flush();
	readfile($file->path);
	$sql->indirme($file->id);
	exit;
}
?>