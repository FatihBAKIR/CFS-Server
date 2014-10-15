<?php
$Info = array("name" => "Content Viewer", "class" => "ContentViewer");

class ContentViewer
{
	static function Install()
	{
		return 1;
	}

	static function Initialize()
	{
		Messenger::AddModifier("contents", "ContentViewer::ViewContents");
		return 1;
	}

	static function ViewContents($res, $user, $input)
	{
		$fileID = $input["r2"];
		$file = API_SQL::$nativeSQL->filefromid(API_SQL::$nativeSQL->IDFromVer($fileID, "latest"));

		if ($file->active == 1 && API_SQL::FilePermission($user->id, $fileID))
		{
			$fileContent = file_get_contents($file->path);
			$res["result"]="success";
			$res["file"]=$file;
			$res["answer"]=$fileContent;
		}

		return $res;
	}
}
?>