<?
include('config.php');

$query = 'select * from `best`;';
$sql->prepare($query);
$sql->execute();
$sql->bind_result($best_id, $user_id, $task_id, $score, $timeused, $text);

$insert = newSqli();
$a = $insert->stmt_init();
$cnt = 0;
while($sql->fetch()){
	echo 'user_id : ' . $user_id. ' score : '. $score . ' ';
	if(abs($score - 100) < 0.0001){
		$query = 'select * from `pass` where `user_id` = ? and `task_id` = ?;';
		$a->prepare($query);
		$a->bind_param('ii', $user_id, $task_id);
		$a->execute();
		if($a->fetch()){
			echo 'exists';
		}
		else {
			$query = 'insert into `pass` (`pass_id`, `user_id`, `task_id`) values (NULL, ?, ?);';
			$a->prepare($query);
			$a->bind_param('ii', $user_id, $task_id);
			$a->execute();
			$cnt++;
			echo 'accepted';
		}
	}
	echo '<br>';
}
echo 'Finished ' . $cnt . ' rows';
?>