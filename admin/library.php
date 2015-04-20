<?php
class Database {
	public static function getUser($user_id)
	{
		$sql = "SELECT * FROM `user` WHERE `user_id` = '".$user_id."'";
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
?>