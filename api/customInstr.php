<?php
require("apiMain.php"); 

$resArray = array();
$resArray["result"] = "fail";
$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$resArray = Messenger::GetModifiers($_REQUEST["r1"], $resArray, $user, $_REQUEST);

echo json_encode($resArray);
?>