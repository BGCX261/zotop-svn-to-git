<?php

zotop::add('zotop.main.side','notepad');

function notepad()
{
	block::header(array(
		'title'=>'记事本',
		'action'=>'<a class="dialog" href="'.zotop::url('zotop/notepad/add').'">新建记事</a>|<a class="more" href="'.zotop::url('zotop/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

zotop::add('zotop.main.main','favorite');

function favorite()
{
	block::header(array(
		'title'=>'我的收藏夹',
		'action'=>'<a class="dialog" href="'.zotop::url('zotop/quick/add').'">管理</a>|<a class="more" href="'.zotop::url('zotop/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

zotop::add('zotop.main.main','mylog');

function mylog()
{
	block::header(array(
		'title'=>'日志记录',
		'action'=>'<a class="more" href="'.zotop::url('zotop/log').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

zotop::add('zotop.main.action','mymsg');

function mymsg()
{
	echo '<div>短消息：<a href="#">未读 3条</a> <a href="#">待处理 5条</a></div>';
}

zotop::add('zotop.main.action','content');

function content()
{
	echo '<div>内容：<a href="#">待审核 3条</a> <a href="#">垃圾箱 5条</a></div>';
}

?>