<?php
$Info = array("name" => "HideIt", "class" => "Hider");

class Hider
{
	static function Install()
	{
		$createString = "CREATE TABLE `hiddenfiles` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`fileID` INT(11) NOT NULL DEFAULT b'0',
							PRIMARY KEY (`id`)
						)
						ENGINE=InnoDB;";
		mysql_query($createString);
		$createString = "CREATE TABLE `hiddendirs` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`dirID` INT(11) NOT NULL DEFAULT b'0',
							PRIMARY KEY (`id`)
						)
						ENGINE=InnoDB;";
		mysql_query($createString);
		return 1;
	}

	static function Initialize()
	{
		Messenger::AddModifier("listing_file", "Hider::FileModifier");
		Messenger::AddModifier("listing_dir", "Hider::DirModifier");
		Messenger::AddModifier("hidef", "Hider::HideFile");
		Messenger::AddModifier("hided", "Hider::HideDir");
		Messenger::AddModifier("unhidef", "Hider::UnHideFile");
		Messenger::AddModifier("unhided", "Hider::UnHideDir");
		return 1;
	}

	static function DirModifier($value, $dir)
	{
		return !self::IsDirHidden($dir->id);
	}

	static function IsDirHidden($dirID)
	{
		$query = mysql_query("select * from hiddendirs where dirID = '".$dirID."'");
		return mysql_num_rows($query) > 0;
	}

	static function FileModifier($value, $file)
	{
		return !self::IsFileHidden($file->id);
	}

	static function IsFileHidden($fileID)
	{
		$query = mysql_query("select * from hiddenfiles where fileID = '".$fileID."'");
		return mysql_num_rows($query) > 0;
	}

	static function HideFile($res, $user, $input)
	{
		$fileID = $input["r2"];

		if (API_SQL::FilePermission($user->id, $fileID)->CanMove())
		{
			mysql_query("insert into hiddenfiles (fileID) values ('".$fileID."')");
			$res["result"] = "success";
			$res["answer"] = "Successfully Hided Your File";			
		}

		return $res;
	}

	static function UnHideFile($res, $user, $input)
	{
		$fileID = $input["r2"];

		if (API_SQL::FilePermission($user->id, $fileID)->CanMove())
		{
			mysql_query("delete from hiddenfiles where fileID = '".$fileID."'");
			$res["result"] = "success";
			$res["answer"] = "Successfully Revealed Your File";			
		}

		return $res;
	}

	static function HideDir($res, $user, $input)
	{
		$dirID = $input["r2"];

		if (API_SQL::DirPermission($user->id, $dirID))
		{
			mysql_query("insert into hiddendirs (dirID) values ('".$dirID."')");
			$res["result"] = "success";
			$res["answer"] = "Successfully Hided Your Folder";			
		}

		return $res;
	}

	static function UnHideDir($res, $user, $input)
	{
		$dirID = $input["r2"];

		if (API_SQL::DirPermission($user->id, $dirID))
		{			
			mysql_query("delete from hiddendirs where dirID = '".$dirID."'");
			$res["result"] = "success";
			$res["answer"] = "Successfully Revealed Your Folder";
		}

		return $res;
	}

}
?>