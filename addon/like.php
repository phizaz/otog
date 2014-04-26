<?
session_start();
include('config.php');

if(!isLogin() ){
	include('notlogin');
	die();
}

$query = 'select * from `like` where `user_id` = ? and `task_id` = ?;';
$sql->prepare($query);
$sql->bind_param('ii', $my['user_id'], $_POST['task_id']);
$sql->execute();
if($sql->fetch()) {
	die();
}

$query = 'insert into `like` (`like_id`, `user_id`, `task_id`) values (NULL, ?, ?);';
$sql->prepare($query);
$sql->bind_param('ii', $my['user_id'], $_POST['task_id']);
$sql->execute();
?>