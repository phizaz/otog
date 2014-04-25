<?
date_default_timezone_set('Asia/Bangkok');
if($_GET['step'] == 2):
	echo mktime($_POST['H'], $_POST['M'], $_POST['S'], $_POST['m'], $_POST['d'], $_POST['y']);
	$_GET['step'] = 1;
	include('mktime.php');
else :
?>
<form method="post" action="?step=2">
	<input type="text" name="y" placeholder="year" value="<?=date('Y')?>">
	<input type="text" name="m" placeholder="month" value="<?=date('m')?>">
	<input type="text" name="d" placeholder="day" value="<?=date('d')?>">
	<input type="text" name="H" placeholder="hour">
	<input type="text" name="M" placeholder="minute">
	<input type="text" name="S" placeholder="second">
	<input type="submit">
</form>
<? endif; ?>