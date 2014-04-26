<?
session_start();
include('config.php');
?>
<!doctype html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title><?=$config['name']?></title>
	<link href="960_12_col.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="addon/css/modal.css">
	<script type="text/javascript" src="jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="jquery.ba-hashchange.min.js"></script>
	<script type="text/javascript">

	var hash;
	getHash();
	var load_interval = null;
	function load() {
		clearInterval(load_interval);
		load_interval = null;

		var progress_bar = $('#progress-bar');
		progress_bar.css('width', '0%');
		progress_bar.show();

		var expect_load_time = 5;
		var update_frequency = 350;
		var freeze_percent = 80;

		var width = 0;
		var cnt = 0;
		load_interval = setInterval(function () {
			cnt++;
			width = cnt / (expect_load_time * 1000 / update_frequency) * 100;
			progress_bar.css('width', width + '%');
			if(width > freeze_percent) {
				console.log(width, freeze_percent);
				clearInterval(load_interval);
			}
		}, update_frequency);
	}
	function unload() {
		var progress_bar = $('#progress-bar');
		clearInterval(load_interval);
		load_interval = null;
		progress_bar.css('width', '100%').delay(350).fadeOut(150);
	}
	function getHash(){
		hash = location.hash.toLowerCase();
		hash = hash.substring(1).split('/');
	}
	function updateHash(degree){
		var newhash = '#';
		for(var i = 0; i < hash.length; i++){
			newhash += hash[i];
			if(i < hash.length - 1) newhash += '/';
		}
		location.hash = newhash;
		getHash();
	}
	function goMain(){
		hash[0] = 'main';
		updateHash();
		load();
		$.ajax({
			url : "main.php", 
			success : function (html){
				unload();
				$('#body').html(html);
			}
		});
	}
	function login(form){
		load();
		$.ajax({
			url : "login.php", 
			type : "post",
			dataType : "json",
			data : $(form).serialize(),
			success : function (html){
				if(html.valid == true){
					$('#error').html('ลงชื่อเรียบร้อย').slideToggle('fast');
					goMain();
				}
				else {
					unload();
					$('#error').html('ยูสเซอร์ ไม่ก็รหัสผิด').slideToggle('fast').delay(4000).slideToggle('fast');
				}
			}
		});
		return false;
	}
	function islogin(){
		load();
		$.ajax({
			url : 'islogin.php', 
			dataType : 'json',
			success : function (html){
				unload();
				if(html.islogin){
					goMain();
				}
			}
		});
	}
	regLoaded = false;
	function loadReg(){
		if(regLoaded) {
			$('#load_link').html('ยังไม่สมัคร?');
			regLoaded = false;
			$('#reg').slideUp('fast');
			return ;
		}
		load();
		$('#load_link').html('สมัครแล้ว?');
		$.ajax({
			url : 'reg.php',
			success : function (html){
				unload();
				regLoaded = true;
				$('#reg').html(html).slideDown('fast');
			}
		})
	}
	islogin();
	</script>
	<link rel="stylesheet" href="css/index.css">
</head>
<body>
	<div id="progress-bar-wrapper">
		<div id="progress-bar">
			
		</div>
	</div>
	<div id="body" align="center">
		<? if(!isLogin()): ?>
		<link href="admin/bootstrap.min.css" rel="stylesheet">
		<? if($config['regist_open']): ?>
		<div id="reg" style="display: none;"></div>
	<? endif; ?>
	<div id="login-wrapper" align="center">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<div class="panel-title">
					<?=$config['name']?>
				</div>
			</div>
			<div class="panel-body">
				<p id="error" style="display: none;" class='well'></p>
				<form method="post" onsubmit="return login(this);">
					<p>
						<input id="user" class='form-control' name="user" type="text" placeholder="ยูสเซอร์">
					</p>
					<p>
						<input id="pass" class='form-control' name="pass" type="password" placeholder="รหัสผ่าน">
					</p>
					<p>
						<? if($config['regist_open']): ?>
						<div class='btn-group pull-right'>
							<input type="button" onclick="loadReg()" class='btn btn-warning' value='สมัครใหม่'>
							<input type="submit" class='btn btn-warning' value="ลงชื่อเข้าใช้">
						</div>
					<? else: ?>
					<input type="submit" class='btn btn-warning pull-right' value="ลงชื่อเข้าใช้">
				<? endif; ?>
			</p>
		</form>
	</div>
</div>
</div>
<script type="text/javascript">
$(window).resize(function(){
	$('#body').height($(window).height()).width($(window).width());
});
$(window).resize();
</script>
<? endif; ?>
</div>
</body>
</html>