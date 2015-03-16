<?php
session_start();
include ('../config.php');
include ('config.php');
include ('library.php');
if (!isLogin()) {
	include ('../notlogin.php');
	die();
}
?>
<div class="modal" style="top:30%; height: 30%; color:#565656">
	<div class="modal-content" style="background:#D9D9D9; margin-left: 40%;margin-right: 40%;height: 100%;">
		<div class='modal-close'>
			<a href="javascript:closeModal('who_pass')"><img class='modal-close-icon' src="addon/img/close-icon.png"></a>
		</div>
		<p><b class="who_pass_pre" style="padding-left:10px;padding-right:10px">รายชื่อผู้ที่ทำผ่าน</b></p>
		<div class="who_pass_pre" style="background:#FFFFFF;border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;height:85%;overflow:auto;padding-left:10px;padding-right:10px">
			<?php
			$task = Database::getById("task",$_GET["task_id"]);
			if($task['see']==1 or isAdmin())
			{
				if($config['mode'] == 'blind_contest' or $config['mode'] == 'contest')
				{
					exit();
				}
				$result = Database::getAllThat("pass","`task_id` = ".$_GET["task_id"]);
				echo "<table class='who_pass_pre' style='list-style-type: circle; width:100%'>";
				while($pass = mysql_fetch_array($result))
				{
					$user = Database::getUser($pass["user_id"]);
					if($user["level"]==1)
					{
						echo "<tr><td style='style:5%'><td style='width:95%' class='who_pass_pre'>".$user["display"]."</td></tr>";
					}
				}
				echo "</div>";
			}
			?>
		</div>
	</div>
</div>