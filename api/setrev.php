<?php
require("apiMain.php"); 

$resArray = array();
$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$resArray["result"] = API_SQL::SetRevision($_REQUEST["base"], $_REQUEST["new"], $user->id);

echo json_encode($resArray);
?>