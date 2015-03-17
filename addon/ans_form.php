<?php
session_start();
include ('../config.php');
include ('config.php');
include ('library.php');
if (!isLogin()) {
	include ('../notlogin.php');
	die();
}

if(!isAdmin())
{
	die();
	exit();
}

$ask_info = Database::getById("ask",$_GET["ask_id"]);
$ask_user = Database::getById("user",$ask_info["user_id"]);
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<b>#<?=$_GET["ask_id"]?> - <?=$ask_user["display"]?></b>
	</div>
	<div class="panel-body">
		<?=$ask_info["detail"]?>
	</div>
	<div class="panel-footer">
		<form method="post" action="ask_active.php?op=answer">
			<textarea class="form-control" name="detail"></textarea>
			<input type="submit" class="btn btn-sm btn-primary" value="ตอบกลับ">
			<input type="hidden" name="ask_id" value="<?=$_GET["ask_id"]?>">
		</form>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<b>คำตอบก่อนหน้า</b>
	</div>
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
		if($count_hr==0)echo "ยังไม่มีการตอบกลับ";
		?>
	</div>
</div>