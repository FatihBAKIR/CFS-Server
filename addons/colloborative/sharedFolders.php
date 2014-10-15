<?php
$Info = array("name" => "Shared Folders", "class" => "FolderSharing");

class FolderSharing
{
	static function Install()
	{
		$createString = "CREATE TABLE `sharedfolders` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`dirID` INT(11) NOT NULL DEFAULT b'0',
							`userID` INT(11) NOT NULL DEFAULT b'0',
							PRIMARY KEY (`id`)
						)
						ENGINE=InnoDB;";
		mysql_query($createString);
		$createString = "CREATE TABLE `sharedpermissions` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`dirID` INT(11) NOT NULL DEFAULT b'0',
							`userID` INT(11) NOT NULL DEFAULT b'0',
							PRIMARY KEY (`id`)
						)
						ENGINE=InnoDB;";
		mysql_query($createString);

		return 1;
	}

	static function Initialize()
	{
		Messenger::AddModifier("logging_in", "FolderSharing::ChkSharedFolder");
		Messenger::AddModifier("listing_subdirs", "FolderSharing::ModifyDirs");
		Messenger::AddModifier("dir_permission", "FolderSharing::CheckDPermission");
		Messenger::AddModifier("file_permission", "FolderSharing::CheckFPermission");
		Messenger::AddModifier("share", "FolderSharing::Share");
		Messenger::AddModifier("unshare", "FolderSharing::UnShare");
		return 1;
	}

	static function Share($res, $user, $input)
	{
		$dirID = $input["r2"];
		$userID = $input["r3"];

		if (API_SQL::DirPermission($user->id, $dirID))
		{
			mysql_query("insert into sharedpermissions (userID, dirID) values ('".$userID."', '".$dirID."')");
			$res["result"] = "success";
			$res["answer"] = "Successfully Shared Your Folder";
			if ($userID != 0)
				Messenger::BroadcastMessage("send_email", API_SQL::$nativeSQL->userfromid($userID)->email, "CFS Folder Share", $user->name ." Has Shared A Folder With You.");
		}

		return $res;
	}

	static function UnShare($res, $user, $input)
	{
		$dirID = $input["r2"];
		$userID = $input["r3"];

		if (API_SQL::DirPermission($user->id, $dirID))
		{
			mysql_query("delete from sharedpermissions where userID = '".$userID."' AND dirID = '".$dirID."'");
			$res["result"] = "success";
			$res["answer"] = "Successfully Unshared Your Folder";
		}

		return $res;
	}

	static function ChkSharedFolder($value, $user)
	{
		self::UserSharedFolder($user->id);

		return $value;
	}

	static function UserSharedFolder($userID)
	{
		$query = mysql_query("select * from sharedfolders where userID = '".$userID."'");
		if (mysql_num_rows($query) > 0)
		{
			$dirID = mysql_result($query, 0, "dirID");
			$dir = API_SQL::$nativeSQL->klasor($dirID);
			if ($dirID == $dir->id && $dir->aktif) return $dirID;

			mysql_query("delete from sharedfolders where dirID = '".$dirID."'");
			return self::UserSharedFolder($userID);			
		}
		else
		{
			$newDir = API_SQL::MakeDir($userID, "Shared Folders", 0);
			mysql_query("insert into sharedfolders (userID, dirID) values ('".$userID."', '".$newDir."')");
			return self::UserSharedFolder($userID);
		}
	}

	static function CheckPermission($val, $userID, $dirID)
	{
		$query = mysql_query("select * from sharedpermissions where userID = '".$userID."' AND dirID='".$dirID."'");
		return $val || mysql_num_rows($query) > 0;
	}

	static function CheckDPermission($val, $userID, $dirID)
	{
		return $val || self::RecursiveCheck($dirID, $userID);
	}

	static function CheckFPermission($val, $userID, $fileID)
	{
		$file = API_SQL::$nativeSQL->filefromid($fileID);
		
		$val->Get = $val->Get || self::RecursiveCheck($file->dir, $userID);

		return $val;
	}

	static function ModifyDirs($dirs, $dirID, $userID)
	{
		if ($dirID != self::UserSharedFolder($userID)) return $dirs;
		
		$query = mysql_query("select * from sharedpermissions where userID = '".$userID."'");

		for ($i = 0; $i < mysql_num_rows($query); $i++)
		{
			$dirs[] = API_SQL::$nativeSQL->klasor(mysql_result($query, $i, "dirID"));
		}

		return $dirs;
	}

	///Checks if any of the upper directories of this folder is available to the querying user
	static function RecursiveCheck($dirID, $userID)
	{
		$dir = API_SQL::$nativeSQL->klasor($dirID);

		if (self::CheckPermission(false, $userID, $dir->id))
			return true;
		else if ($dir->ustdizin == 0)
			return false;
		else
			return self::RecursiveCheck($dir->ustdizin, $userID);
	}
}
?>