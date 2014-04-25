<?
session_start();
include('config.php');
if(!isLogin()){
	include('notlogin.php');
	die();
}
if(!inTime()){
	die('การแข่งขันจบแล้ว');
}
$task_id = $_GET['id'];
ob_start();
?>

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<style>
		body, input {
			font-family: 'lucida grande',tahoma,verdana,arial,sans-serif;
			font-size: 11px;
			color: rgb(30,30,30);
			margin: 0px;
		}
		.grey{
			color: rgb(140,140,140);
		}
	</style>
</head>
<body>

<?
if($_GET['step'] == 2){
	if($_FILES['file']['error'] > 0){
		die('<span class="grey">Fail!</span>');
	}
	else {
		$allowedExts = array('c','cpp');
		$tmp = explode('.',$_FILES['file']['name']);
		$extension = strtolower(end($tmp));
		
		if(in_array($extension, $allowedExts)){
			$filename = $task_id . '-' . $_SESSION[$config['name_short']]['user'] . '.' . $extension;
			move_uploaded_file($_FILES['file']['tmp_name'], 'judge/upload/' . $filename);
			//ENQUEUE
			$query = '
				INSERT INTO `queue` (
				`queue_id` ,
				`user_id` ,
				`task_id` ,
				`time` ,
				`file`
				)
				VALUES (NULL , ?, ?, ?, ?);';
			$sql->prepare($query);
			$sql->bind_param('iiis', $_SESSION[$config['name_short']]['user_id'], $task_id, $config['time'], $filename);
			$sql->execute();

			//GO TO RESULT
			
			echo '<script type="text/javascript">
				window.parent.hash[1] = "result";
				window.parent.updateHash();
			</script>';
		}
		else {
			header('location: upload.php?id='.$task_id.'&err=wrong-extension');
			exit(0);
		}
	}
}
else { 
	if($_GET['err'] == 'wrong-extension'){ ?>
	<script type="text/javascript">alert("อัพแต่ c, cpp เท่านั้นดิ");</script>
	<? } ?>
	<div style="display: table-cell; height: 50px; vertical-align: middle;">
	<form method="post" action="?id=<?=$task_id?>&step=2" enctype="multipart/form-data">
		<input class="grey" type="file" name="file" id="file" style="width: 120px;">
		<div style="height: 2px;"></div>
		<input type="submit" name="submit" style="width: 50px;" value="ตรวจ">
	</form>
	</div>
<? 
	ob_end_flush();
} 
?>

</body>
</html>