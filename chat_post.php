<?
session_start();
include('config.php');

if(!isLogin() ) {
	include('notlogin.php');
	die();
}

$query = 'insert into `chat` (`chat_id`, `user_id`, `text`, `time`) values (NULL, ?, ?, ?);';
$sql->prepare($query);
$sql->bind_param('isi', $_SESSION[$config['name_short']]['user_id'], $_POST['text'], $config['time']);
$sql->execute();
echo 'success';
?>