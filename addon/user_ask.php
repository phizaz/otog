<?php
session_start();
include ('../config.php');
include ('config.php');
include ('library.php');
if (!isLogin()) {
	include ('../../notlogin.php');
	die();
}

?>

<head>
	<title>ถาม - ตอบ</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="../admin/bootstrap.min.css">
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript">
	function load_ans(ask_id)
	{
		$("#ask_"+ask_id).load("ask_ans.php?ask_id="+ask_id);
		setInterval(function(){
			$("#ask_"+ask_id).load("ask_ans.php?ask_id="+ask_id);
		},1000);
	}
	$(document).ready(function(){
	});
	</script>
</head>
<body>
	<br>
	<div class='container'>
		<div class="navbar navbar-default">
			<div class="navber-header">
				<div class="navbar-brand">Q&A</div>
			</div>
		</div>
		<div class='row'>
			<div class='col-md-3'></div>
			<div class='col-md-6'>
				<div class="panel panel-default">
					<div class="panel-heading"><b>ถาม</b></div>
					<div class="panel-body">
						<form method="post" action="ask_active.php?op=ask">
							<textarea class='form-control' style='height:75' name="detail"></textarea>
							<input class='btn btn-primary btn-sm pull-right' type='submit' value='ส่งคำถาม'>
						</form>
					</div>
				</div>
				<?php
				$result = Database::getAllThat("ask","`sub_ask` = 0 and `user_id` = ".$_SESSION[$config['name_short']]['user_id']. " ORDER BY  `ask`.`ask_id` DESC ");
				while($ask = mysql_fetch_array($result))
				{
					?>
					<div class="panel panel-default">
						<div class="panel-heading">#<?=$ask["ask_id"]?></div>
						<div class="panel-body">
							<?=$ask["detail"]?>
						</div>
						<div class="panel-footer" id="ask_<?=$ask['ask_id']?>" onclick="load_ans('<?=$ask['ask_id']?>')">
							<submit class="btn btn-primary btn-sm"><b>ดูคำตอบ</b></submit>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</body>