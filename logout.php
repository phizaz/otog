<?
session_start();
include('config.php');

$query = 'update `activity` set `time` = 0 where `user_id` = ?';
$sql->prepare($query);
$sql->bind_param('i', $_SESSION[$config['name_short']]['user_id']);
$sql->execute();

session_destroy();
?>