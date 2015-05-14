<?php
session_start();
include ('../config.php');
include ('config.php');
include ('library.php');

if(!isAdmin())
{
	die();
	exit();
}

$result = Database::getAllThat("ask","`sub_ask` = 0 ORDER BY  `ask`.`ask_id` DESC ");
while($ask = mysql_fetch_array($result))
{
	$res = Database::getAllThat("ask","`sub_ask` = ".$ask["ask_id"]);
	$count_hr = 0;
	$sub = mysql_fetch_array($res);
	$activate = "btn-default";
	if(!isset($sub["ask_id"]))
		$activate = "btn-primary";
	$short = "";
	$lm = strlen($ask["detail"]);
	if($lm > 50)$lm = 50;
	for($i = 0; $i < $lm; $i++)$short.=$ask["detail"][$i];
	$user = Database::getById("user",$ask["user_id"]);
	?>
	<p><p onclick="load_ans(<?=$ask["ask_id"]?>)" class="btn <?=$activate?> btn-lg" style="width:100%">#<?=$ask["ask_id"]?> - <?=$user["display"]?> : <?=$short."..."?></p></p>
	<?php
}
?>