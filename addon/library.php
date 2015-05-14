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
		$result = mysql_query($sql);
		return mysql_fetch_array($result);
	}

	public static function getById($from ,$id)
	{
		$sql = "SELECT * FROM `".$from."` WHERE `".$from."_id` = '".$id."'";
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
		mysql_query($sql);
	}

	public static function deleteById($table, $id)
	{
		$sql = "DELETE FROM `".$table."` WHERE `".$table."_id` = ".$id;
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
?>