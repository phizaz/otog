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

<div style="height: 20px;"></div>
<div id="task" class="container_12">	
	<style>
	.table {
		display: table;
		width: 100%;
	}
	.row {
		display: table-row;
		text-align: center;
		height: 40px;
	}
	.row.passed{
		/*background: rgb(253,255,204);*/
		background: rgb(235,255,235);
	}
	.row.tried{
		background:rgb(255,235,235);
		/*background:rgb(240,240,240);*/
	}
	.row.latest{
		background: rgb(0,0,0); color: rgb(255,255,255) !important;
	}
	.cell {
		display: table-cell;
		vertical-align: middle;
		padding: 5px;
		border-bottom: 1px solid rgb(230,230,230);
		word-wrap: break-word;
	}
	.red {
		color : red;
	}
	.green {
		color : green;
	}
	.editor {
		display : none;
	}
	.editor input[type='text'] {
		font-size: 14px;
		width: 40px;
	}
	.like{
		position: relative; color: rgb(97,118,175); font-size: 12px;
	}
	.like .names {
		display: none;
		background: rgba(0,0,0,0.85); border-radius: 3px; position: absolute; top: 20px; color: rgb(255,255,255); white-space: nowrap;
	}
	.like:hover .names {
		display: block;
	}
	.like .names .name {
		padding: 4px;
	}
	</style>
	<div class="grid_12">
		<div class="table">
			<div class="row" style="text-align: center; font-weight: bold;">
				<div class="cell" style="width: 70px;">
					ข้อที่
				</div>
				<div class="cell" style="width: 200px;">
					ชื่อ
				</div>
				<div class="cell" style="width: 70px;">
					คะแนนเต็ม
				</div>
				<div class="cell" style="width: 150px;">
					ล่าสุด
				</div>
				<div class="cell">
					คุณส่งแล้ว
				</div>
				<?
				if(!isBlind()) echo '<div class="cell">ผ่านแล้ว</div>';
				if(isAdmin()) echo '<div class="cell">เห็น?</div>';
				?>
				<div class="cell" style="width: 140px;">
					ส่ง
				</div>
			</div>
			<?

			$addition = ' where `see` = 1 ';
			if(isAdmin()) $addition = '';
			$query = 'select `task_id`, `name`, `name_short`, `score`, `time_limit`, `mem_limit`, `see`, `see_date` from `task` '. $addition .' order by `task_id` desc;';
			$sql->prepare($query);
			$sql->execute();
			$sql->bind_result($task_id, $name, $name_short, $score, $time_limit, $mem_limit, $see, $see_date);

			$a = newSqli();
			$b = $a->stmt_init();
			$query = 'select `task_id` from `result` where `user_id` = ? order by `result_id` desc limit 1;';
			$b->prepare($query);
			$b->bind_param('i', $my['user_id']);
			$b->execute();
			$b->bind_result($latest_task_id);
			if(!$b->fetch()) $latest_task_id = -1;
			
			if(!isBlind()){
				$query = 'select `task_id` from `pass` where `user_id` = ?;';
				$b->prepare($query);
				$b->bind_param('i', $my['user_id']);
				$b->execute();
				$b->bind_result($pass_task_id);
				
				$passed = array();
				while($b->fetch()){	
					$passed[$pass_task_id] = true;	
				}
			}
			
			while($sql->fetch() ){
				// Old-fashion obsoleted.
				// $link = 'doc/' . $name_short . '.pdf';
				// New fashion.
				$link = 'doc.php?id=' . $task_id;
				$latest = latest($task_id, $my['user_id']);
				
				$query = 'select `user_id` from `like` where `task_id` = ?;';
				$b->prepare($query);
				$b->bind_param('i', $task_id);
				$b->execute();
				$b->bind_result($like_user_id);
				$like_count = 0;
				$like_names = '';
				$first = true;
				$liked = 0;
				while($b->fetch()){
					$like_count++;
					if($like_user_id == $my['user_id']) $liked = 1;
					$user = user($like_user_id);
					$like_names .= '<span class="name" style="'.($first ? '' : 'padding-left: 0px;').'">'.($first ? '' : ',').' '.$user['display'].'</span>';
					$first = false;
				}
				
				$query = 'select * from `result` where `user_id` = ? and `task_id` = ?;';
				$b->prepare($query);
				$b->bind_param('ii', $my['user_id'], $task_id);
				$b->execute();
				$b->store_result();
				$submit_count = $b->num_rows;
				
				$query = 'select `user_id` from `pass` where `task_id` = ? and (`user_id`) in (select `user_id` from `user` where `level` = 1);';
				$b->prepare($query);
				$b->bind_param('i', $task_id);
				$b->execute();
				$b->store_result();
				$pass_count = $b->num_rows;
				
				$class_task = ((date("m-d-y",time()) == date("m-d-y",$see_date)) ? 'new ':'').($submit_count > 0 ? ($passed[$task_id] == true ? 'passed' : 'tried') : 'nosub').($task_id == $latest_task_id ? ' latest':'');
				if($config["mode"]!="online")
					$class_task = '';

				echo '
			<div class="row task '.$class_task.'">
				<div class="cell">
					' . $task_id . ' '.($task_id == $latest_task_id ? '(ล่าสุด)' : '').'
				</div>
				<div class="cell" align="left">
					<a href="' . $link . '" target="_blank">' . $name . (isAdmin() ? ' ('. $name_short .') ' : '') . '<br>';
				printf("(%.1lf s., %d MB.)", $time_limit, $mem_limit);
				echo '</a>
					<div style="float: right;"><a href="javascript:like('.$task_id.');"><img src="addon/img/like.png"></a> <span id="like_'.$task_id.'" class="like" liked="'.$liked.'"><div class="names" likecount="'.$like_count.'">'.$like_names.'</div><span class="likecount">'.($like_count == 0 ? 'ไม่มี' : $like_count . ' คน').'</span></span></div>
				</div>
				<div id="score_'.$task_id.'" class="cell score">
				';
				if(isAdmin()):
					echo '
					<div class="display" value="'.$score.'"><a class="number" href="javascript:editScore('.$task_id.');" title="คลิ๊กเพื่อแก้ไขคะแนน">' . $score . '</a></div>
					<div class="editor">
						<form onsubmit="return changeScore(this, '.$task_id.');">
							<input id="score_editor_'.$task_id.'" name="score" type="text">
							<input type="hidden" name="task_id" value="'.$task_id.'">
						</form>
					</div>';
				else:
					echo $score;
				endif;
				
				if(isBlind() ) {
					$latest['text'] = $singlecase[substr($latest['text'], 0, 1)];
				}
				if(!$latest['success']) $latest['text'] = 'คุณยังไม่ได้ส่ง';
				
				echo '
				</div>
				<div class="cell">
					' . $latest['text'] . ($latest['success'] && !isBlind() ? ' (' . $latest['score'] . '%)' : '') . '
				</div>
				<div class="cell">
					' . $submit_count . '
				</div>';
				if(!isBlind()) {
				echo '
				<div class="cell">
					' . $pass_count . '
				</div>
				';
				}
				if(isAdmin()){
					echo '<div class="cell"><a id="see_'.$task_id.'" href="javascript:toggleSee('.$task_id.');" class="'.($see == 0 ? 'red' : 'green').'" see="'.$see.'" title="คลิ๊กเพื่อสลับการมองเห็น">'.($see == 0 ? 'ไม่เห็น' : 'เห็น').'</a></div>';
				}
				echo '
				<div id="submit_'.$task_id.'" class="cell upload '.($task_id == $latest_task_id ? 'latest' : '').'">
					<a href="javascript:submit('.$task_id.');"><h4 style="margin: 0px; padding: 0px; width: 140px; height: 40px; display: table-cell; vertical-align: middle;">ส่ง</h4></a>
				</div>
			</div>
			';
			}
			?>
		</div>
		<script type="text/javascript">
			function submit(task_id){
				$('#submit_'+task_id).html('<iframe src="upload.php?id=' + task_id + '" height="40px" width="140px" scrolling="no" frameborder="0"></iframe>');
			}
			function editScore(task_id){
				var scoreObj = $('#score_' + task_id);
				scoreObj.children('.display').hide();
				scoreObj.children('.editor').show();
				$('#score_editor_' + task_id).val( scoreObj.children('.display').attr('value') );
			}
			function like(task_id){ 
				var like_obj = $('#like_'+task_id);
				if(like_obj.attr('liked') == 0){
					like_obj.attr('liked',1);
					$.ajax({
						url : 'addon/like.php',
						type : 'post',
						data : 'task_id=' + task_id, 
						success : function (html){
							var likecount_obj = like_obj.children('.likecount');
							var like_names_obj = like_obj.children('.names');
							var like_count = like_names_obj.attr('likecount');
							like_count++;
							like_names_obj.attr('likecount',like_count);
							likecount_obj.html(like_count + ' คน');
							like_names_obj.append('<span class="name" style="'+(like_count == 1 ? '' : 'padding-left: 0px;')+'">'+(like_count == 1 ? '' : ', ')+'<?=$my['display']?></span>');
						}
					});
				}
			}
			function changeScore(form, task_id){
				load();
				$.ajax({
					url : 'task_change_score.php',
					type : 'post',
					data : $(form).serialize(), 
					success : function (html){
						unload();
						var scoreObj = $('#score_' + task_id);
						scoreObj.children('.display').attr('value', html).show().children('.number').html(html);
						scoreObj.children('.editor').hide();
					}
				});
				return false;
			}
			function toggleSee(task_id){
				load();
				$.ajax({
					url : 'task_toggle_see.php', 
					type : 'post', 
					data : 'task_id=' + task_id,
					success : function (html){
						unload();
						var target = $('#see_'+task_id);
						var see = target.attr('see');
						var newsee = see == 1 ? 0 : 1;
						target
							.removeClass( (newsee == 1 ? 'red' : 'green') )
							.addClass( (newsee == 1 ? 'green' : 'red') )
							.html( (newsee == 1 ? 'เห็น' : 'ไม่เห็น') )
							.attr('see', newsee);
					}  
				});
			}
		</script>
	</div>
</div>
<div style="height: 20px;"></div>
<div id="chk_interval" style="display:none">all</div>