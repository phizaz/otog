<?
session_start();
include('config.php');

if(!isAdmin()) die('You are not admin.');

$task_id = $_POST['task_id'];
$score = $_POST['score'];

$query = 'update `task` set `score` = ? where `task_id` = ?;';
$sql->prepare($query);
$sql->bind_param('ii', $score, $task_id);
$sql->execute();

echo $score;
?>