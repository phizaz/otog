<?
session_start();
include('config.php');

if(!isLogin() ) {
	include('notlogin.php');
	die();
}

if($_POST['last'] == -1){ //FIRST TIME LOAD
	$query = 'select * from `chat` order by `chat_id` desc limit ?;';
	$sql->prepare($query);
	$sql->bind_param('i', $config['chat_first_time_load']);
	$sql->execute();
	$sql->bind_result($chat_id, $user_id, $text, $time);
}
else { //RELOAD
	$query = 'select * from `chat` where `chat_id` > ? order by `chat_id` asc;';
	$sql->prepare($query);
	$sql->bind_param('i', $_POST['last']);
	$sql->execute();
	$sql->bind_result($chat_id, $user_id, $text, $time);
}

function cmp($a, $b){
	return $a['chat_id'] < $b['chat_id'] ? -1 : 1;
}

$result = array();
while($sql->fetch()){
	$user = user($user_id);
	$result[] = array('chat_id' => $chat_id, 'user_display' => $user['display'], 'text' => $text, 'time' => D('H:M:S d m y', $time));
}
usort($result, 'cmp');
echo json_encode($result);
?>