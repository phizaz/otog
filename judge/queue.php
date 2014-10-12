<?php
include("../config.php");
$message = "";
$text = '';
$score = 0;
$timeused = 0;

function extension($file){
	$extension = explode('.', $file);
	return strtolower($extension[1]);
}
function name($file){
	$name = explode('.', $file);
	return strtolower($name[0]);
}
function rm($file){
	if(file_exists($file)){
		$command = 'rm ' . $file;
		exec($command);
	}
}
function makedir($dir){
	if(!file_exists($dir)){
		$command = 'mkdir ' . $dir;
		exec($command);
	}
}
function cp($from, $to){
	$command = 'cp ' . $from . ' ' . $to;
	exec($command);
}
function t($file){
	$command = 'touch ' . $file;
	exec($command);
}
function wipe($file){
	rm($file);
	t($file);
}
function degrade(){
	GLOBAL $sql, $grading_id;
	//DEGRADNIG
	$query = 'delete from `grading` where `grading_id` = ?;';
	$sql->prepare($query);
	$sql->bind_param('i', $grading_id);
	$sql->execute();
}
function write(){
	GLOBAL $sql, $user_id, $task_id, $time, $text, $score, $timeused, $message;
	//ADD TO RESULT
	//not yet finished
	$query = 'insert into `result` (
			`result_id`,
			`user_id`,
			`task_id`,
			`time`,
			`text`,
			`score`,
			`timeused`,
			`message`
		) values (NULL, ?, ?, ?, ?, ?, ?, ?);';
	$sql->prepare($query);
	$sql->bind_param('iiisdds', $user_id, $task_id, $time, $text, $score, $timeused, $message);
	$sql->execute();
}
function error($code){
	GLOBAL $message, $text, $score, $timeused;
	echo $code . '\n';
	$text = 'err';
	$message = $code . '<br>เกรดเดอร์จะหยุดทำงานดังนั้นไป start grader ด้วย';
	$score = 0;
	$timeused = 0;
	degrade();
	write();
	die();
}
function compile($file){
	echo 'Compiling '. $file."\n";
	$name = name($file);
	$extension = extension($file);
	echo 'Extension : ' .$extension."\n";
	$list = array('c' => 'gcc -std=c99 ', 'cpp' => 'g++');

	wipe('message.txt');
	rm('compiled/'.$name.'.out');
	$command = $list[$extension] . ' -O2 upload/' . $file . ' -o compiled/' . $name . '.out -lm 2> message.txt';
	echo 'command : ' . $command . "\n";
	exec($command);

	return $name;
}
function message(){
	echo 'Reading compiler message'."\n";
	$filename = 'message.txt';
	$handle = fopen($filename, 'r');
	$content = '';
	if(filesize($filename) > 0) $content = fread($handle, filesize($filename));
	fclose($handle);
	return $content;
}
function run($name, $task, $case){
	global $config;
	echo 'Runnig name : ' . $name . ' task : ' . $task['name_short'] . ' case : ' . $case . "\n";
	rm('output.txt');
	rm('box.txt');
	$input = 'ev/'.$task['name_short'].'/'.$case.'.in';
	if(!file_exists($input)){
		error('Input test case file not found! task : '.$task['name_short'].' case : '.$case);
	}
	$command = './' . $config['box_file'] .' -i '.$input.' -o output.txt -m '.($task['mem_limit'] * 1000).' -t '.$task['time_limit'].' -T compiled/'.$name.'.out 2> box.txt';
	exec($command);

	//GET INFO FROM box.txt
	if(!file_exists('box.txt')) error('File box.txt not found!');
	$handle = fopen('box.txt', 'r');
	$i = 0;
	$line = array();
	while(!feof($handle)){
		$line[$i] = fgets($handle);
		if(trim($line[$i]) != '') $i++;
	}
	fclose($handle);

	$ok = 0;
	if(trim($line[0]) == 'Time limit exceeded.' ) $ok = 'T';
	else if(trim($line[0]) == 'OK') $ok = 'O';
	else $ok = 'X';

	$timeused = '';
	$lastLine = $line[$i-1];
	$lastLine = str_split($lastLine);
	$start = false;
	for($j = 0, $len = count($lastLine); $j < $len; $j++){
		if($lastLine[$j] == 'r') $start = true;
		else if($lastLine[$j] == 'u') break;
		else if($start) $timeused .= $lastLine[$j];
	}

	return array('result' => $ok, 'timeused' => doubleval($timeused));
}
function compare($task, $case, $style){
	echo 'Comparing task : '.$task['name_short'].' case : ' . $case. "\n";
	rm('grader_result.txt');
	wipe('grader_path.txt');

	if($style == 'ruby'){
		$handle = fopen('grader_path.txt', 'w');
		$path = 'ev/'.$task['name_short'].'/'.$case.'.sol';
		fwrite($handle, $path);

		$check = 'ev/'.$task['name_short'].'/check.rb';
		if(!file_exists($check)) error('Check file (ruby) not found! task : '. $task['name_short'] . ' case : '. $case);
		$command = 'ruby '.$check;
		exec($command);
	}
	else if($style == 'cpp'){
		$path = 'ev/'.$task['name_short'].'/';
		$judge = $path . 'check.out';
		if(!file_exists($judge) ){
			//COMPILE
			if(!file_exists($path.'check.cpp') ) error('Check file (cpp) not found! task : '. $task['name_short'] . ' case : '. $case);
			$command = 'g++ -O2 '.$path.'check.cpp -o '.$judge.' -lm';
			exec($command);
		}
		$command = $judge . ' ' . $path . $case . '.sol';
		exec($command);
	}

	$grader_result = 'grader_result.txt';
	if(!file_exists($grader_result)) error('Grader result not fonud! task : '.$task['name_short'] . ' case : '. $case);
	$handle = fopen($grader_result, 'r');
	$line = fgets($handle);
	if(trim($line) == 'P') return 'P';
	else return '-';
}

