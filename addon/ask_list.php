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

$short = "";
$lm = strlen($ask["detail"]);
if($lm > 30)$lm = 30;
for($i = 0; $i < $lm; $i++)$short.=$ask["detail"][$i];
$result = Database::getAllThat("ask","`sub_ask` = 0 ORDER BY  `ask`.`ask_id` DESC ");
while($ask = mysql_fetch_array($result))
{
	$user = Database::getById("user",$ask["user_id"]);
	?>
	<p><p onclick="load_ans(<?=$ask["ask_id"]?>)" class="btn btn-default btn-lg" style="width:100%">#<?=$ask["ask_id"]?> - <?=$user["display"]?> : <?=$short?></p></p>
	<?php
}
?>