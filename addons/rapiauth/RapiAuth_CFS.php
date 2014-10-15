<?php
$Info = array("name" => "RapiAuth CFS", "class" => "RapiAuthCFS");

class RapiAuthCFS
{
	static function Install()
	{
		$createString = "CREATE TABLE `rapiauth` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`userID` INT(11) NOT NULL DEFAULT '0',
							`AuthActive` INT(1) NOT NULL DEFAULT b'0',
							PRIMARY KEY (`id`)
						)
						ENGINE=InnoDB;";
		mysql_query($createString);
		return 1;
	}

	static function Initialize()
	{
		Messenger::AddModifier("logging_in", "RapiAuthCFS::CheckCode");
		Messenger::AddListener("user_setting", "RapiAuthCFS::SetAuth");
		return 1;
	}

	static function CheckCode($value, $user)
	{
		if (!self::IsAuthEnabled($user->id)) return true;
		include_once ("RapiAuth.php");
		if (!isset($_REQUEST["ra_code"])) $authCode = "";
		else $authCode = $_REQUEST["ra_code"];

		if ($authCode == "") return false;
		$Authenticatior = new RapiAuth("c4ca4238a0b923820dcc509a6f75849b");

		$token = $user->id;
		if (!$Authenticatior->Kontrol($token, $authCode))
		{
			return false;
		}

		return true;
	}

	static function IsAuthEnabled($userID)
	{
		$query = mysql_query("select * from rapiauth where userID = '".$userID."'");
		return mysql_num_rows($query) && mysql_result($query, 0, "AuthActive") == 1;
	}

	static function SetAuth($userID, $key, $val)
	{

		if ($key != "AuthActive") return;
		$query = mysql_query("select * from rapiauth where userID = '".$userID."'");
		if (mysql_num_rows($query) > 0)
			mysql_query("update rapiauth set AuthActive = ".$val." where userID='".$userID."'");
		else
			mysql_query("insert into rapiauth (userID, AuthActive) values ('".$userID."', ".$val.")");
	}
}
?>