<?php
session_start();
include('config.php');
include('library.php');
$sql = "SELECT * FROM `user` WHERE `user` = '".$_POST['user']."' AND `pass` = '".$_POST['pass']."' ORDER BY `level` DESC";
$result = mysql_query($sql) or die(mysql_error());
$user_info = mysql_fetch_array($result);
if(isset($user_info["user_id"]))
{
	if($user_info["level"]==0)
	{
		$_SESSION["user_id"]=$user_info["user_id"];
		echo "<meta http-equiv='refresh' content='0; index.php'/>";
	}
	else
		echo "<meta http-equiv='refresh' content='0; login.php?fail=1'/>";
}
else
	echo "<meta http-equiv='refresh' content='0; login.php?fail=1'/>";
?>