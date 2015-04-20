<?php
session_start();
if(isset($_SESSION['user_id']))
	echo "<meta http-equiv='refresh' content='0; index.php'/>";
$msg="";
if(isset($_REQUEST["fail"]))
{
	if($_REQUEST["fail"]==1)
		$msg = "USERNAME OR PASSWORD WAS WRONG!";
	if($_REQUEST["fail"]==2)
		$msg = "PLEASE LOGIN";
}
?>
<head>
	<title>ADMIN ONLY</title>
	<!-- Latest compiled and minified CSS -->
	<!-- <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"> -->
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css">

	<!-- Optional theme -->
	<!-- <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css"> -->

	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

	<script src="http://scius.kku.ac.th/scripts/jquery-1.9.1.js"></script>
</head>

<body>
	<div class='container'>
		<center>
			<h1>ADMIN ONLY</h1>
			<h3><font color='red'><?=$msg?></font></h3>
		</center>
		<div class='row'>
			<div class='col-md-4'></div>
			<div class='col-md-4'>
				<hr>
				<form action='login_active.php' method='post'>
					<input class='form-control' type='text' placeholder='USERNAME' name='user'><br>
					<input class='form-control' type='password' placeholder='PASSWORD' name='pass'><br>
					<input class='btn btn-success pull-right' type='submit' value='LOGIN'/>
				</form>
			</div>
			<div class='col-md-4'></div>
		</div>
	</div>
</body>