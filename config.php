<?
// @session_start();
date_default_timezone_set('Asia/Bangkok');
include('mysql.php');
error_reporting(0); // turn off all notice
function newSqli(){
	global $mysql;
	$mysqli = new mysqli($mysql['host'], $mysql['user'], $mysql['pass'], $mysql['database']);
	$mysqli->query('SET NAMES utf8');
	return $mysqli;
}
$mysqli = newSqli();
$sql = $mysqli->stmt_init();

// GET CONFIG FROM DATABASE

$query = 'select `index`, `val`, `type` from `config`;';
$sql->prepare($query);
$sql->execute();
$sql->bind_result($index, $val, $type);

$config = array();
while($sql->fetch()){
	if($type == 'string') $config[$index] = $val;
	else if($type == 'int') $config[$index] = intval($val);
	else if($type == 'double') $config[$index] = doubleval($val);
	else if($type == 'bool') {$config[$index] = $val == 'true' ? true : false;}
	else die('Config var type unknown. type : ' . $type);
}
$config['time'] = time(0);

$singlecase = array('P' => 'Accepted', 'T' => 'Time limit exceeded', '-' => 'Wrong answer', 'X' => 'Crashed');
$month = array('มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม');

function isAdmin(){
	global $_SESSION, $config;
	if($_SESSION[$config['name_short']]['level'] == 0) return true;
	return false;
}
function inTime(){
	global $config, $_SESSION;
	if($config['force_start'] || isAdmin() || $config['mode'] == 'online' || 
		($config['time'] >= $config['start_time'] && $config['time'] <= $config['end_time'])){
		return true;
	}
	else {
		return false;
	}
}
function isLogin(){
	global $_SESSION, $config;
	if( ( $config['online'] || isAdmin() ) && $_SESSION[$config['name_short']]['login'] == true) return true;
	return false;
}
function isBlind(){
	global $config;
	if(!isAdmin() && $config['mode'] == 'blind_contest') return true;
	return false;
}
$user_loaded = array();
function user($user_id){
	if(!isset($user_loaded[$user_id]) ) {
		$mysqli = newSqli();
		$sql = $mysqli->stmt_init();
		$query = 'select * from `user` where `user_id` = ?;';
		$sql->prepare($query);
		$sql->bind_param('i',$user_id);
		$sql->execute();
		$sql->bind_result($user_id, $user, $pass, $display, $level);
		$sql->fetch();
		$user_loaded[$user_id] = array('user_id' => $user_id, 'user' => $user, 'pass' => $pass, 'display' => $display, 'level' => $level);
	}
	return $user_loaded[$user_id];
}
function D($str, $timestamp){
	global $month;
	$arr = str_split($str);
	$output = '';
	for($i = 0, $len = count($arr); $i < $len; $i++){
		if($arr[$i] == 'y') $output .= date('Y', $timestamp) + 543;
		else if($arr[$i] == 'm') $output .= $month[intval(date('m', $timestamp))-1];
		else if($arr[$i] == 'w') $output .= date('m', $timestamp);
		else if($arr[$i] == 'd') $output .= date('d', $timestamp);
		else if($arr[$i] == 'H') $output .= date('H', $timestamp);
		else if($arr[$i] == 'M') $output .= date('i', $timestamp);
		else if($arr[$i] == 'S') $output .= date('s', $timestamp);
		else $output .= $arr[$i];
	}
	return $output;
}
function latest($task_id, $user_id){
	$query = 'select * from `latest` where `task_id` = ? and `user_id` = ?;';
	$mysqli = newSqli();
	$sql = $mysqli->stmt_init();
	$sql->prepare($query);
	$sql->bind_param('ii', $task_id, $user_id);
	$sql->execute();
	$sql->bind_result($latest_id, $user_id, $task_id, $score, $timeused, $text);
	if($sql->fetch()) return array('success' => true, 'latest_id' => $latest_id, 'score' => $score, 'timeused' => $timeused, 'text' => $text);
	else return array('success' => false);
}
function best($task_id, $user_id){
	$query = 'select * from `best` where `task_id` = ? and `user_id` = ?;';
	$mysqli = newSqli();
	$sql = $mysqli->stmt_init();
	$sql->prepare($query);
	$sql->bind_param('ii', $task_id, $user_id);
	$sql->execute();
	$sql->bind_result($best_id, $user_id, $task_id, $score, $timeused, $text);
	if($sql->fetch()) return array('success' => true, 'best_id' => $best_id, 'score' => $score, 'timeused' => $timeused, 'text' => $text);
	else return array('success' => false);
}
function pass($task_id, $user_id){
	$query = 'select * from `pass` where `task_id` = ? and `user_id` = ?;';
	$mysqli = newSqli();
	$sql = $mysqli->stmt_init();
	$sql->prepare($query);
	$sql->bind_param('ii', $task_id, $user_id);
	$sql->execute();
	if($sql->fetch()) return true;
	else return false;
}
$task_loaded = array();
function task($task_id){
	global $task_loaded;
	if(!isset($task_loaded[$task_id]) ) {
		$query = 'select `task_id`, `name`, `name_short`, `score`, `time_limit`, `mem_limit`, `see` from `task` where `task_id` = ?;';
		$mysqli = newSqli();
		$sql = $mysqli->stmt_init();
		$sql->prepare($query);
		$sql->bind_param('i', $task_id);
		$sql->execute();
		$sql->bind_result($task_id, $name, $name_short, $score, $time_limit, $mem_limit, $see);
		if($sql->fetch()) $task_loaded[$task_id] = array('success' => true, 'task_id' => $task_id, 'name' => $name, 'name_short' => $name_short, 'link' => $link, 'score' => $score, 'time_limit' => $time_limit, 'mem_limit' => $mem_limit, 'see' => $see);
		else $task_loaded[$task_id] = array('success' => false);
	}
	return $task_loaded[$task_id];
}
function showDif($dif){
	$time = array('day' => 0, 'hour' => 0, 'min' => 0, 'sec' => 0);
	$time['sec'] = intval($dif);
	$time['min'] = intval($time['sec'] / 60); $time['sec'] %= 60;
	$time['hour'] = intval($time['min'] / 60); $time['min'] %= 60;
	$time['day'] = intval($time['hour'] / 24); $time['hour'] %= 24;
	$thai = array('sec' => '', 'min' => ':', 'hour' => ':', 'day' => 'วัน<br>');
	$chk_show = false;
	foreach($time as $key => $val){
		if($chk_show)
			printf("%02d%s",$val,$thai[$key]);
		else if($val > 0){
			printf("%d%s",$val,$thai[$key]);
			$chk_show = true;
		}
	}
}


//UPDATE THE LATEST ACTIVITY
if(isLogin()){
	$my = $_SESSION[$config['name_short']];
	
	$query = 'select `activity_id` from `activity` where `user_id` = ?;';
	$sql->prepare($query);
	$sql->bind_param('i', $my['user_id']);
	$sql->execute();
	$sql->bind_result($activity_id);
	if($sql->fetch()){
		$query = 'update `activity` set `time` = ? where `activity_id` = ?;';
		$sql->prepare($query);
		$sql->bind_param('ii', $config['time'], $activity_id);
		$sql->execute();
	}
	else {
		$query = 'insert into `activity` (`activity_id`, `user_id`, `time`) values (NULL, ?, ?);';
		$sql->prepare($query);
		$sql->bind_param('ii', $my['user_id'], $config['time']);
		$sql->execute();
	}
}

?>