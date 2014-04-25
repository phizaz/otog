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
	<script type="text/javascript" src="jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="jquery.ba-hashchange.min.js"></script>
	<script type="text/javascript">

	var hash;
	getHash();
	function load(){
		$('#loader-wrapper').delay(500).queue(function() {$(this).fadeIn().dequeue();}); 
	}
	function unload(){
		$('#loader-wrapper').clearQueue().fadeOut();
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
	<style>
	body {
		font-family: 'lucida grande',tahoma,verdana,arial,sans-serif;
		font-size: 14px;
		color: rgb(30,30,30);
	}
	input[type='text'], input[type="password"] {
		/*font-size: 14px;*/
		text-align: center;
		width: 250px;
		/*height: 25px;*/
	}
	a{
		color: rgb(255,145,0);
		text-decoration: none;
	}
	</style>
</head>
<body>
	<div id="loader-wrapper">
		<style>
			#loader-wrapper {
				position: fixed;
				top: 0px;
				left: 0px;
				right: 0px;
				bottom: 0px;
				display: none;
				z-index: 1000;
			}
			#loader {
				position: absolute;
				top: 50%;
				text-align: center;
				margin-top: -30px;
				width: 100%;
			}
		</style>
		<div id="loader" align="center">
			<div style="overflow: hidden; width: 100px; height: 60px; border-radius: 15px; margin: auto;">
				<img src="loading.gif" style="width: 250px; margin: -95px 0px 0px -75px;">
			</div>
		</div>
	</div>
	<div id="body" align="center">
		<? if(!isLogin()): ?>
		<style>
		#body {
			display: table-cell;
			vertical-align: middle;
		}
		#login-wrapper {
			width: 400px;  
			/*background: rgb(240,240,240);*/
		}
		#login-wrapper .inner{
			height: 300px;
			display: table-cell;
			vertical-align: middle;
		}
		</style>
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