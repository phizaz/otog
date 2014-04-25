<?php
include('config.php');

$input = array(
	"name_short" => "Short Name",
	"name" => "Full Name",
	"force_start" => "Force Start",
	"online" => "Online Status",
	"ranking_show_user_id" => "Ranking User Id",
	"regist_open" => "Allow Register",
	"mode" => "Mode",
	"show_ranking" => "Show Ranking"
	);

$contest = array(
	"start_time" => "Start Time",
	"end_time" => "End Time"
	);

?>
<script type="text/javascript">
$("#msg").hide();
isContest("start");
function isContest(type)
{
	if(type=="btn")
	{
		if($('#input_mode').val()=="contest" || $('#input_mode').val()=="blind_contest")
			$('#contest_p').slideDown();
		else
			$('#contest_p').slideUp();
	}
	else
	{
		if($('#input_mode').val()=="contest" || $('#input_mode').val()=="blind_contest")
			$('#contest_p').show();
		else
			$('#contest_p').hide();
	}

}
function save(index)
{
	$.post("active.php", { 
			index: index, 
			val: $("#input_"+index).val(),
			type: 'main'
		}, 
		function(result){
			$("#msg").html(result);
			$("#msg").slideDown().delay(1500).slideUp();
		}
	);
	isContest("btn");
}

function save_t(index)
{
	$.post("active.php", { 
			index: index, 
			val: $("#input_"+index).val(),
			type: 'main_time'
		}, 
		function(result){
			$("#msg").html(result);
			$("#msg").slideDown().delay(1500).slideUp();
		}
	);
}
function pressEnter(e,index,type) {
	if(type!="time")
	{
		if (e.keyCode == 13) {
			save(index);
			return false;
		}
	}
	else
	{
		if (e.keyCode == 13) {
			save_t(index);
			return false;
		}
	}
}
</script>
<div class='row'>
	<div class='col-md-3'>
		<center><h1>MAIN</h1></center>
	</div>
	<div class='col-md-6'>
		<div id = "msg" ></div><br>
		<div class='row' id="config_detail" style="overflow:scroll;">
		<!-- general config -->
		<?php
		foreach ($input as $key => $value) {
			$sql = "SELECT * FROM `config` WHERE `index` = '".$key."'";
			$result = mysql_query($sql);
			$config = mysql_fetch_array($result);
			?>
			<acronym title="<?=$config["description"]?>">
				<div class='col-md-3'><label><?=$value?></label></div>
				<div class='col-md-6'><input class='form-control' type='text' id='input_<?=$key?>'value="<?=$config["val"]?>" onkeypress="return pressEnter(event,'<?=$key?>','')"/></div>
				<div class='col-md-3' align='middle'><button class='btn btn-primary' onclick="save('<?=$key?>')">SAVE</button></div>
			</acronym>
			<br><br>
			<?php
		}
		?>
		<!-- contest config -->
			<div id="contest_p">
				<?php
				foreach ($contest as $key => $value) {
					$sql = "SELECT * FROM `config` WHERE `index` = '".$key."'";
					$result = mysql_query($sql);
					$config = mysql_fetch_array($result);
					$date = date("m/d/Y H:i:s",$config["val"]);
					?>
					<acronym title="<?=$config["description"]?>">
						<div class='col-md-3'><label><?=$value?></label></div>
						<div class='col-md-6'><input class='form-control' type='text' id='input_<?=$key?>' value="<?=$date?>" onkeypress="return pressEnter(event,'<?=$key?>','time')"/></div>
						<div class='col-md-3' align='middle'><button class='btn btn-primary' onclick="save_t('<?=$key?>')">SAVE</button></div>
					</acronym>
					<br><br>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<div class='col-md-3'>
		<div class="panel panel-info">
			<div class="panel-heading"><h3 class="panel-title">Note</h3></div>
			<div class="panel-body">
				Point the cursor at something that you want to know awhile, you will see the description.
				<!-- <h6>** Double click a row to edit. (For USER, TASK and ANNOUNCE)</h6> -->
			</div>
		</div>
	</div>
</div>