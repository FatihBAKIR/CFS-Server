<? 
require("apiMain.php"); 

$loginArray = array();
$loginArray["result"] = "fail";

$hash = API_SQL::Login($_REQUEST["user"] ,$_REQUEST["pass"])."";
if (!is_integer($hash))
{
	$loginArray["result"] = "success";
	$loginArray["sessionHash"] = $hash;
}

echo json_encode($loginArray);
?>