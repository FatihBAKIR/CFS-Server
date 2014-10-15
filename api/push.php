<?php
require("apiMain.php"); 

$user = API_SQL::UserFromHash($_REQUEST["SH"]);

$file = API_SQL::PushFile($_FILES["file"], $_REQUEST["title"], $_REQUEST["dir"], $user);

if (isset($_REQUEST["base"]) && $_REQUEST["base"] != "") API_SQL::SetRevision($_REQUEST["base"], $file["fID"], $user->id);
echo json_encode($file);
?>