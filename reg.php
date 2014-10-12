<?
session_start();
include('config.php');
if(!$config['regist_open']){
	die('Registration is not available at this time.');
}
if (isLogin()){
	die('You were logged in.');
}
if($_GET['step'] == 2):
	$result = array();
	if($_POST['user'] == '' || $_POST['pass'] == '' || $_POST['pass-2'] == '' || $_POST['display'] == ''){
		$result['success'] = false;
		$result['err'] = 'notfill';
	}
	else if($_POST['pass'] != $_POST['pass-2']){
		$result['success'] = false;
		$result['err'] = 'notsame';
	}
	else {
		$query = 'select * from `user` where `user` = ?;';
		$sql->prepare($query);
		$sql->bind_param('s', $_POST['user']);
		$sql->execute();
		if($sql->fetch()){
			$result['success'] = false;
			$result['err'] = 'dul';
		}
		else {
			$query = 'insert into `user` (
				`user_id`, 
				`user`, 
				`pass`, 
				`display`, 
				`level`
				) values (NULL, ?, ?, ?, 1);';
			$sql->prepare($query);
			$sql->bind_param('sss', $_POST['user'], $_POST['pass'], $_POST['display']);
			$sql->execute();
			$user_id = $mysqli->insert_id;
			$result['success'] = true;
			$_SESSION[$config['name_short']]['login'] = true;
			$_SESSION[$config['name_short']]['user_id'] = $user_id;
			$_SESSION[$config['name_short']]['user'] = $_POST['pass'];
			$_SESSION[$config['name_short']]['display'] = $_POST['display'];
			$_SESSION[$config['name_short']]['level'] = 1;
		}
	}
	echo json_encode($result);
else: ?>
<script type="text/javascript">
function reg(form){
	load();
	$.ajax({
		url : 'reg.php?step=2',
		type : 'post',
		data : $(form).serialize(),
		dataType : 'json',
		success : function (html){
			if(html.success){
				islogin();
			}
			else {
				unload();
				if(html.err == 'dul'){
					$('#reg-err').html('คุณเคยสมัครแล้ว').slideToggle('fast').delay(3000).slideToggle('fast');
				}
				else if(html.err == 'notfill'){
					$('#reg-err').html('ใส่ไม่ครบ').slideToggle('fast').delay(3000).slideToggle('fast');
				}
				else if(html.err == 'notsame'){
					$('#reg-err').html('รหัสไม่ตรงกัน').slideToggle('fast').delay(3000).slideToggle('fast');
				}
			}
		}
	});
	return false;
}
</script>
<!-- <div id="login-wrapper" align="center"> -->
	<div class="panel panel-success">
		<div class="panel-heading">
			<div class="panel-title">
				สมัครสมาชิกใหม่
			</div>
		</div>
		<div class="panel-body">
			<div style="height: 5px;"></div>
			<p class="well" id="reg-err" style="display: none;"></p>
			<form method="post" onsubmit="return reg(this);">
				<p><input type="text" class="form-control" name="user" placeholder="ยูสเซอร์ใหม่"></p>
				<p><input id="reg_pass" class="form-control" type="password" name="pass" placeholder="รหัสผ่านใหม่"></p>
				<p><input id="reg_pass-2" class="form-control" type="password" name="pass-2" placeholder="ย้ำรหัสใหม่"></p>
				<p><input type="text" class="form-control" name="display" placeholder="ชื่อที่ใช้แสดง"></p>
				<p><input type="submit" class="btn btn-success pull-right" value="สมัครสมาชิก"></p>
			</form>
		</div>
	</div>
<!-- </div> -->
<? endif; ?>