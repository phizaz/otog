<?
session_start();
include('config.php');
if (!isLogin()){
	include('notlogin.php');
	die();
}
if(!inTime()){
	include('timeout.php');
	die();
}
?>
<link rel="stylesheet" type="text/css" href="/addon/css/modal.css">
<script type="text/javascript">
	$(document).ready(function(){
		$('#code_watcher').hide();
	});
	function compiler_message(id){
		$('#compiler_message-'+id).slideToggle('fast');
	}
	function code_watch(task,user){
		showModal('code_watcher');
		$('#code_watcher').load("/addon/code_watcher.php?task="+task+"&user="+user);
	}
	function showModal(id)
	{
		$('#'+id).fadeIn('fast');
		$("body").css("overflow", "hidden");
	}
	function closeModal(id)
	{
		$('#'+id).fadeOut('fast');
		$("body").css("overflow", "auto");
	}
	$(document).ready(function(){
		$('html').click(function(e) {   
			if( !$(e.target).hasClass('code'))
			{
				closeModal('code_watcher');
			}
		});
	});
</script>
<div style="height: 20px;"></div>
<div id="result" class="container_12">	
	<style>
	.table {
		display: table;
		width: 100%;
	}
	.row {
		display: table-row;
	}
	.row:hover:not(:nth-child(1)){
		background: rgb(253,255,203) !important;
	}
	.row:nth-child(2n){
		background: rgb(240,240,240);
	}
	.cell {
		display: table-cell;
		vertical-align: middle;
		padding: 5px;
	}
	.compiler_message{
		display: none;
		text-align: left;
	}
	.fast-uploader {
		float: right;
	}
	.fast-uploader h4 {
		float: left;
		width: 300px;
		text-align: right;
		margin: 0px 15px 0px 0px;
	}
	.fast-uploader small {
		font-weight: normal;
	}
	.fast-uploader div {
		width: 150px;
		float: left;
	}
	.fast-uploader iframe {
		width: 150px;
		height: 50px;
	}
	</style>
	<div class="modal-container" id="code_watcher"></div>
	<div class="grid_12" id="result_list">
		<div class="fast-uploader">
			<?php 
			$my = $_SESSION[$config['name_short']];
			$latest_task = null;

			//Search in Queue
			$query = 'select `task_id` from `queue` where `user_id` = ' . $my['user_id'] . ' order by `queue_id` desc limit 1';
			$sql -> prepare($query);
			$sql -> execute();
			$sql -> bind_result($task_id);
			while ($sql -> fetch() ) {
				$latest_task = $task_id;
			}

			if($latest_task == null) {
				//Search in Grading
				$query = 'select `task_id` from `grading` where `user_id` = ' . $my['user_id'] . ' order by `grading_id` desc limit 1';
				$sql -> prepare($query);
				$sql -> execute();
				$sql -> bind_result($task_id);
				while ($sql -> fetch() ) {
					$latest_task = $task_id;
				}
			}

			if($latest_task == null) {
				//Search in Result
				$query = 'select `task_id` from `result` where `user_id` = ' . $my['user_id'] . ' order by `result_id` desc limit 1';
				$sql -> prepare($query);
				$sql -> execute();
				$sql -> bind_result($task_id);
				while ($sql -> fetch() ) {
					$latest_task = $task_id;
				}
			}

			if($latest_task != null) {
				$query = 'select `name`, `name_short` from `task` where `task_id` = ' . $latest_task;
				$sql -> prepare($query);
				$sql -> execute();
				$sql -> bind_result($task_name, $task_name_short);

				$latest_task_name = null;
				$latest_task_name_short = null;
				while($sql -> fetch()) {
					$latest_task_name = $task_name;
					$latest_task_name_short = $task_name_short;
				}
			}
			 ?>
			<?php if($latest_task != null): ?>
				<h4>ส่งข้อล่าสุด <?=$latest_task_name?> <small>(<?=$latest_task_name_short?>)</small></h4>
				<div><iframe src="upload.php?id=<?=$latest_task?>&fast=true" frameborder="0"></iframe></div>
			<?php endif; ?>
		</div>
		<div class="table">
			<div class="row" style="text-align: center; font-weight: bold;">
				<div class="cell" style="width: 50px;">
					#
				</div>
				<div class="cell" style="width: 150px;">
					เวลา
				</div>
				<div class="cell" style="width: 100px;">
					ผู้ส่ง
				</div>
				<div class="cell" style="width: 150px;">
					ข้อ
				</div>
				<div class="cell">
					ผลตรวจ
				</div>
				<div class="cell" style="width: 100px;">
					ได้คะแนน
				</div>
				<div class="cell" style="width: 100px;">
					เวลารวม
				</div>
			</div>
			<?

			$addition = ' where `user_id` = ? ';
			if(isAdmin()) $addition = ''; 

			$query = 'select `user_id`, `task_id`, `time` from `queue` '.$addition.' order by `queue_id` desc;';
			$sql->prepare($query);
			if(!isAdmin()) $sql->bind_param('d', $_SESSION[$config['name_short']]['user_id']);
			$sql->execute();
			@$sql->bind_result($user_id, $task_id, $time);

			while($sql->fetch()){
				$user = user($user_id);
				$task = task($task_id);
				echo '
				<div class="row" style="text-align: center;">
					<div class="cell">
						-
						<a href="javascript:code_watch(\''.$task["task_id"].'\',\''.$user["user"].'\')"><img style="height:30px" src="/addon/img/code_icon.png"></a>
					</div>
					<div class="cell">
						' . D('d m y H:M:S', $time). '
					</div>
					<div class="cell">
						' . $user['display'] . '
					</div>
					<div class="cell">
						' . $task['name'] . '
					</div>
					<div class="cell">
						รอตรวจ..
					</div>
					<div class="cell">
						-
					</div>
					<div class="cell">
						-
					</div>
				</div>
				';
			}
			
			$query = 'select `user_id`, `task_id`, `time` from `grading` '.$addition.' order by `grading_id` desc;'; 
			$sql->prepare($query);
			if(!isAdmin()) $sql->bind_param('d', $_SESSION[$config['name_short']]['user_id']);
			$sql->execute();
			$sql->bind_result($user_id, $task_id, $time);

			while($sql->fetch()){
				$user = user($user_id);
				$task = task($task_id);
				echo '
				<div class="row" style="text-align: center;">
					<div class="cell">
						-
						<a href="javascript:code_watch(\''.$task["task_id"].'\',\''.$user["user"].'\')"><img style="height:30px" src="/addon/img/code_icon.png"></a>
					</div>
					<div class="cell">
						' . D('d m y H:M:S', $time) . '
					</div>
					<div class="cell">
						' . $user['display'] . '
					</div>
					<div class="cell">
						' . $task['name'] . '
					</div>
					<div class="cell">
						กำลังตรวจ..
					</div>
					<div class="cell">
						-
					</div>
					<div class="cell">
						-
					</div>
				</div>
				';
			}
	
			$query = 'select `result_id`, `user_id`, `task_id`, `time`, `text`, `score`, `timeused`, `message` from `result` '.$addition.' order by `result_id` desc limit 100;';
			$sql->prepare($query);
			if(!isAdmin()) $sql->bind_param('d', $_SESSION[$config['name_short']]['user_id']);
			$sql->execute();
			$sql->bind_result($result_id, $user_id, $task_id, $time, $text, $score, $timeused, $message);

			while($sql->fetch()){
				$user = user($user_id);
				$task = task($task_id);
				echo '
				<div class="row" style="text-align: center;">
					<div class="cell">
						' . $result_id . '
						<a href="javascript:code_watch(\''.$task["task_id"].'\',\''.$user["user"].'\')"><img style="height:30px" src="/addon/img/code_icon.png"></a>
					</div>
					<div class="cell">
						' . D('d m y H:M:S', $time) . '
					</div>
					<div class="cell">
						' . $user['display'] . ' '. ($my['level'] == 0 ? '('.$user['user'].')' : ''). '
					</div>
					<div class="cell">
						<a href="doc/'. $task['name_short'] .'.pdf" target="_blank">' . $task['name'] . '</a>
					</div>
					<div class="cell">
						';
				if($text == 'cmperr'){
					echo '<a href="javascript:compiler_message('.$result_id.');">คอมไฟล์เออเร่อ</a>';
				}
				else if($text == 'err'){
					echo '<a href="javascript:compiler_message('.$result_id.');">มีปัญหาในการตรวจ</a>';
				}
				else {
					if(isBlind()) $text = $singlecase[substr($text, 0, 1)];  
					echo $text;
				}
				$compiler_message = str_replace('<', '&lt;', $compiler_message);
				$compiler_message = str_replace('>', '&gt;', $compiler_message);

				echo '
						<div class="compiler_message" id="compiler_message-'. $result_id. '">'.$message.'</div>
					</div>
					<div class="cell">';
				if(isBlind()) echo '-';
				else printf("%.2lf", $score); 
				echo '
					</div>
					<div class="cell">
				';
				if(isBlind()) echo '-';
				else printf("%.2lf",$timeused);		
				echo '
					</div>
				</div>
				';
			}
			?>
		</div>
	</div>
</div>
<div style="height: 20px;"></div>