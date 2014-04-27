<?
session_start();
include('config.php');

if(!isLogin() ) {
	include('notlogin.php');
	die();
}
$text = "";
for($i = 0; $i < strlen($_POST['text'])-3; $i++)
{
	if($_POST['text'][$i]=='<')
		$text.="&lt";
	else
		$text.=$_POST['text'][$i];
}
$query = 'insert into `chat` (`chat_id`, `user_id`, `text`, `time`) values (NULL, ?, ?, ?);';
$sql->prepare($query);
$sql->bind_param('isi', $_SESSION[$config['name_short']]['user_id'], $text, $config['time']);
$sql->execute();
echo 'success';
?>