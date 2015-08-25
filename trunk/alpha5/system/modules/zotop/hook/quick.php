<?php
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
?>