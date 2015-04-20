<?php
session_start();
// session_destroy();
// $_SESSION[$config['name_short']]['user_id'] = 153;
include("addon/config.php");
include("config.php");
include("addon/library.php");
$result_task = Database::getAllThat("task","`see` = 1");
$count["all"] = 0;
$count["pass"] = 0;
$count["not_pass"] = 0;
$count["not_submit"] = 0;
$count["new"] = 0;
while($task = mysql_fetch_array($result_task))
{
	$count["all"]++;
	$task_pass = Database::getThat("pass","`task_id` = ".$task["task_id"]." and `user_id` = ".$_SESSION[$config['name_short']]['user_id']);
	if(isset($task_pass["pass_id"]))
		$count["pass"]++;
	else
	{
		$task_not_pass = Database::getThat("result","`task_id` = ".$task["task_id"]." and `user_id` = ".$_SESSION[$config['name_short']]['user_id']);
		if(isset($task_not_pass["result_id"]))
			$count["not_pass"]++;
		else
			$count["not_submit"]++;
	}
	if(date("m-d-y",$task["see_date"])==date("m-d-y",time()))
		$count["new"]++;
}
?>
<script type="text/javascript">
function select_type(input)
{
	location.hash = "#main/task";
	var cnt_inter = 0;
	var t_inter = setInterval(function(){
		cnt_inter++;
		// $("#can").text(cnt_inter);
		$(".task").hide();
		if(input=="passed")
			$(".passed").show();
		else if(input=="tried")
			$(".tried").show();
		else if(input=="nosub")
			$(".nosub").show();
		else if(input=="new")
			$(".new").show();
		else
			$(".task").show();
		$("#chk_interval").text(input);
		if($("#chk_interval").text() == input)
			clearInterval(t_inter);
	});
}
</script>
<style type="text/css"> 
	.cnt_msg{
		font-family: Calibri;
 	  	font-size: 15px;
 	  	margin-top:10px;
 	  	margin-bottom:0px;
 	   	text-align: center;
 	   	font-weight:600;
	} 
	.cnt_num{
		font-family: Calibri;
 	   	font-size: 50px;
 	  	margin-top:-5px;
 	   	font-weight:100;
 	   	text-align: center;
	}
	.font_white {
		color: #FFFFFF;
	}
	.font_gray {
		color: #111111;
	}
	.count_btn {
		border-radius: 6px;
		display: inline-block;
		height: 86px;
		margin: 0 0px;
  		padding: 0px 0 0;
  		width: 115px;
  		margin-top:10px;
  		opacity: 0.8;
	}
	.count_btn.blue{
		background-color:#17B4E9;
	}
	.count_btn.green{
		background-color:#41E241;
	}
	.count_btn.red{
		background-color:#FF4D4D;
	}
	.count_btn.org{
		background-color:#FFAD33;
	}
	.count_btn.gray{
		background-color:rgb(200,200,200);
	}
	</style>
	<div class='count_btn gray'>
		<a href="javascript:select_type('all')">
			<div class='cnt_msg font_gray'>ทั้งหมด</div>
			<div class='cnt_num font_gray'><?=$count["all"]?></div>
		</a>
	</div>
	<div class='count_btn green'>
		<a href="javascript:select_type('passed')">
			<div class='cnt_msg font_white'>ผ่านแล้ว</div>
			<div class='cnt_num font_white'><?=$count["pass"]?></div>
		</a>
	</div>
	<div class='count_btn red'>
		<a href="javascript:select_type('tried')">
			<div class='cnt_msg font_white'>ยังไม่ผ่าน</div>
			<div class='cnt_num font_white'><?=$count["not_pass"]?></div>
		</a>
	</div>
	<div class='count_btn org'>
		<a href="javascript:select_type('nosub')">
			<div class='cnt_msg font_white'>ยังไม่ส่ง</div>
			<div class='cnt_num font_white'><?=$count["not_submit"]?></div>
		</a>
	</div>
	<div class='count_btn blue'>
		<a href="javascript:select_type('new')">
			<div class='cnt_msg font_white'>โจทย์วันนี้</div>
			<div class='cnt_num font_white'><?=$count["new"]?></div>
		</a>
	</div>