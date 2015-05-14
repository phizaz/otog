<?php
session_start();
include ('../config.php');
include ('config.php');
include ('library.php');
if (!isLogin()) {
	include ('../../notlogin.php');
	die();
}

//FOR OFFICIAL CONTEST
// if(!isAdmin())
// {
// 	exit();
// }
//FOR OFFICIAL CONTEST

function checkAccept($user_id,$task_id)
{
	$chkApt = Database::getThat("latest","`user_id`=".$user_id." and `task_id`=".$task_id);
	if($chkApt["score"]==100)
		return true;
	else
		return false;
}
?>
<!DOCTYPE>
<head>
	<title>OTOG CONTEST SCOREBOARD</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="../admin/bootstrap.min.css">
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$("#rank_show").hide();
		$("#rank_show").load("rank.php");
		$("#rank_show").hide();
		$("#rank_show").slideDown();
		var hash="";
		setInterval(function(){
			if(hash!=location.hash)
			{
				hash=location.hash;
				$(window).scrollTop($(hash).offset().top);
			}
		});
	});
	function setTop()
	{
		location.hash="#top";
		$(window).scrollTop($("#top").offset().top);
	}
	</script>
	<style type="text/css">
	.middle{
		text-align: center;
	}
	</style>
</head>
<body id="top">
	<br>
	<div class='container'>
		<div class="navbar navbar-default">
			<div class="navber-header">
				<div class="navbar-brand">OTOG CONTEST SCOREBOARD</div>
				<ul class='nav navbar-nav'>
					<li><a href="#ranking" class="navbar-link btn-link">RANK</a></li>
					<li><a href="#task" class="navbar-link btn-link">TASK-PRIZE</a></li>
					<li><a href="#user" class="navbar-link btn-link">USER-PRIZE</a></li>
				</ul>
			</div>
		</div>
		<? if($config["show_ranking"] or isAdmin() or (($config['mode'] == 'blind_contest' or $config['mode'] == 'contest') and $config['time'] > $config['end_time'])): ?>
		<h1 id="ranking">Ranking List</h1><hr>
		<div id="rank_show"></div><br>
		<? if(!isset($_SESSION["note_check"])): ?>
		<script type="text/javascript">
		$(document).ready(function(){
			$('#close_note').click(function(){
				$('.note').hide();
				$('.note').load("note_check.php");
			});
		});
		</script>
		<div id="note" class='note' style="width:42%">
			<div class="alert alert-dismissable alert-info">
				<button type="button" class="close" id="close_note" data-dismiss="alert">×</button>
				<h4>Note!</h4>
				First Blood: The first user that passed the task.<br>
				Faster Than Light: The user that solved the task with fastest algorithm.<br>
				Passed In One: The user that passed the task in one submission.<br>
				One Man Solve: The only one user that passed the task.
			</div>
		</div>
		<? endif;?>
		<!-- <div class="note"><br><br><br><br><br></div> -->
		<h1 id="task">Task - Prize</h1>
		<div onclick="setTop()" class='btn btn-info pull-right'>TOP</div>
		<hr>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="middle">Task</th>
					<th class="middle">First Blood</th>
					<th class="middle">Faster Than Light</th>
					<th class="middle">Passed In One</th>
					<th class="middle">One Man Solve</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$user_result = Database::getAllThat("user","`level`=1");
				while($user = mysql_fetch_array($user_result))$count_prize[$user["display"]]["All"]=0;

				$result_task = Database::getAllThat("task","see = 1 ORDER BY `task_id` DESC ");
				while($task = mysql_fetch_object($result_task))
				{
					?>
					<tr>
						<td class="middle"><b><?=$task->name?></b></td>
						<td class="middle">
							<!-- First Blood -->
							<?php
							$checkExist = 0;
							$result_prize = Database::getAllThat("latest","`task_id` = ".$task->task_id." and `score`=100 ORDER BY `latest_id` ASC");
							while($prize = mysql_fetch_object($result_prize))
							{
								$user = Database::getThat("user","`user_id`='".$prize->user_id."'");
								if($user["level"]==1 and checkAccept($user["user_id"],$task->task_id))
								{
									echo $user["display"];
									$count_prize[$user["display"]]["First Blood"]++;
									$count_prize[$user["display"]]["All"]++;
									$checkExist = 1;
									break;
								}
							}
							if(!$checkExist)
								echo "-";
							?>
						</td>
						<td class="middle">
							<!-- Faster Than Light -->
							<?php
							$checkExist = 0;
							$result_prize = Database::getAllThat("latest","`task_id` = ".$task->task_id." and `score`=100 ORDER BY `timeused` ASC");
							while($prize = mysql_fetch_object($result_prize))
							{
								$user = Database::getThat("user","`user_id`='".$prize->user_id."'");
								if($user["level"]==1 and checkAccept($user["user_id"],$task->task_id))
								{
									echo $user["display"];
									$count_prize[$user["display"]]["Faster Than Light"]++;
									$count_prize[$user["display"]]["All"]++;
									$checkExist = 1;
									break;
								}
							}
							if(!$checkExist)
								echo "-";
							?>
						</td>
						<td class="middle">
							<!-- Passed In One -->
							<?php
							$checkExist = 0;
							$result_prize = Database::getAllThat("result","`task_id` = ".$task->task_id." and `score`=100 ORDER BY `result_id` ASC");
							while($prize = mysql_fetch_object($result_prize))
							{
								$user = Database::getThat("user","`user_id`='".$prize->user_id."'");
								if($user["level"]==1 and !isset($check_pio[$task->task_id][$user["user_id"]]) and checkAccept($user["user_id"],$task->task_id))
								{
									$count_submit = 0;
									$result_result = Database::getAllThat("result","`task_id` = ".$task->task_id." and `user_id`=".$user["user_id"]);
									while($result = mysql_fetch_object($result_result))
									{
										$count_submit++;
									}
									if($count_submit==1)
									{
										$check_pio[$task->task_id][$user["user_id"]]=1;
										echo $user["display"]."<br>";
										$count_prize[$user["display"]]["Passed In One"]++;
										$count_prize[$user["display"]]["All"]++;
										$checkExist = 1;
									}
								}
							}
							if(!$checkExist)
								echo "-";
							?>
						</td>
						<td class="middle">
							<!-- One Man Solve -->
							<?php
							$checkExist = 0;
							$oms[$task->task_id]["display"]="";
							$oms[$task->task_id]["count"]=0;
							$result_prize = Database::getAllThat("result","`task_id` = ".$task->task_id." and `score`=100 ORDER BY `result_id` ASC");
							while($prize = mysql_fetch_object($result_prize))
							{
								$user = Database::getThat("user","`user_id`='".$prize->user_id."'");
								if($user["level"]==1 and !isset($check_oms[$task->task_id][$user["user_id"]]) and checkAccept($user["user_id"],$task->task_id))
								{
									$check_oms[$task->task_id][$user["user_id"]]=1;
									$oms[$task->task_id]["display"]=$user["display"];
									$oms[$task->task_id]["count"]++;
									if($oms[$task->task_id]["count"]>1)
										break;
								}
							}
							if($oms[$task->task_id]["count"]==1)
							{
								echo $oms[$task->task_id]["display"];
								$count_prize[$oms[$task->task_id]["display"]]["One Man Solve"]++;
								$count_prize[$oms[$task->task_id]["display"]]["All"]++;
								$checkExist = 1;
							}
							if(!$checkExist)
								echo "-";
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<h1 id="user">User - Prize</h1>
		<div onclick="setTop()" class='btn btn-info pull-right'>TOP</div>
		<hr>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="middle">User</th>
					<th class="middle">First Blood</th>
					<th class="middle">Faster Than Light</th>
					<th class="middle">Passed In One</th>
					<th class="middle">One Man Solve</th>
					<th class="middle">All</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($count_prize as $key => $value) 
					$count_prize[$key]["name"]=$key;
				usort($count_prize, function($b, $a) {
				    return $a['All'] - $b['All'];
				});
				foreach ($count_prize as $key => $value) {
					?>
					<tr>
						<td class="middle"><b><?=$value["name"]?></b></td>
						<td class="middle"><?=($value["First Blood"])?$value["First Blood"]:'0'?></td>
						<td class="middle"><?=($value["Faster Than Light"])?$value["Faster Than Light"]:'0'?></td>
						<td class="middle"><?=($value["Passed In One"])?$value["Passed In One"]:'0'?></td>
						<td class="middle"><?=($value["One Man Solve"])?$value["One Man Solve"]:'0'?></td>
						<td class="middle"><?=($value["All"])?$value["All"]:'0'?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<br>
		<? elseif($config['mode'] == 'blind_contest' or $config['mode'] == 'contest'): ?>
		<br>
		<center>
			<h1>THE CONTEST IS HAPPENING.</h1>
			<h3>We will announce the prize after the contest has ended.</h3>
		</center>
		<? else: ?>
		<br>
		<center>
			<h1>THE CONTEST DOES NOT EXIST.</h1>
			<h3>We will announce the prize after the contest ended.</h3>
		</center>
		<? endif; ?>
	</div>
</body>