<?
session_start();
include('../config.php');
if (!isLogin()){
	include('../notlogin.php');
	die();
}
if((!isAdmin() && $config['mode'] == 'online') and !$config["show_ranking"]){
	die('Online mode has no ranking.');
}
?>
<style>
table {
	width: 100%;
	border-spacing: 0;
	border-collapse:collapse;
}
table h4{
	margin: 0px;
}
table tr:hover:not(:nth-child(1)){
	background: #abebfe !important;
}
table tr:nth-child(2n+1):not(:nth-child(1)){
	background: rgb(240,240,240);
}
table tr td {
	vertical-align: middle;
	text-align: center;
	padding: 10px;
}
.detail {
	/*display: none;*/
	font-size: 11px;
}
.detail_wrapper{
	padding: 0px;
}
.option {
	padding: 10px;
	float: left;
}
.option:hover {
	background: rgb(255,255,255);
}
</style>
<div id="rank" class="container_12">
<? if($config['mode'] == 'blind_contest' && inTime() && !isAdmin()):?>
	<div class="grid_12" align="center">
		<h1>จะมีการจัดอันดับเมื่อการแข่งขันจบ</h1>
	</div>
<? else: 

	$query = 'select * from `latest` where (`user_id`) in (select `user_id` from `user` where `level` = 1) order by `user_id` asc, `task_id` asc;';
	$sql->prepare($query);
	$sql->execute();
	$sql->bind_result($latest_id, $user_id, $task_id, $score, $timeused, $text);
	
	$a = newSqli();
	$b = $a->stmt_init();

	$cnt = -1;
	$old_id = -1;
	$info = array(array());
	while($sql->fetch()){
		if($user_id != $old_id){
			$old_id = $user_id;
			$cnt++;
			$info[$cnt] = user($user_id);
			$info[$cnt]['score'] = 0;
			$info[$cnt]['timeused'] = 0;
			$info[$cnt]['task'] = array();
		}
		$task = task($task_id);
		if($task['see'] == 1){
			$info[$cnt]['score'] += $score / 100.0 * $task['score'];
			$info[$cnt]['timeused'] += $timeused;
			
			$query = 'select * from `result` where `user_id` = ? and `task_id` = ?;';				
			$b->prepare($query);
			$b->bind_param('ii', $user_id, $task_id);
			$b->execute();
			$b->store_result();
			$submit_count = $b->num_rows;
			$info[$cnt]['task'][] = array('task_id' => $task['task_id'], 'task_name' => $task['name'], 'task_name_short' => $task['name_short'], 'task_score' => $task['score'], 'score' => $score, 'timeused' => $timeused, 'text' => $text, 'submit_count' => $submit_count);
		}
	}
	function cmp($a, $b){
		if(abs($a['score'] - $b['score']) < 0.0001)
			if($a['timeused'] == $b['timeused']) return 0;
			else return $a['timeused'] < $b['timeused'] ? -1 : 1;
		return $a['score'] < $b['score'] ? 1 : -1;
	}
	usort($info, 'cmp');
	
	$col_count = 4;
?>
	<meta http-equiv="content-Type" content="text/html; charset=utf-8">
	<div>
		<!-- <a href="javascript: showAll();"><h4 id="showAll" class="option">แสดงรายละเอียด</h4></a> <a href="javascript: hideAll();"><h4 id="hideAll" class="option">ซ่อนรายละเอียด</h4></a> -->
	</div>
	<table>
		<tr>
			<td>
				<h4>#</h4>
			</td>
			<? if($config['ranking_show_user_id']): $col_count++; ?>
			<td>
				<h4>User</h4>
			</td>
			<? endif; ?>
			<td>
				<h4>Task</h4>
			</td>
			<td>
				<h4>Score</h4>
			</td>
			<td>
				<h4>Running Time</h4>
			</td>
		</tr>
			<?
			$maxFont = 50;
			$minFont = 16;
			$now = $maxFont;
			$rank = 1;
			$small_factor = 0.70;
			$maxRank = $config['ranking_count'] == 0 ? 2000000000 : $config['ranking_count'];
			for ($i = 0; $i <= $cnt && $rank <= $maxRank; $i++) {
				echo '
				<tr style="font-size: ' . $now . 'px;" detail="detail_'.$info[$i]['user_id'].'">
					<td>
					' . $rank . '
					</td>';
				if ($config['ranking_show_user_id']) {
					echo '
					<td>
					' . $info[$i]['user'] . '
					</td>';
				}
				echo '
					<td>
					' . $info[$i]['display'] . '
					</td>
					<td>
					' . $info[$i]['score'] . '
					</td>
					<td>
					';
				printf("%.2lf", $info[$i]['timeused']);
				echo '
					</td>
				</tr>
				';
				echo '
				<tr>
					<td colspan="'.$col_count.'" class="detail_wrapper">
					<div id="detail_'.$info[$i]['user_id'].'" class="detail">
					';
				foreach($info[$i]['task'] as $task){
					echo '
					<div style="float: left; width: 16.66%;" align="center"><div style="padding: 5px;">
						<div style="font-weight: bold;">'.$task['task_id'].'. <a href="../doc/'.$task['task_name_short'].'.pdf" target="_blank">'.$task['task_name'].'</a></div>
						<div>'.$task['text'].'</div>
						<div>Score '.($task['score'] / 100.0 * $task['task_score']).' / '.$task['task_score'].'</div>
						<div>Time ';printf("%.2lf", $task['timeused']);echo ' Submitted '.$task['submit_count'];
					echo '</div>
						</div>
					</div>';
				}
				echo '
					</div>
					</td>
				</tr>
				';
				if ($i < $cnt - 1 && $info[$i]['score'] == $info[$i + 1]['score']) {
				} else {
					$rank++;
					$now = $now * $small_factor > $minFont ? $now * $small_factor : $minFont;
				}
			}
		?>
	</table>	
	<script type="text/javascript">
		function showAll(){
			$('#showAll').css('background', 'rgb(255,255,255)');
			$('#hideAll').css('background', 'none');
			$('.detail').slideDown('fast');
		}
		function hideAll(){
			$('#hideAll').css('background', 'rgb(255,255,255)');
			$('#showAll').css('background', 'none');
			$('.detail').slideUp('fast');
		}
		hideAll();
		$('tr').click(function(){
			$('#' + $(this).attr('detail') ).slideToggle('fast');
		});
	</script>	
</div>
<? endif; ?>
</div>