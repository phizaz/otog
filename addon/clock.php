<?php
session_start();
include ('../config.php');
if (!isLogin()) {
	include ('../notlogin.php');
	die();
}
echo showDif($config['end_time'] - $config['time']);
?>


