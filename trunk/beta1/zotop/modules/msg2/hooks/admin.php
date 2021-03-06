<?php
zotop::add('system.main.action','msg_unread');
zotop::add('system.useraction','msg_useraction');

function msg_unread()
{
	echo '<div>短消息：<a href="#">未读 3条</a> <a href="#">待处理 5条</a></div>';
}

function msg_useraction()
{
	?>
	<span id="msg">
		<a href="<?php echo zotop::url('msg/list') ?>" target="mainIframe">短消息</a>
		<span id="msg-unread">
			<a href="<?php echo zotop::url('msg/list/unread') ?>" target="mainIframe"><span id="msg-unread-num">0</span>条未读</a>
		</span>
		<b>|</b>
	</span>
	<script>
	//获取未读消息数目
	function getUnreadMsg(){
		var url = "<?php echo zotop::url('msg/list/unread') ?>";
		$.get(url,'',function(msg){
			msg.num = parseInt(msg.num);			
			$('#msg-unread-num').html(msg.num);
		},'json');
	};
	//定时获取未读短消息数目
	(function(){		
		setInterval(getUnreadMsg,10000);
	})();
	</script>
	<?php
}
?>