<?php
session_start();
include ('../config.php');
include ('config.php');
include ('library.php');
if (!isLogin()) {
	include ('../../notlogin.php');
	die();
}

$chk = Database::getById("ask",$_GET["ask_id"]);
if($chk["user_id"]!=$_SESSION[$config['name_short']]['user_id'])
{
	die();
	exit();
}
?>
<div class="panel panel-default">
	<div class="panel-body">
		<?php
		$res = Database::getAllThat("ask","`sub_ask` = ".$_GET['ask_id']);
		$count_hr = 0;
		while($sub = mysql_fetch_array($res))
		{
			if($count_hr++!=0)
				echo "<hr>";
			echo $sub['detail'];
		}
		if($count_hr==0)echo "<span class='label label-default'>กำลังรอการตอบกลับ ระบบจะแสดงคำตอบทันทีที่มีการตอบกลับ ...</span>";
		?>
	</div>
</div>