<?php
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
?>