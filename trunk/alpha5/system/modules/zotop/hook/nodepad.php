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
?>