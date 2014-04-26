<?
session_start();
include ('config.php');
if (!isLogin()) {
	include ('notlogin.php');
	die();
}
$page = '#main';
?>
<script type="text/javascript">
	function goPage(page) {
		load();
		$.ajax({
			url : page + '.php',
			success : function(html) {
				unload();
				$('#content').html(html);
			}
		});
	}

	function engine() {
		getHash();
		$("#test_test").load(hash[1]);
		if (hash[1] == null || hash[1] == '') {
			hash[1] = 'task';
			updateHash();
		}
		goPage(hash[1]);
	}

	//WHEN LINK
	engine();
	$(window).hashchange(function() {
		engine();
	});

	function logout() {
		load();
		$.ajax({
			url : 'logout.php',
			success : function(html) {
				unload();
				window.location = '';
			}
		});
	}
</script>
<? if($config['mode']=="online" or isAdmin()): ?>
<script type="text/javascript">
	$("#count_task").load("count_task.php");
</script>
<? endif;?>
<style>
	.announce {
		font-size: 20px;
	}
	.announce .border {
		padding: 10px;
	}
</style>
<div id="nav" class="container_12" align="center">
	<div class="grid_2">
		<h4>สวัสดี <?=$_SESSION[$config['name_short']]['display'] ?>
		<?php
		if($_SESSION[$config['name_short']]['level']==0)
		{
			echo "<hr><a href='admin' target='_blank'>หน้าผู้ดูแล</a>";
		}
		?>
		</h4>
	</div>
	<div class="grid_2">
		<? if($config['mode'] == 'online'): ?>
		<h4>โหมดฝึกซ้อม
		<? else: ?>
			<? if($config['time'] < $config['start_time']): ?>
			<h4>การแข่งยังไม่เริ่ม
			<? elseif($config['time'] > $config['end_time']): ?>
			<h4>การแข่งขันจบแล้ว <a href="addon/scoreboard.php" target="_blank">สรุปผลการแข่งขัน</a>
			<? else: ?>
			<script type="text/javascript">
			setInterval(function(){
				$("#contest_clock").load("addon/clock.php");
				if($("#contest_clock").text().length==2)
					location.reload();
			},900);
			</script>
			<h3><span style="font-size:12px">เหลือเวลา</span><br><span id="contest_clock"></span>
			<? endif; ?>
		<? endif; ?>
		<?=($config['online'] ? '' : '(ปิดอยู่)') ?>
		</h4>
	</div>
	<div class="grid_2">
		<h4><a onclick="select_type('all')" href="<?=$page ?>/task">โจทย์</a></h4>
		<!-- <h4><a href="<?=$page ?>/task">โจทย์</a></h4> -->
	</div>
	<div class="grid_2">
		<h4><a href="<?=$page ?>/result">ผลตรวจ</a></h4>
	</div>
	<? if(isAdmin() || $config['mode'] != 'online' || ($config['mode'] == 'online' && $config['show_ranking'])): ?>
	<div class="grid_2">
		<h4><a href="<?=$page ?>/rank">ผลสอบ</a></h4>
	</div>
	<? endif; ?>
	<div class="grid_2">
		<h4><a href="javascript:logout();">ออกระบบ</a></h4>
	</div>
