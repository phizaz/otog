<?
session_start();
include('config.php');
include_once("analyticstracking.php");
$user = $_POST['user'];
$pass = $_POST['pass'];

$query = 'select * from `user` where `user` = ? and `pass` = ?;';
$sql->prepare($query);
$sql->bind_param('ss',$user,$pass);
$sql->execute();
@$sql->bind_result($user_id, $user, $pass, $display, $level);

$result = array();
if($sql->fetch()){
	$result['valid'] = true;
	$_SESSION[$config['name_short']]['login'] = true;
	$_SESSION[$config['name_short']]['user_id'] = $user_id;
	$_SESSION[$config['name_short']]['user'] = $user;
	$_SESSION[$config['name_short']]['display'] = $display;
	$_SESSION[$config['name_short']]['level'] = $level;
}
else {
	$result['valid'] = false;
}
echo json_encode($result);
?>