<?php
zotop::add('zotop.main.action','content');
zotop::add('zotop.index.navbar','navbar_content');
function content()
{
	echo '<div>内容：<a href="#">待审核 3条</a> <a href="#">垃圾箱 5条</a></div>';
}
function navbar_content()
{
	echo '<li><a href="'.zotop::url('content').'" target="mainIframe"><span>内容管理</span></a></li>';
}
?>