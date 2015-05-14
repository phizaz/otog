
<div class="container_12">
	<div class="grid_12">
		<? if($config['time'] < $config['start_time']): ?>
		<script type="text/javascript">
			setInterval(function(){
				$("#contest_clock").load("addon/clock_before.php");
				if($("#contest_clock").text().length==2)
					location.reload();
			},900);
		</script>
		<h1 style="text-align: center; font-size: 400%;">ยังไม่ถึงเวลาแข่ง</h1>
		<h1 style="text-align: center;">อีก <span id="contest_clock"></span></h1>
		<? elseif($config['time'] > $config['end_time']): ?>
		<h1 style="text-align: center; font-size: 400%;">การแข่งขันจบแล้ว</h1>
		<h1 style="text-align: center;"><a href="addon/scoreboard.php">สรุปผลการแข่งขัน</a></h1>
		<? endif;?>
	</div>
</div>