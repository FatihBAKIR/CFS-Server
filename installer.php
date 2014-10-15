<?php
	###
	###	CFS Server Installation Script
	###	M.Fatih BAKIR
	###	http://www.fatihbakir.net
	###

	#Please Fill In These Variables
	$DatabaseHost = "localhost"; 
	$DatabaseUser = "root";
	$DatabasePass = "";
	$DatabaseName = "cfs";

	$CFS_Path = "http://localhost/cfserver"; #http://cfs.yourdomain.com
	$MaxFileSize = 209715200; #bytes
	$UploadPath = "upload";
	$MaxFileCount = 100; #files
	$MaxTotalSize = 1073741824; #bytes

	$configContent = 
"<?php
$host = \"$DatabaseHost\";
$db = \"DatabaseName\";
$user = \"DatabaseUser\";
$pass = \"DatabasePass\";
?>";

	file_put_contents("api/config.php", $configContent);

	#Connecting To Tadabase
	@mysql_connect($DatabaseHost, $DatabaseUser, $DatabasePass) or die ("Database Error 1");
	@mysql_select_db($DatabaseName) or die ("Database Error 2");
	mysql_query("SET NAMES 'utf8'");
	mysql_query("SET CHARACTER SET utf8");
	mysql_query("SET COLLATION_CONNECTION = 'utf8_general_ci'");

	#Creating Base Database Structure
	$InstallSQL = file_get_contents('cfs.sql');
	$sqlQueries = explode(";", $InstallSQL);
	for ($i=0; $i < count($sqlQueries); $i++) {
		mysql_query($sqlQueries[$i]);
	}

	#Inserting necessary settings
	mysql_query("insert into ayarlar (ayarisim, deger) values ('maksboyut', '".$MaxFileSize."')");
	mysql_query("insert into ayarlar (ayarisim, deger) values ('up_path', '".$UploadPath."')");
	mysql_query("insert into ayarlar (ayarisim, deger) values ('script_adress', '".$CFS_Path."')");
	mysql_query("insert into ayarlar (ayarisim, deger) values ('dosya_sayisi', '".$MaxFileCount."')");
	mysql_query("insert into ayarlar (ayarisim, deger) values ('boyut_limiti', '".$MaxTotalSize."')");
?>