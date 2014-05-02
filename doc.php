<?php 
session_start();
include "config.php";

$task_id = $_GET['id'];

$query = 'select `name_short`, `see` from `task` where `task_id` = ?';
$sql->prepare($query);
$sql->bind_param('i',$task_id);
$sql->execute();
$sql->bind_result($name_short, $visible);

if($sql->fetch()) {
	$path = 'doc/' . $name_short . '.pdf';
	if(file_exists($path)){
		if($visible or isAdmin()) {
			header('Content-Description: File Transfer');
	    header('Content-Type: application/pdf');
	    header('Content-Disposition: inline; filename="'. $name_short . '.pdf"');
	    header('Content-Length: ' . filesize($path));
	    ob_clean();
	    flush();
	    readfile($path);
	    $sqlx = "select * from `task` where `task_id` = ".$task_id;
		$result = mysql_query($sqlx);
		$task = mysql_fetch_object($result);
	    echo "<title>".$task->name." ".($name_short)."</title>";
	    exit;
	  } else {
	  	echo 'this file is not visible by now.';
	  }
	} else {
		echo 'file not found location:' . $path;
		exit;
	}
} else {
	echo 'task not recognized';
}