<?
session_start();
include('config.php');
if(!isAdmin()){
	die('You are not admin');
}

$task_id = $_POST['task_id'];
$task = task($task_id);
$new_see = ($task['see'] == 1 ? 0 : 1);
$query = 'update `task` set `see` = ? , `see_date` = '.time().' where `task_id` = ?;';
$sql->prepare($query);
$sql->bind_param('ii', $new_see, $task_id);
$sql->execute();
?>