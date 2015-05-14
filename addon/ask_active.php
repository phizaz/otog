<?php
session_start();
include ('../config.php');
include ('config.php');
include ('library.php');

if($_GET["op"]=="ask")
{
	$ins["user_id"] = $_SESSION[$config['name_short']]['user_id'];
	$ins["sub_ask"] = 0;
	$ins["detail"] = $_POST["detail"];
	if($ins["detail"]!="")
	{
		Database::putInto("ask",$ins);
	}
	echo "<meta http-equiv='refresh' content='0; user_ask.php'/>";
}
else if($_GET["op"]=="answer" and isAdmin())
{
	$ins["user_id"] = $_SESSION[$config['name_short']]['user_id'];
	$ins["sub_ask"] = $_POST["ask_id"];
	$ins["detail"] = $_POST["detail"];
	if($ins["detail"]!="")
	{
		Database::putInto("ask",$ins);
	}
	echo "<meta http-equiv='refresh' content='0; admin_ask.php'/>";
}
?>
