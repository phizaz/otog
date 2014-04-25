<?php
// if(!isset($_SESSION['user_id']))
// 	echo "<meta http-equiv='refresh' content='0; login.php?fail=2'/>";
include('config.php');
include('library.php');
if($_POST["type"]=="main")
{
	if($_POST["index"]=="mode")
	{
		$sql = "SELECT * FROM `config` WHERE `index` = 'mode'";
		$result = mysql_query($sql) or die(mysql_error());
		$mode = mysql_fetch_array($result) or die(mysql_error());
		if($_POST["val"]=="contest" or $_POST["val"]=="blind_contest")
		{
			// Database::deleteAllThat("backup","`task_id` > 0");
			$re_task = Database::getAllThat("task","`see` = 1");
			while($task = mysql_fetch_array($re_task))
			{
				$input["task_id"]=$task["task_id"];
				Database::putInTo("backup",$input);
				$inputx["see"]=0;
				Database::updateById("task",$task["task_id"],$inputx);
			}
		}
		else if($_POST["val"]=="online")
		{
			$re_backup = Database::getAll("backup");
			while($task = mysql_fetch_array($re_backup))
			{
				$input["see"]=1;
				Database::updateById("task",$task["task_id"],$input);
			}
			Database::deleteAllThat("backup","`task_id` > 0");
		}
	}
	$sql = "UPDATE `config` SET `val` = '".$_POST["val"]."' WHERE `index` = '".$_POST["index"]."'";
	mysql_query($sql) or die("<div style='background-color:#FFBFD3'><br><div style='height:1px'></div><h4>UNSUCCESS</h4><div style='height:1px'></div><br></div>");
}
else if($_POST["type"]=='main_time')
{
	$date = strtotime($_POST["val"]);
	$sql = "UPDATE `config` SET `val` = '".$date."' WHERE `index` = '".$_POST["index"]."'";
	mysql_query($sql) or die("<div style='background-color:#FFBFD3'><br><div style='height:1px'></div><h4>UNSUCCESS</h4><div style='height:1px'></div><br></div>");

}
else if($_REQUEST["action"]=="add_task")
{
	Database::putInTo("task",$_POST);
	echo "<meta http-equiv='refresh' content='0; ../admin/#task'/>";
}
else if($_REQUEST["action"]=="rem_task")
{
	Database::deleteAllThat("task","`task_id` = '".$_POST['task_id']."'");
}
else if($_REQUEST["action"]=="edit_task")
{
	$sql = "SELECT * FROM `task` WHERE `task_id` = ".$_REQUEST["task_id"];
	$result = mysql_query($sql) or die(mysql_error());
	$task = mysql_fetch_array($result) or die(mysql_error());
	if($_POST["see"]==1 and $task["see"]==0)
		$_POST["see_date"]=time();
	Database::UpdateById("task",$_REQUEST["task_id"],$_POST);
	// echo "<meta http-equiv='refresh' content='0; ../admin/#task'/>";
}
else if($_REQUEST["action"]=="add_user")
{
	Database::putInTo("user",$_POST);
	echo "<meta http-equiv='refresh' content='0; ../admin/#user'/>";
}
else if($_REQUEST["action"]=="rem_user")
{
	Database::deleteAllThat("user","`user_id` = '".$_POST['user_id']."'");
}
else if($_REQUEST["action"]=="edit_user")
{
	Database::UpdateById("user",$_REQUEST["user_id"],$_POST);
	// echo "<meta http-equiv='refresh' content='0; ../admin/#user'/>";
}
else if($_REQUEST["action"]=="add_ann")
{
	Database::putInTo("announce",$_POST);
	echo "<meta http-equiv='refresh' content='0; ../admin/#announce'/>";
}
else if($_REQUEST["action"]=="rem_ann")
{
	Database::deleteAllThat("announce","`announce_id` = '".$_POST['announce_id']."'");
}
else if($_REQUEST["action"]=="edit_ann")
{
	Database::UpdateById("announce",$_REQUEST["announce_id"],$_POST);
	// echo "<meta http-equiv='refresh' content='0; ../admin/#announce'/>";
}
?>
<div class="panel panel-success">
	<div class="panel-heading"><h3 class="panel-title">Server massage</h3></div>
	<div class="panel-body" align="middle">SAVE SUCCESSFUL</div>
</div>