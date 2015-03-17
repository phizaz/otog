<?php
session_start();
include ('../config.php');
include ('config.php');
include ('library.php');

if(!isAdmin())
{
	die();
	exit();
}

?>

<head>
	<title>ถาม - ตอบ</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="../admin/bootstrap.min.css">
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$("#ask_list").load("ask_list.php");
		setInterval(function(){
			$("#ask_list").load("ask_list.php");
		},1000);
	});

	function load_ans(ask_id)
	{
		$("#answer_form").load("ans_form.php?ask_id="+ask_id);
	}
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
			<div class='col-md-6' id="answer_form">
				<br>
				<center><h1 style="color:#565656">เลือกคำถาม</h1></center>
			</div>
			<div class='col-md-6'>
				<div class="panel panel-default">
					<div class="panel-heading"><b>คำถาม</b></div>
						<div class="panel-body" style="height:70%; overflow:auto" id="ask_list">
						</div>
						<div class="panel-footer"></div>
					</div>
			</div>
		</div>
	</div>
</body>