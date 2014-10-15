<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers', 'Content-Type');
header("Cache-Control", "public");
//error_reporting(0);
require_once("../system/messenger.php");
require_once("main.php"); 
API_SQL::$nativeSQL = $sql;

require_once("../system/addon.php");
Messenger::AddModifier("file_permission", "API_SQL::CheckOwnership");

class Permission
{
	public $Move = 0;
	public $Get = 0;
	public $FullAccess = 0;

	public function CanGet()
	{
		return $this->Get || $this->FullAccess;
	}

	public function CanMove()
	{
		return $this->Move || $this->FullAccess;
	}

	public function __construct($preset)
	{
		switch ($preset) {
			case 'owner':
				$this->FullAccess = 1;
				break;
			default:
				break;
		}
	}
}

class API_SQL
{
	public static $nativeSQL;
	static function Login($userName, $password)
	{
		if (API_SQL::$nativeSQL->login($userName , md5($password)))
		{
			$user = API_SQL::$nativeSQL->userfromname($userName);
			$user->pass = "";
			if (Messenger::GetModifiers("logging_in", true, $user))
				return API_SQL::GenerateSessionHash($user->id);
			else return 0;
		}
		else return 0;
	}
	
	static function GenerateSessionHash($userID)
	{
		$zaman = zaman();
		$sHash = md5("sessionfor".$userID."&".$zaman);
		$query = mysql_query("insert into apiSessions (userID, timestamp, sessionHash) values ('".$userID."','".$zaman."','".$sHash."')");
		return $sHash;
	}
	
	static function UserFromHash($sHash)
	{
		$query = mysql_query("select * from apiSessions where sessionHash = '" . $sHash . "'");
		if (mysql_num_rows($query) == 0) return 0;
		$id = mysql_result($query, 0, "userID");
		return API_SQL::$nativeSQL->userfromid($id);
	}
	
	static function EndSession($sHash)
	{
		mysql_query("delete from apiSessions where sessionHash='".$sHash."'");
	}
	
	static function SubDirectories($dirID, $userID)
	{
		$query = mysql_query("select * from klasorler where ust_klasor='".$dirID."' AND kullanici='".$userID."'");
		
		$dirs = array();
		while ($row = mysql_fetch_array($query)) {
			$dosya = new klasor();
			$dosya->id = $row["id"];
			$dosya->kimin = $row["kullanici"];
			$dosya->aktif = $row["aktif"];
			$dosya->isim = $row["isim"];
			$dosya->acik = $row["public"];
			$dosya->pass = $row["sifre"];
			$dosya->ustdizin = $row["ust_klasor"];
			if ($dosya->aktif == 1)
			{	
				if (Messenger::GetModifiers("listing_dir", true, $dosya))
					$dirs[] = $dosya;
			}
		}

		$dirs = Messenger::GetModifiers("listing_subdirs", $dirs, $dirID, $userID);
		
		return $dirs;
	}
	
	static function RemoveFile($fileID)
	{
		mysql_query("update yuklemeler set aktif='0' where id='".$fileID."'");
	}
	
	static function RemoveDir($userID ,$dirID)
	{
		$dosyalar = API_SQL::$nativeSQL->klasordekidosyalari($userID, $dirID);
		mysql_query("update klasorler set aktif='0' where id='".$dirID."'");
		for ($i = 0; $i < sizeof($dosyalar); $i++)
		{
			API_SQL::RemoveFile($dosyalar[$i]->id);
		}	
		$klasorler = API_SQL::$nativeSQL->klasorler(API_SQL::$nativeSQL->klasor($dirID));
		for ($i = 0; $i < sizeof($klasorler); $i++)
		{
			API_SQL::RemoveDir($klasorler[$i]->id);
		}	
	}
	
	static function EditFile($fileID, $fileName, $dirID)
	{
		mysql_query("update yuklemeler SET dizin='".$dirID."', baslik='".$fileName."' where id='".$fileID."'");
	}
	
	static function EditDir($dirID, $dirName, $parent)
	{
		mysql_query("update klasorler SET ust_klasor='".$parent."', isim='".$dirName."' where id='".$dirID."'");
	}
	
	static function MakeDir($userID ,$title, $parentDir)
	{		
		mysql_query("insert into klasorler (kullanici, ust_klasor, isim) VALUES ('".$userID."','".$parentDir."','".$title."')");
		$query = mysql_query("select * from klasorler where kullanici = '".$userID."' AND isim = '".$title."' AND ust_klasor = '".$parentDir."'");
		return mysql_result($query, 0, "id");
	}
	
	static function PushFile($file, $title, $dir, $user)
	{
		$PushData = array();
		$PushData["result"] = "fail";
		if ($user->limit <= sizeof(API_SQL::$nativeSQL->dosyalari($user->id)))
		{ 
			$PushData["reason"] = "file_quota";
			return $PushData;
		}
		if ($file["size"] > API_SQL::$nativeSQL->serversetting("maksboyut"))
		{
			$PushData["reason"] = "file_size";		
			return $PushData;	
		}
		if ($file["error"] > 0)
		{
			$PushData["reason"] = "file_error";
			return $PushData;
		}
		
		$zaman = zaman();
		$fName = md5($zaman.$file["name"]);
		move_uploaded_file($file["tmp_name"], "../" . API_SQL::$nativeSQL->serversetting("up_path") . "/" . $fName);
		$fTitle = iconv("ISO-8859-9", "UTF-8", $file["name"]);
		$sifre = "";
		if (isset($title) && $title != "") $fTitle = iconv("ISO-8859-9", "UTF-8", $title);
		$fTitle = replace_tr($fTitle);
		$pub = API_SQL::GetUserPref($user->id, "pub_new", "0");
		$id = API_SQL::$nativeSQL->newfile($file["name"], $fTitle, $fName, "", $file["type"], $file["size"], $dir, $user->id, $pub);
		$PushData["result"] = "success";
		$PushData["fID"] = $id;
		return $PushData;
	}
	
