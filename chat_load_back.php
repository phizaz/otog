<?
session_start();
include('config.php');

if(!isLogin() ) {
	include('notlogin.php');
	die();
}


$query = 'select * from `chat` where `chat_id` < ? order by `chat_id` desc limit ?;';
$sql->prepare($query);
$sql->bind_param('ii', $_POST['first'], $config['chat_back_load']);
$sql->execute();
$sql->bind_result($chat_id, $user_id, $text, $time);

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