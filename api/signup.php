<?php
require("apiMain.php"); 

$result = array();
$res = $sql->yeniuye($_REQUEST["user"], $_REQUEST["pass"], $_REQUEST["email"]);

$result["result"] = $res == 1 ? "success" : "fail";

echo json_encode($result);
?>