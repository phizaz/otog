<?php
session_start();
if(!isset($_SESSION['user_id']))
{
	echo "<meta http-equiv='refresh' content='0; login.php'/>";
	exit();
}
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
	.hidden-submit {
		border: 0 none;
		height: 0;
		width: 0;
		padding: 0;
		margin: 0;
		overflow: hidden;
	}
	</style>

	<!-- Optional theme -->
	<!-- <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css"> -->

	<meta http-equiv="content-Type" content="text/html; charset=utf-8">

	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript">
	function initialPage()
	{
		if(location.hash=="")
			$("#main").load("main_config.php");
		else
		{
			var hash = location.hash;
			hash = hash.substring(1,hash.length);
			$("#main").load(hash+"_config.php");
		}
	}
	function checkPage()
	{
		var currentPage = "";
		var hash;
		// var cnt=0;
		setInterval(function(){
			if(location.hash != currentPage)
			{
				// cnt++;
				// $("#cnt").html(cnt);
				currentPage = location.hash;
				hash = location.hash;
				hash = hash.substring(1,hash.length);
				$("#main").load(hash+"_config.php");
			}
		});
	}
	$(document).ready(function(){
		initialPage();
		checkPage();
		setInterval(function(){
			$('#config_detail').css('height', window.innerHeight-100+'px');
		});
	});
	</script>
</head>
<body>
	<header>
		<!-- <div id = "cnt"></div> -->
		<div class='navbar navbar-inverse navbar-static-top'>
			<div class='container'>
				<div class='navbar-header'>
					<a class='btn-link navbar-brand' href='#main'>OTOG</a>
					<ul class='nav navbar-nav'>
						<li><a class='navbar-link btn-link' href='#user'>USER</a></li>
						<li><a class='navbar-link btn-link' href='#task'>TASK</a></li>
						<li><a class='navbar-link btn-link' href='#announce'>ANNOUNCE</a></li>
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
