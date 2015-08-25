<?php
zotop::add('zotop.system.side.tools','system_side_tool_database');

function system_side_tool_database()
{
	echo '<li><a href="'.zotop::url('database/manage/bakup').'" target="mainIframe">数据库备份及还原</a></li>';
}
?>