echo 'Grader is Working';
while(true){
	//CHECK QUEUE
	$query = 'select * from `queue` order by `queue_id` desc limit 1;';
	$sql->prepare($query);
	$sql->execute();
	$sql->bind_result($queue_id, $user_id, $task_id, $time, $file);
	if($sql->fetch()){
		//DEQUEUE
		$query = 'delete from `queue` where `queue_id` = ?;';
		$sql->prepare($query);
		$sql->bind_param('i',$queue_id);
		$sql->execute();

		//ADD TO GRADING
		$query = 'insert into `grading` (
				`grading_id` ,
				`user_id`,
				`task_id`,
				`time`,
				`file`
				) values (NULL, ?, ?, ?, ?);';
		$sql->prepare($query);
		$sql->bind_param('iiis', $user_id, $task_id, $time, $file);
		$sql->execute();
		$grading_id = $mysqli->insert_id;

		$user = user($user_id);
		$task = task($task_id);

		//CREATE THIS RESULT DIRECTORY
		if($config['mode'] != 'online'){
			$parent_dir = 'graded/' . D('y-w-d', time(0));
			makedir($parent_dir);
			$thisDir = $parent_dir . '/' . $user['user'] . '-' .$task['name_short']. '-' . D('H:M:S', time(0));
			makedir($thisDir);
			cp('upload/' . $file, $thisDir . '/' . $file);
		}

		//COMPILE
		$name = compile($file);
		$message = message();
		$text = ''; $score = 0; $timeused = 0;
		if(file_exists('compiled/'.$name.'.out')){ // COMPILE SUCCESS
			//GRADE
			if($task['success']){

				$script = 'ev/'.$task['name_short'].'/script.php';
				if(!file_exists($script)){
					error('Script file not found! ' . $script);
				}
				$style = 'ruby';
				include($script); //$cases IS HERE

				$passed  = 0;
				for($case = 1; $case <= $cases; $case++){
					//GRADE case
					$result = run($name, $task, $case);
					$timeused += $result['timeused'];
					if($result['result'] == 'O'){
						//COPY OUTPUT TO THISDIR
						if($config['mode'] != 'online') cp('output.txt', $thisDir . '/out-' . $case . '.txt');

						//COMPARE
						$final = compare($task, $case, $style);
						$text .= $final;
						if($final == 'P'){
							$passed++;
						}
					}
					else {
						$text .= $result['result'];
					}
				}
				$score = $passed / $cases * 100;

				//WRITE PASSED
				if($passed == $cases && !pass($task_id, $user_id)){
					$query = 'insert into `pass` (
						`pass_id`,
						`user_id`,
						`task_id`
						) values (NULL, ?, ?);';
					$sql->prepare($query);
					$sql->bind_param('ii', $user_id, $task_id);
					$sql->execute();
				}

				//WRIET BEST
				$best = best($task_id, $user_id);
				if($best['success'] &&
					($best['score'] < $score ||
						(abs($best['score'] - $score) < 0.00001 &&
							$best['timeused'] > $timeused) ) ){
					$query = 'update `best` set
						`score` = ?,
						`timeused` = ?,
						`text` = ?
						where `best_id` = ?;';
					$sql->prepare($query);
					$sql->bind_param('ddsi', $score, $timeused, $text, $best['best_id']);
					$sql->execute();
				}
				else if(!$best['success']){
					$query = 'insert into `best` (
						`best_id`,
						`user_id`,
						`task_id`,
						`score`,
						`timeused`,
						`text`
						) values (NULL, ?, ?, ?, ?, ?);';
					$sql->prepare($query);
					$sql->bind_param('iidds', $user_id, $task_id, $score, $timeused, $text);
					$sql->execute();
				}
			}
			else {
				error('Grade the unknown Task! task_id : ' + $task_id);
			}
		}
		else {
			$text = 'cmperr';
			$score = 0;
			$timeused = 0;
		}
		//WRITE LATEST
		$latest = latest($task_id, $user_id);
		if($latest['success']){
			$query = 'update `latest` set
				`score` = ?,
				`timeused` = ?,
				`text` = ?
				where `latest_id` = ?;';
			$sql->prepare($query);
			$sql->bind_param('ddsi', $score, $timeused, $text, $latest['latest_id']);
			$sql->execute();
		}
		else {
			$query = 'insert into `latest` (
				`latest_id`,
				`user_id`,
				`task_id`,
				`score`,
				`timeused`,
				`text`
				) values (NULL, ?, ?, ?, ?, ?);';
			$sql->prepare($query);
			$sql->bind_param('iidds', $user_id, $task_id, $score, $timeused, $text);
			$sql->execute();
		}

		degrade();
		write();
	}
	sleep(1);
}
?>
