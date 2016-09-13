<?php
session_start();
if(!isset($_SESSION['user_id']))
	echo "<meta http-equiv='refresh' content='0; login.php?fail=2'/>";
include('config.php');
?>
<script type="text/javascript">
$(".edit").hide();
$("#msg").hide();
function rem(id)
{
	if(confirm('Sure?'))
	{
		$.post("active.php?action=rem_user", {user_id: id}, 
			function(result){
				$("#msg").html(result);
				$('#main').load('user_config.php');	
			}
		);
	}
}
function edit(id)
{
	$('#user_'+id).hide();
	$('#edit_'+id).show();
}
function cancel(id)
{
	$('#user_'+id).show();
	$('#edit_'+id).hide();
}
$('.user').hover(
	function() {$(this).css('background-color', '#F9F9F9')}, 
	function() {$(this).css('background-color', '#FFFFFF')
});
function active(id,action)
{
	$.post("active.php?action="+action, { 
		user: $("#"+id+"_user").val(),
		pass: $("#"+id+"_pass").val(),
		display: $("#"+id+"_display").val(), 
		level: $("#"+id+"_level").val()}, 
		function(result){
			$("#msg").html(result);
			$("#msg").slideDown().delay(1500).slideUp();
		}
	);
	if(action!="add")
	{
		$("#"+id+"__user").html($("#"+id+"_user").val());
		$("#"+id+"__pass").html($("#"+id+"_pass").val());
		$("#"+id+"__display").html($("#"+id+"_display").val());
		$("#"+id+"__level").html($("#"+id+"_level").val());
		$("#user_"+id).show();
		$("#edit_"+id).hide();
	}
}
function pressEnter(e,id,action) {
	if (e.keyCode == 13) {
		active(id,action);
		return false;
	}
}
</script>
<div class='row'>
	<div class='col-md-3'>
		<center><h1>USER</h1></center>
		<hr>
		<div class="panel panel-info">
			<div class="panel-heading"><h3 class="panel-title">Note</h3></div>
			<div class="panel-body">
				<!-- <h6>* Point the cursor at something that you want to know awhile, you will see the description. (MAIN only)</h6> -->
				Double click a row to edit.
			</div>
		</div>
	</div>
	<div class='col-md-9'>
		<div id='msg'></div>
		<div id="config_detail" style="overflow:scroll;">
		<table class='table table-striped'>
			<thead>
				<tr>
					<td><b>#</b></td>
					<td><b>Username</b></td>
					<td><b>Password</b></td>
					<td><b>Display Name</b></td>
					<td><b>Level</b></td>
					<td style='width:100px'><b></b></td>
				</tr>
			</thead>
			<tbody>
				<tr onkeypress="return pressEnter(event,'add','add_user')">
					<td></td>
					<td><input type='text' placeholder='Username' class='form-control' id='add_user' name='user'></td>
					<td><input type='text' placeholder='Password' class='form-control' id='add_pass' name='pass'></td>
					<td><input type='text' placeholder='Display Name' class='form-control' id='add_display' name='display'></td>
					<td><input type='text' placeholder='Level' class='form-control' id='level' name='add_level'></td>
					<td><input type='submit' id='add' class='btn btn-success' onclick="active('add','add_user')" value='ADD'></td>
				</tr>
				<?php
				$sql = "SELECT * FROM `user` ORDER BY `user_id` DESC";
				$result = mysql_query($sql);
				while($input = mysql_fetch_array($result))
				{
					?>
					<tr id='user_<?=$input["user_id"]?>' class='user' ondblclick='edit(<?=$input["user_id"]?>)'> 
						<td><?=$input["user_id"]?></td>
						<td id="<?=$input["user_id"]?>__user"><?=$input["user"]?></td>
						<td id="<?=$input["user_id"]?>__pass"><?=$input["pass"]?></td>
						<td id="<?=$input["user_id"]?>__display"><?=$input["display"]?></td>
						<td id="<?=$input["user_id"]?>__level"><?=$input["level"]?></td>
						<td>
							<div class='btn-group'>
								<span onclick='edit(<?=$input["user_id"]?>)' class='btn btn-warning glyphicon glyphicon-wrench'></span>
								<span onclick='rem(<?=$input["user_id"]?>)' class='btn btn-danger glyphicon glyphicon-trash'></span>
							</div>
						</td>
					</tr>
					<tr id='edit_<?=$input["user_id"]?>' class='edit' onkeypress="return pressEnter(event,'<?=$input["user_id"]?>','edit_user&user_id=<?=$input["user_id"]?>')">
						<!-- <form action='active.php?action=edit_user&user_id=<?=$input["user_id"]?>' method='post' style='display:none'> -->
							<td><?=$input["user_id"]?></td>
							<td><input value='<?=$input["user"]?>' type='text' placeholder='Username' class='form-control' id='<?=$input["user_id"]?>_user'></td>
							<td><input value='<?=$input["pass"]?>' type='text' placeholder='Password' class='form-control' id='<?=$input["user_id"]?>_pass'></td>
							<td><input value='<?=$input["display"]?>' type='text' placeholder='Display Name' class='form-control' id='<?=$input["user_id"]?>_display'></td>
							<td><input value='<?=$input["level"]?>' type='text' placeholder='Level' class='form-control' id='<?=$input["user_id"]?>_level'></td>
							<td class='btn-group'>
								<input type='submit' id='go' class='btn btn-primary' value='Y' onclick="active('<?=$input["user_id"]?>','edit_user&user_id=<?=$input["user_id"]?>')">
								<input type='button' class='btn btn-danger' onclick="cancel(<?=$input["user_id"]?>)" value='N'>
							</td>
						<!-- </form> -->
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		</div>
	</div>
</div>