	static function CheckOwnership($value, $userID, $fileID)
	{
		$query = mysql_query("select * from sahiplik where dosya = '" . $fileID . "'");

		if (mysql_num_rows($query) == 0) return $value;

		$id = mysql_result($query, 0, "kullanici");
		if ($id == $userID) $value->FullAccess = 1;

		return $value;
	}
	
	static function GeneratePublicLink($fileID)
	{
		$file = API_SQL::$nativeSQL->filefromid($fileID);
		return API_SQL::$nativeSQL->serversetting("script_adress")."/dosya/".$file->id."/".$file->title;
	}
	
	static function SetPub($fileID, $pub)
	{
		mysql_query("update yuklemeler set pub='".$pub."' where id='".$fileID."'");
	}
	
	static function SetPubAll($userID, $pub)
	{
		$files = API_SQL::$nativeSQL->dosyalari($userID);
		
		for ($i = 0; $i < sizeof($files); $i++)
		{
			API_SQL::SetPub($files[$i]->id, $pub);
		}
	}
	
	static function FileStats($id)
	{
		$query = mysql_query("select * from indirmeler where dosya='".$id."'");
		
		$retArray = array();
		
		$retArray["count"] = mysql_num_rows($query) . "";
		
		$retArray["latest"] = array();
		
		for ($i = 0; $i < min($retArray["count"],10); $i++) $retArray["latest"][] = array("ip" => mysql_result($query, $i, "ip"), "time" => date('d/m/Y', mysql_result($query, $i, "zaman")));
		
		return $retArray;
	}
	
	static function GetUserPref($uID, $key, $value)
	{
		$query = mysql_query("select * from userSettings where userID='".$uID."' and prefkey='".$key."'");
		if (mysql_num_rows($query) > 0) return mysql_result($query, 0, "value");
		return $value;
	}
	
	static function SetUserPref($uID, $key, $value)
	{
		Messenger::BroadcastMessage("user_setting", $uID, $key, $value);
		if (API_SQL::GetUserPref($uID, $key, "noVal") != "noVal")
			mysql_query("delete from userSettings where userID='".$uID."' and prefkey='".$key."'");
		mysql_query("insert into userSettings (userID, prefkey, value) values ('".$uID."', '".$key."', '".$value."')");
	}
	
	static function GetLastFile($uID)
	{
		$query = mysql_query("select * from sahiplik where kullanici='".$uID."' ORDER BY ID DESC");		
		
		$i = 0;
		while (1)
		{
			$file = API_SQL::$nativeSQL->filefromid(mysql_result($query, $i, "dosya"));
			if ($file->active) return $file;
			else $i++;
		}
	}
	
	static function SetRevision($base, $new, $uID)
	{
		if ($new == "last") $new = API_SQL::GetLastFile($uID)->id;
		if ($new <= $base) return "2,".$new;
		if (!(API_SQL::FilePermission($uID, $base)->FullAccess && API_SQL::FilePermission($uID, $new)->FullAccess)) return "1";
		$base = API_SQL::$nativeSQL->IDFromVer($base, "latest");
		mysql_query("insert into versioning (base_file, file) values ('".$base."', '".$new."')");
		return "success";
	}
	
	static function OutRevision($fID, $uID)
	{
		$base = API_SQL::$nativeSQL->BaseOfRev($fID);
		$last = API_SQL::$nativeSQL->IDFromVer($fID, "latest");
		if ($base == $fID)
		{
			mysql_query("delete from versioning where base_file = '".$fID."'");
			return "success";
		}
		if ($last == $fID)
		{
			mysql_query("delete from versioning where file = '".$fID."'");
			return "success";			
		}
		
		$fileBefore;
		$next = $base->id;
		while (1)
		{
			$next = API_SQL::$nativeSQL->IDFromVer($next, 1);
			//print $next->id;
			if ($fID == $next->id)
			{
				 $fileBefore = $next;
				 break;
			}
			$next = $next->id;
		}
		
		$fileAfter = API_SQL::$nativeSQL->IDFromVer($fID, 1);		
		
		mysql_query("delete from versioning where base_file = '".$fileBefore->id."'");
		mysql_query("delete from versioning where base_file = '".$fileAfter->id."'");
		
		API_SQL::SetRevision($fileBefore, $fileAfter, $uID);
	}

	static function DirPermission($userID, $dirID)
	{
		$val = $dirID == 0 || API_SQL::$nativeSQL->klasor($dirID)->kimin == $userID;
		return Messenger::GetModifiers("dir_permission", $val, $userID, $dirID);
	}

	static function FilePermission($userID, $fileID)
	{
		$val = Messenger::GetModifiers("file_permission", new Permission(""), $userID, $fileID);
		return $val;
	}
}
?>