<?php
class Database {
	public static function getUser($user_id)
	{
		$sql = "SELECT * FROM `ws_user_info` WHERE `id` = '".$user_id."'";
		$result_user = mysql_query($sql);
		$user_info = mysql_fetch_array($result_user);
		return $user_info;
	}

	public static function getAll($from)
	{
		$sql = "SELECT * FROM `".$from."`";
		$result = mysql_query($sql);
		return $result;
	}

	public static function getAllThat($from ,$where)
	{
		$sql = "SELECT * FROM `".$from."` WHERE ".$where;
		$result = mysql_query($sql);
		return $result;
	}

	public static function getThat($from ,$where)
	{
		$sql = "SELECT * FROM `".$from."` WHERE ".$where;
		$result = mysql_query($sql) or die(mysql_error());
		return mysql_fetch_array($result) or die(mysql_error());
	}

	public static function getById($from ,$id)
	{
		$sql = "SELECT * FROM `".$from."` WHERE `id` = '".$id."'";
		$result = mysql_query($sql);
		$info = mysql_fetch_array($result);
		return $info;
	}

	public static function putInto($table,$info)
	{
		$field = "";
		$values = "";
		$first = true;
		foreach ($info as $key => $value)
		{
			if($first)
			{
				$first = !$first;
				$field.="`".$key."`";
				$values.="'".$value."'";
			}
			else
			{
				$field.=",`".$key."`";
				$values.=",'".$value."'";
			}
		}
		$sql = "INSERT INTO `".$table."` (".$field.") VALUES (".$values.")";
		mysql_query($sql);
		return mysql_insert_id();
	}
	public static function deleteAllThat($table, $info)
	{
		$sql = "DELETE FROM `".$table."` WHERE ".$info;
		mysql_query($sql) or die(mysql_error());
	}

	public static function deleteById($table, $id)
	{
		$sql = "DELETE FROM `".$table."` WHERE `id` = ".$id;
		mysql_query($sql);
	}

	public static function updateById($table, $id, $info)
	{
		$new_info = NULL;
		$first = true;
		foreach ($info as $key => $value) {
			if($first)
			{
				$first = !$first;
				$new_info.="`".$key."` = '".$value."'";
			}
			else
			{
				$new_info.=", `".$key."` = '".$value."'";
			}
		}
		$sql = "UPDATE `".$table."` SET ".$new_info." WHERE `".$table."_id` = ".$id;
		mysql_query($sql);
	}
}

class Member {
	public static function isUser()
	{
		if(!isset($_COOKIE['user_id']))
		{
			// Redirect to login page
			echo "<meta http-equiv='refresh' content='0; login.php'/>";
			exit();
		}
		else
		{
			$user_info = Database::getById("ws_user_info", $_COOKIE['user_id']);
			if($user_info["type"]!="student")
			{
				echo "<meta http-equiv='refresh' content='0; login.php'/>";
				exit();
			}
			else
			{
				setcookie("user_id",$_COOKIE['user_id'],time()+7200);
				setcookie("user_fullname",$_COOKIE['user_fullname'],time()+7200);
			}
		}
	}

	public static function isAdmin()
	{
		if(!isset($_COOKIE['admin_id']))
		{
			// Redirect to login page
			echo "<meta http-equiv='refresh' content='0; login.php'/>";
			exit();
		}
		else
		{
			$user_info = Database::getById("ws_user_info", $_COOKIE['admin_id']);
			if($user_info["type"]!="admin")
			{
				echo "<meta http-equiv='refresh' content='0; login.php'/>";
				exit();
			}
			else
			{
				setcookie("admin_id",$_COOKIE['admin_id'],time()+7200);
				setcookie("admin_fullname",$_COOKIE['admin_fullname'],time()+7200);
			}
		}
	}
}

class Notice {
	public static function mailUpdate()
	{
		$update = Database::getById("ws_notice",1);
		if($update["update"] != date('Y-m-d'))
		{
			$mail_subject = "มีงานที่ใกล้จะต้องส่ง";
			$mail_headers = "Content-type:text/html;charset=UTF-8" . "\r\n";
			$mail_headers .= "From: project@scius.kku.ac.th";
			$set = array('update' => date('Y-m-d'));
			Database::updateById("ws_notice",1,$set);
			$result_work = Database::getAllThat("ws_work","`date_due` = '".date('Y-m-d', strtotime('+1 day'))."'");
			while($work = mysql_fetch_array($result_work))
			{
				$mail_txt = "<html>มีงานที่ใกล้จะต้องส่ง <a href='".SITE."/work_show.php?work_id=".$work["id"]."'>".$work["name"]."</a></html>";
				$subject = Database::getById("ws_subject", $work["subject_id"]);
				$class = Database::getById("ws_class", $subject["class_id"]);
				$result_std = Database::getAllThat("ws_user_info", "`class_id` = ".$class["id"]);
				while($std = mysql_fetch_array($result_std))
				{
					$chk = mail($std["email"],$mail_subject,$mail_txt,$mail_headers);
				}
			}
		}
	}
}

class Remover {
	public static function user($id)
	{
		$database = array(
			"ws_chat",
			"ws_conclude",
			"ws_post",
			"ws_work_status",
			"ws_work_step"
		);
		foreach ($database as $key => $value) {
			Database::deleteAllThat($value,"`user_id` = ".$id);
		}
		$group_result = Database::getAllThat("ws_work_group","`user_id` = ".$id);
		while($group = mysql_fetch_array($group_result)){
			Remover::group($group["id"]);
		}
		$work_result = Database::getAllThat("ws_work","`user_id` = ".$id);
		while($work = mysql_fetch_array($work_result)){
			Remover::work($work["id"]);
		}
		Database::deleteById("ws_user_info",$id);
	}

	public static function work($id)
	{
		$database = array(
			"ws_work_status"
		);
		foreach ($database as $key => $value) {
			Database::deleteAllThat($value,"`work_id` = ".$id);
		}
		$group_result = Database::getAllThat("ws_work_group","`work_id` = ".$id);
		while($group = mysql_fetch_array($group_result)){
			Remover::group($group["id"]);
		}
		Database::deleteById("ws_work",$id);
	}

	public static function group($id)
	{
		Database::deleteAllThat("ws_chat","`group_id` = ".$id);
		Database::deleteAllThat("ws_work_step","`group_id` = ".$id);
		Database::deleteById("ws_work_group",$id);
	}

	public static function subject($id)
	{
		Database::deleteAllThat("ws_conclude","`subject_id` = ".$id);
		$work_result = Database::getAllThat("ws_work","`subject_id` = ".$id);
		while($work = mysql_fetch_array($work_result)){
			Remover::work($work["id"]);
		}
		Database::deleteById("ws_subject",$id);
	}

	public static function class_tmp($id)
	{
		$subject_result = Database::getAllThat("ws_subject","`class_id` = ".$id);
		while($subject = mysql_fetch_array($subject_result)){
			Remover::subject($subject["id"]);
		}
		$user_result = Database::getAllThat("ws_user_info","`class_id` = ".$id);
		while($user = mysql_fetch_array($user_result)){
			Remover::user($user["id"]);
		}
		Database::deleteById("ws_class",$id);
	}
}
?>