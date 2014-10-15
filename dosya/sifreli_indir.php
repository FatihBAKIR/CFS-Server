<? require("../main.php"); ?>
<?
if (isset($_REQUEST["id"]) && isset($_REQUEST["baslik"]) && isset($_REQUEST["sifre"]))
{
 	$dosya = $sql->filefromid($_REQUEST["id"]);
	if ($dosya->id != 0 && $dosya->active == 1 && $dosya->pass == $_REQUEST["sifre"])
	{
		$_SESSION["izin".$dosya->id] = 1;
		echo "dogru";
	}
	else
	{
		if ($dosya->pass == $_REQUEST["sifre"])
			echo "yok";
		else
			echo "yanlis";
	}
}
?>