<?php
session_start();
if(!isset($_SESSION['user_id']))
	echo "<meta http-equiv='refresh' content='0; login.php'/>";
$page = "main";
if(isset($_REQUEST['page']))
{
	$page = $_REQUEST['page'];
}
?>
<head>
	<title>ADMIN ONLY</title>
	<!-- Latest compiled and minified CSS -->
	<!-- <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"> -->
	<link rel="stylesheet" href="bootstrap.min.css">
	<style type="text/css">
	.btn-link:link,
	.btn-link:visited
	{text-decoration:none;}
	.btn-link:hover,
	.btn-link:active
	{text-decoration:underline;}
	.navbar-brand:hover,
	.navbar-brand:active
	{text-decoration:none;}
	</style>

	<!-- Optional theme -->
	<!-- <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css"> -->

	<meta http-equiv="content-Type" content="text/html; charset=utf-8">

	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

	<script src="http://scius.kku.ac.th/scripts/jquery-1.9.1.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#main').load('<?=$page?>_config.php');
	});
	function goPage(page)
	{
		$('#main').load(page+'.php');	
	}
	</script>
</head>
<body>
	<header>
		<div class='navbar navbar-inverse navbar-static-top'>
			<div class='container'>
				<div class='navbar-header'>
					<a class='btn-link navbar-brand' onclick="goPage('main_config')">OTOG</a>
					<ul class='nav navbar-nav'>
						<li><a class='navbar-link btn-link' onclick="goPage('user_config')">USER</a></li>
						<li><a class='navbar-link btn-link' onclick="goPage('task_config')">TASK</a></li>
						<li><a class='navbar-link btn-link' onclick="goPage('ann_config')">ANNOUNCE</a></li>
					</ul>
				</div>
				<a href='logout.php'>
					<div class='pull-right navbar-brand' style='color:red' >LOGOUT</div>
					<!-- <input type='button' class='pull-right btn navbar-btn btn-danger' value='LOGOUT'> -->
				</a>
			</div>
		</div>
	</header>
	<div class='container' id='main'>
	</div>
</body>