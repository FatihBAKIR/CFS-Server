<? require("../api/main.php"); ?>
<?
if (isset($_REQUEST["id"]))
{
 	$dosya = $sql->filefromid($sql->IDFromVer($_REQUEST["id"], "latest"));
	if ($dosya->id != 0 && $dosya->pub == 1 && $dosya->active == 1 && ($dosya->pass == "" || (isset($_SESSION["izin".$dosya->id]) && $_SESSION["izin".$dosya->id] == 1)))
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($dosya->name));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $dosya->size);
		ob_clean();
		flush();
		readfile($sql->InternalFile($dosya->id));
		$_SESSION["izin".$dosya->id] = 0;
		$sql->indirme($dosya->id);
		exit;
	}
}
?>