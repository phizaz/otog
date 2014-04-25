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
		$.post("active.php?action=rem_ann", {announce_id: id}, 
			function(result){
				$("#msg").html(result);
				$('#main').load('announce_config.php');	
			}
		);
	}
}
function edit(id)
{
	$('#ann_'+id).hide();
	$('#edit_'+id).show();
}
function cancel(id)
{
	$('#ann_'+id).show();
	$('#edit_'+id).hide();
}
$('.ann').hover(
	function() {$(this).css('background-color', '#F9F9F9')}, 
	function() {$(this).css('background-color', '#FFFFFF')
});
function active(id,action)
{
	$.post("active.php?action="+action, { 
		announce: $("#"+id+"_announce").val(),
		see: $("#"+id+"_see").val()}, 
		function(result){
			$("#msg").html(result);
			$("#msg").slideDown().delay(1500).slideUp();
		}
	);
	if(action!="add")
	{
		$("#"+id+"__announce").html($("#"+id+"_announce").val());
		$("#"+id+"__see").html($("#"+id+"_see").val());
		$("#ann_"+id).show();
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
		<center><h1>ANNOUNCE</h1></center>
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
					<td><b>Announce</b></td>
					<td><b>See</b></td>
					<td style='width:100px'><b></b></td>
				</tr>
			</thead>
			<tbody>
				<tr onkeypress="return pressEnter(event,'add','add_ann')">
					<!-- <form method='post' action='active.php?action=add_ann'> -->
						<td></td>
						<td><input type='text' placeholder='Announce' class='form-control' id='add_announce'></td>
						<td><input type='text' placeholder='See' class='form-control' id='add_see'></td>
						<td><input type='submit' class='btn btn-success' value='ADD' onclick="active('add','add_ann')"></td>
					<!-- </form> -->
				</tr>
				<?php
				$sql = "SELECT * FROM `announce` ORDER BY `announce_id` DESC";
				$result = mysql_query($sql);
				while($input = mysql_fetch_array($result))
				{
					?>
					<tr id='ann_<?=$input["announce_id"]?>' class='ann' ondblclick='edit(<?=$input["announce_id"]?>)'> 
						<td><?=$input["announce_id"]?></td>
						<td id="<?=$input["announce_id"]?>__announce"><?=$input["announce"]?></td>
						<td id="<?=$input["announce_id"]?>__see"><?=$input["see"]?></td>
						<td>
							<div class='btn-group'>
								<span onclick='edit(<?=$input["announce_id"]?>)' class='btn btn-warning glyphicon glyphicon-wrench'></span>
								<span onclick='rem(<?=$input["announce_id"]?>)' class='btn btn-danger glyphicon glyphicon-trash'></span>
							</div>
						</td>
					</tr>
					<tr id='edit_<?=$input["announce_id"]?>' class='edit' onkeypress="return pressEnter(event,'<?=$input["announce_id"]?>','edit_ann&announce_id=<?=$input["announce_id"]?>')">
						<!-- <form action='active.php?action=edit_ann&announce_id=<?=$input["announce_id"]?>' method='post' style='display:none'> -->
							<input type='submit' id='go' class='hidden-submit' value=''>
							<td><?=$input["announce_id"]?></td>
							<td><input value='<?=$input["announce"]?>' type='text' placeholder='Announce' class='form-control' id='<?=$input["announce_id"]?>_announce'></td>
							<td><input value='<?=$input["see"]?>' type='text' placeholder='See' class='form-control' id='<?=$input["announce_id"]?>_see'></td>
							<td class="btn-group">
								<input type='submit' id='go' class='btn btn-primary' value='Y' onclick="active('<?=$input["announce_id"]?>','edit_ann&announce_id=<?=$input["announce_id"]?>')">
								<input type='button' class='btn btn-danger' onclick="cancel(<?=$input["announce_id"]?>)" value='N'>
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