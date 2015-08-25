<?php
zotop::add('zotop.main.side','zotop_notepad');
zotop::add('zotop.main.main','zotop_favorite_main');
zotop::add('zotop.index.quickbar','zotop_index_quickbar');
zotop::add('zotop.main.main','zotop_log');
zotop::add('system.shutdown','zotop_logsave');

function zotop_notepad()
{
	block::header(array(
		'title'=>'记事本',
		'action'=>'<a class="dialog" href="'.zotop::url('zotop/notepad/add').'">新建记事</a>|<a class="more" href="'.zotop::url('zotop/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

function zotop_favorite_main()
{
	block::header(array(
		'title'=>'我的收藏夹',
		'action'=>'<a class="dialog" href="'.zotop::url('zotop/quick/add').'">管理</a>|<a class="more" href="'.zotop::url('zotop/notepad').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

function zotop_index_quickbar()
{
	echo '<a href="'.zotop::url('zotop/setting').'" target="mainIframe">系统设置</a> <b>|</b> ';
}

function zotop_log()
{
	block::header(array(
		'title'=>'日志记录',
		'action'=>'<a class="more" href="'.zotop::url('zotop/log').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	block::footer();
}

function zotop_logsave()
{
	zotop::data('mylog',zotop::$logs);
}

?>