<?php
require("apiMain.php"); 
API_SQL::EndSession($_REQUEST["SH"]);
$resultArray = array();
$resultArray["result"] = "success";

echo json_encode($resultArray);
?>