</div>
<div class="container_12">
	<div class="grid_8">
		<div id="announce" align="center">
			<a href="javascript:slide()" style="color:black">
				<?
				$query = 'select `announce` from `announce` where `see` = 1 order by `announce_id` desc;';
				$sql -> prepare($query);
				$sql -> execute();
				$sql -> bind_result($announce);
				$ann_idx = 0;
				while ($sql -> fetch()) {
					echo '
					<div class="announce" id="announce_'.$ann_idx++.'" align="center" style="display : none">
						<div class="border">
						' . $announce . '
						</div>
					</div>';
				}
				?>
			</a>
		</div>
		<script type="text/javascript">
			var ann_idx = Math.floor(Math.random()*1000)%<?=$ann_idx?>;
			$("#announce_"+ann_idx).show();
			if(<?=$ann_idx?> > 1)
			{
				$("#announce").css("height","100px");
				var slide_time = setInterval(function(){
					$("#announce_"+ann_idx).slideUp();
					ann_idx = (ann_idx+1)%<?=$ann_idx?>;
					$("#announce_"+ann_idx).slideDown();
				},10000);
			}
			function slide()
			{
				if(<?=$ann_idx?> > 1)
				{
					clearInterval(slide_time);

					$("#announce_"+ann_idx).slideUp();
					ann_idx = (ann_idx+1)%<?=$ann_idx?>;
					$("#announce_"+ann_idx).slideDown();

					slide_time = setInterval(function(){
						$("#announce_"+ann_idx).slideUp();
						ann_idx = (ann_idx+1)%<?=$ann_idx?>;
						$("#announce_"+ann_idx).slideDown();
					},10000);
				}
			}
		</script>
		<div id="online" align="left">
			<b>ยังมีชีวิตอยู่</b>
			<?
			$query = 'select `user_id`, `time` from `activity`;';
			$sql -> prepare($query);
			$sql -> execute();
			$sql -> bind_result($user_id, $time);

			$first = true;
			while ($sql -> fetch()) {
				if (time(0) - $time <= $config['user_life_time'] * 60) {
					$user = user($user_id);
					if ($first)
						$first = false;
					else
						echo ', ';
					echo $user['display'];
				}
			}
			?>
		</div>
		<div id="count_task" align="left"></div>
		<div style="height: 10px;"></div>
	</div>
	<div class="grid_4" align="left">
		<div id="chat">
			<style>
				#chat_messages {
					height: 120px;
					border: 1px solid rgb(250,250,250);
					border-radius: 4px 4px 0px 0px;
					position: relative;
					font-size: 12px;
				}
				#chat_messages #messages {
					overflow-x: hidden;
					overflow-y: scroll;
					position: relative;
					height: 120px;
				}
				#chat_messages #messages h4 {
					margin: 0px;
					padding: 3px 0px 3px 0px;
				}
				#chat_messages #messages p {
					margin: 0px;
					color: rgb(66,66,66);
					padding: 0px 0px 3px 0px;
				}
				#chat_messages #messages .time {
					font-weight: normal;
					color: rgb(200,200,200);
					font-size: 10px;
				}
				#chat_messages #messages .message {
					padding: 0px 10px 0px 10px;
					transition: 0.35s ease;
					-webkit-transition: 0.35s ease;
					-moz-transition: 0.35s ease;
					-o-transition: 0.35s ease;
					-ms-transition: 0.35s ease;
				}
				#chat_messages #messages .unread{
					background: rgb(250,250,250);
				}
				#chat_type {
					position: relative;
					height: 50px;
				}
				#chat_type .border {
					padding: 10px;
				}
				#chat_messages .banner {
					position: absolute;
					width: 100%;
					left: -1px;
					padding: 1px;
					font-size: 11px;
					color: rgb(180,180,180);
					background: rgb(250,250,250);
					text-align: center;
					display: none;
				}
				#new_message {
					bottom: 0px;
				}
				#message_loading {
					top: 0px;
				}
				#chat_type textarea:focus{
					outline-width: 0;
				}
			</style>
			<div id="chat_messages">
				<div id="message_loading" class="banner">
					กำลังโหลด
				</div>
				<div id="new_message" class="banner">
					มีข้อความใหม่
				</div>
				<div id="messages">
					<div id="messages_inner">
						<div id="chat_tmp" style="display: none;"></div>
					</div>
				</div>
			</div>
			<div id="chat_type">
				<div style="border: 1px solid rgb(250,250,250); border-top:none;">
					<textarea id="message_text" name="text" style="width: 100%; height: 48px; border: none; margin: 0px; padding: 0px;"></textarea>
				</div>
			</div>
			<h1 id="test_test"></h1>
			<script type="text/javascript">
				var firstMessage = -1;
				var currentMessage = -1;
				var chat_engine = null;
				var chat_old_engine = null;
				var chat_messages_obj = $('#chat_messages');
				var messages_obj = $('#messages');
				var messages_inner_obj = $('#messages_inner');
				var new_message_obj = $('#new_message');
				var message_loading_obj = $('#message_loading');
				var chat_tmp_obj = $('#chat_tmp');
				var latest_poster = null;
				
				function loadOldMessage(){
					if(firstMessage == -1) return ;
					if(chat_old_engine != null) return ;
					message_loading_obj.fadeIn('fast');
					chat_old_engine = $.ajax({
						url : 'chat_load_back.php',
						type : 'post',
						data : 'first='+firstMessage,
						dataType : 'json',
						success : function(message){
							message_loading_obj.fadeOut('fast');
							if(message.length == 0){}
							else {
								firstMessage = message[0]['chat_id'];
								chat_tmp_obj.html('');
								for(i = 0; i < message.length; i++){
									chat_tmp_obj.append('<div id="message_'+message[i]['chat_id']+'" class="message"><h4>'+message[i]['user_display']+' <span class="time">'+message[i]['time']+'</span></h4><p>'+message[i]['text']+'</p></div>');
								}
								messages_obj.scrollTop(chat_tmp_obj.height());
								messages_inner_obj.prepend(chat_tmp_obj.html());
							}
							chat_old_engine = null;
						}
					});
				}
				function reloadMessage(scroll){
					if(chat_engine != null) 
						if(scroll == false) return ;
						else {
							chat_engine.abort();
							chat_engine = null;
						}
					chat_engine = $.ajax({
						url : 'chat_load.php',
						type : 'post',
						data : 'last='+currentMessage,
						dataType : 'json',
						success : function (message){
							if(message.length == 0) {}
							else {
								if(firstMessage == -1) {
									firstMessage = message[0]['chat_id'];
								}
								currentMessage = message[message.length-1]['chat_id'];
								if(messages_inner_obj.height() > chat_messages_obj.height()) new_message_obj.fadeIn('fast');
								for(i = 0; i < message.length; i++){
									var post_header = '<h4>'+message[i]['user_display']+' <span class="time">'+message[i]['time']+'</span></h4>';
									var add = '';
									if(latest_poster != message[i]['user_display']){
										latest_poster = message[i]['user_display'];
										add = post_header;
									}
									messages_inner_obj.append('<div id="message_'+message[i]['chat_id']+'" class="message">'+add+'<p>'+message[i]['text']+'</p></div>');
								}
								if(scroll){
									messages_obj.animate({scrollTop: messages_inner_obj.height()}, 'fast');
								}
							}
							chat_engine = null;
						}
					});
				}
				
				reloadMessage(true);
				setInterval(function (){reloadMessage(false);}, <?=$config['chat_reload_time']?>);
				
				messages_obj.scroll(function () {
					if(messages_obj.scrollTop() + chat_messages_obj.height() == messages_inner_obj.height()){
						new_message_obj.fadeOut('fast');
					}
					if(messages_obj.scrollTop() == 0){
						loadOldMessage();
					}
				}); 
				
				$('#message_text').keydown(function (e){
					var code = (e.keyCode ? e.keyCode : e.which);
					if(code == 13 && !e.shiftKey){
						//SUBMIT
						$(this).attr('disabled', 'disabled');
						$.ajax({
							url : 'chat_post.php',
							type : 'post',
							data : 'text='+$(this).val().replace(/\n/g, '<br>'),
							success : function (html){
								$('#message_text').removeAttr('disabled').val('');
								reloadMessage(true);
							}
						});
					}
				});
			
			</script>
		</div>
	</div>
</div>
<div id="content" style="background: rgb(250,250,250); padding-bottom: 20px;">
<div>