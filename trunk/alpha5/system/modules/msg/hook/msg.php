<?php
zotop::add('zotop.main.action','mymsg');

function mymsg()
{
	echo '<div>短消息：<a href="#">未读 3条</a> <a href="#">待处理 5条</a></div>';
}
?>