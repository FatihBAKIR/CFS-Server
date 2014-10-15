<?php
require("apiMain.php"); 

$resArray = array();
$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$resArray["result"] = API_SQL::OutRevision($_REQUEST["id"], $user->id);

echo json_encode($resArray);
?>