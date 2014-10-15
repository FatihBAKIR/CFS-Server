<?php
$Info = array("name" => "Mail Confirmation", "class" => "MailConfirmation");

class MailConfirmation
{
	static function Install()
	{
		$createString = "CREATE TABLE `pendingUsers` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`username` TEXT NOT NULL,
							`password` TEXT NOT NULL,
							`email` TEXT NOT NULL,
							`confKey` TEXT NOT NULL,
							`confirmed` INT(1) NOT NULL DEFAULT b'0',
							PRIMARY KEY (`id`)
						)
						ENGINE=InnoDB;";
		mysql_query($createString);

		return 1;
	}

	static function Initialize()
	{
		Messenger::AddModifier("signing_up", "MailConfirmation::NewUser");
		Messenger::AddModifier("emailConf", "MailConfirmation::Confirmation");
		return 1;
	}

	static function NewUser($value, $userName, $password, $email)
	{
		if (!self::isPending($userName, $email))
		{
			$confKey = md5($userName.$email."key".zaman()."CFSconf");
			mysql_query("insert into pendingUsers (username, password, email, confKey) values ('".$userName."', '".$password."', '".$email."', '".$confKey."')");
			self::SendMail($email, self::GenerateConfLink($confKey));
		}
		else
		{
			if (self::isConfirmed($userName, $email))
			{
				self::removePending($userName, $email);
				return true;
			}
		}
			return false;
	}

	static function Confirmation($res, $user, $REQ)
	{
		$key = $REQ["r2"];

		$query = mysql_query("select * from pendingUsers where confKey = '".$key."'");
		if (mysql_num_rows($query) > 0)
		{
			self::setConfirmed(mysql_result($query, 0, "username"), mysql_result($query, 0, "email"));
			API_SQL::$nativeSQL->yeniuye(mysql_result($query, 0, "username"), mysql_result($query, 0, "password"), mysql_result($query, 0, "email"));
			$res["result"] = "success";
			$res["answer"] = "Your Account Has Been Activated";
		}

		return $res;
	}

	static function isPending($userName, $email)
	{
		$query = mysql_query("select * from pendingUsers where username = '".$userName."' OR email = '".$email."'");
		if (mysql_num_rows($query) > 0) return true;
	}

	static function isConfirmed($userName, $email)
	{		
		$query = mysql_query("select * from pendingUsers where username = '".$userName."' OR email = '".$email."'");
		if (mysql_num_rows($query) == 0) return false;

		if (mysql_result($query, 0, "confirmed") == 1) return true;
		else return false;
	}

	static function setConfirmed($userName, $email)
	{
		mysql_query("update pendingUsers set confirmed = 1 where username = '".$userName."' OR email = '".$email."'");		
	}

	static function removePending($userName, $email)
	{
		mysql_query("delete from pendingUsers where username = '".$userName."' OR email = '".$email."'");
	}

	static function GenerateConfLink($confKey)
	{
		return API_SQL::$nativeSQL->serversetting("script_adress")."/api/customInstr.php?r0=custom&r1=emailConf&r2=".$confKey;
	}

	static function SendMail($email, $confLink)
	{
		$confMail = "Your Confirmation Link: <a href=\"".$confLink."\" >".$confLink."</a>";
		Messenger::BroadcastMessage("send_email", $email, "CFS Account Confimation", $confMail);
	}
}
?>