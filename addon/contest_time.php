<!DOCTYPE>
<head>
	<link rel="stylesheet" type="text/css" href="../admin/bootstrap.min.css">
	<script type="text/javascript" src="jquery-1.10.2.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript">
	setInterval(function(){
				$("#contest_clock").load("clock.php");
	},100);
	</script>
</head>
<body style="background-color:black">
	<center>
	</center>
	<br><br><br><br>
	<div class='container'>
		<div class="panel panel-default">
			<div class="panel-heading">
				<center><span style="font-size:50;" class='panel-title'>เหลือเวลา</span></center>
			</div>
			<div class="panel-body" style="background-color:black">
				<center><font color="#FFFFFF"><span id="contest_clock" style="font-size:300;"></span></font></center>
			</div>
		</div>
	</div>
</body>


