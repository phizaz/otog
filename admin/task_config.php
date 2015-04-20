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
		$.post("active.php?action=rem_task", {task_id: id},
			function(result){
				$("#msg").html(result);
				$('#main').load('task_config.php');
			}
		);
	}
}
function edit(id)
{
	$('#task_'+id).hide();
	$('#edit_'+id).show();
}
function cancel(id)
{
	$('#task_'+id).show();
	$('#edit_'+id).hide();
}
$('.task').hover(
	function() {$(this).css('background-color', '#F9F9F9')},
	function() {$(this).css('background-color', '#FFFFFF')
});
function active(id,action)
{
	$.post("active.php?action="+action, {
		name: $("#"+id+"_name").val(),
		name_short: $("#"+id+"_name_short").val(),
		score: $("#"+id+"_score").val(),
		see: $("#"+id+"_see").val(),
		mem_limit: $("#"+id+"_mem_limit").val(),
		time_limit: $("#"+id+"_time_limit").val()},
		function(result){
			$("#msg").html(result);
			$("#msg").slideDown().delay(1500).slideUp();
		}
	);
	if(action!="add")
	{
		$("#"+id+"__name").html($("#"+id+"_name").val());
		$("#"+id+"__name_short").html($("#"+id+"_name_short").val());
		$("#"+id+"__score").html($("#"+id+"_score").val());
		$("#"+id+"__time_limit").html($("#"+id+"_time_limit").val());
		$("#"+id+"__mem_limit").html($("#"+id+"_mem_limit").val());
		$("#"+id+"__see").html($("#"+id+"_see").val());
		$("#task_"+id).show();
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
		<center><h1>TASK</h1></center>
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
					<td><b>Name</b></td>
					<td><b>Short Name</b></td>
					<td><b>Score</b></td>
					<td><b>Time</b></td>
					<td><b>Memory</b></td>
					<td><b>See</b></td>
					<td style='width:100px'><b></b></td>
				</tr>
			</thead>
			<tbody>
				<tr onkeypress="return pressEnter(event,'add','add_task')">
					<!-- <form action='active.php?action=add_task' method='post'> -->
						<td></td>
						<td><input type='text' placeholder='Name' class='form-control' id='add_name'></td>
						<td><input type='text' placeholder='Short Name' class='form-control' id='add_name_short'></td>
						<td><input type='text' placeholder='Score' class='form-control' id='add_score'></td>
						<td><input type='text' placeholder='Time' class='form-control' id='add_time_limit'></td>
						<td><input type='text' placeholder='Mem' class='form-control' id='add_mem_limit'></td>
						<td><input type='text' placeholder='See' class='form-control' id='add_see'></td>
						<td><input type='submit' id='go' class='btn btn-success' value='ADD' onclick="active('add','add_task')"></td>
					<!-- </form> -->
				</tr>
				<?php
				$sql = "SELECT * FROM `task` ORDER BY `task_id` DESC";
				$result = mysql_query($sql);
				while($input = mysql_fetch_array($result))
				{
					?>
					<tr id='task_<?=$input["task_id"]?>' class='task' ondblclick='edit(<?=$input["task_id"]?>)'>
							<td><?=$input["task_id"]?></td>
							<td>
								<a href='../doc/<?=$input["name_short"]?>.pdf' target='_blank' class='btn-link' id="<?=$input["task_id"]?>__name"><?=$input["name"]?></a>
							</td>
							<td id="<?=$input["task_id"]?>__name_short"><?=$input["name_short"]?></td>
							<td id="<?=$input["task_id"]?>__score"><?=$input["score"]?></td>
							<td id="<?=$input["task_id"]?>__time_limit"><?=$input["time_limit"]?></td>
							<td id="<?=$input["task_id"]?>__mem_limit"><?=$input["mem_limit"]?></td>
							<td id="<?=$input["task_id"]?>__see"><?=$input["see"]?></td>
						<td style="min-width: 185px;">
							<div class='btn-group'>
								<a href='javascript:edit(<?=$input["task_id"]?>)' class='btn btn-warning'>
									<i class="glyphicon glyphicon-wrench"></i>
								</a>
								<a href="rejudge.php?task_id=<?=$input['task_id']?>" target="_blank" class="btn btn-warning" style="font-size: 9px">rejudge</a>
								<a href='javascript:rem(<?=$input["task_id"]?>)' class='btn btn-danger'>
									<i class="glyphicon glyphicon-trash"></i>
								</a>
							</div>
						</td>
					</tr>
					<tr id='edit_<?=$input["task_id"]?>' class='edit' onkeypress="return pressEnter(event,'<?=$input["task_id"]?>','edit_task&task_id=<?=$input["task_id"]?>')">
						<!-- <form action='active.php?action=edit_task&task_id=<?=$input["task_id"]?>' method='post' style='display:none'> -->
							<td><?=$input["task_id"]?></td>
							<td><input value='<?=$input["name"]?>' type='text' placeholder='Name' class='form-control' id='<?=$input["task_id"]?>_name'></td>
							<td><input value='<?=$input["name_short"]?>' type='text' placeholder='Short Name' class='form-control' id='<?=$input["task_id"]?>_name_short'></td>
							<td><input value='<?=$input["score"]?>' type='text' placeholder='Score' class='form-control' id='<?=$input["task_id"]?>_score'></td>
							<td><input value='<?=$input["time_limit"]?>' type='text' placeholder='Time' class='form-control' id='<?=$input["task_id"]?>_time_limit'></td>
							<td><input value='<?=$input["mem_limit"]?>' type='text' placeholder='Mem' class='form-control' id='<?=$input["task_id"]?>_mem_limit'></td>
							<td><input value='<?=$input["see"]?>' type='text' placeholder='See' class='form-control' id='<?=$input["task_id"]?>_see'></td>
							<td class='btn-group'>
								<input type='submit' id='go' class='btn btn-primary' value='Y' onclick="active('<?=$input["task_id"]?>','edit_task&task_id=<?=$input["task_id"]?>')">
								<input type='button' class='btn btn-danger' onclick="cancel(<?=$input["task_id"]?>)" value='N'>
							</td>
						</form>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	</div>
</div>
